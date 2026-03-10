<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BeltLevel;
use App\Models\Province;
use App\Models\City;
use App\Models\Payment;
use App\Services\Payment\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function __construct(
        protected DokuService $doku
    ) {
    }

    /**
     * FORM: Pendaftaran kolektif
     */
    public function create()
    {
        $user = auth()->user();
        $role = strtolower((string) $user->role);

        $beltLevels = BeltLevel::orderBy('order')->get();

        $provinces = Province::query()
            ->when($role !== 'pb', function ($q) use ($user) {
                return $q->where('id', $user->province_id);
            })
            ->orderBy('name')
            ->get();

        $cities = City::query()
            ->when($role === 'pengprov', function ($q) use ($user) {
                return $q->where('province_id', $user->province_id);
            })
            ->when($role === 'pengcab', function ($q) use ($user) {
                return $q->where('id', $user->city_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.members.create', compact('provinces', 'beltLevels', 'cities', 'role'));
    }

    /**
     * REVIEW: halaman konfirmasi sebelum bayar
     * POST only
     */
    public function review(Request $request)
    {
        $admin = auth()->user();
        $role = strtolower((string) $admin->role);

        if ($request->isMethod('get')) {
            return redirect()->route('admin.members.create')
                ->withErrors(['review' => 'Silakan isi form pendaftaran terlebih dahulu.']);
        }

        $validated = $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.parent_name' => ['nullable', 'string', 'max:255'],
            'members.*.whatsapp' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s()-]{8,30}$/'],
            'members.*.belt_level_id' => ['required', 'integer', 'exists:belt_levels,id'],
            'members.*.email' => ['nullable', 'email', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
        ], [
            'members.*.whatsapp.regex' => 'Format WhatsApp tidak valid.',
        ]);

        $member_data = $validated['members'];

        $provinceId = $this->resolveProvinceId($admin, $role, (int) ($validated['province_id'] ?? 0));
        if ($provinceId <= 0) {
            return redirect()->route('admin.members.create')
                ->withErrors(['province_id' => 'Province belum di-set pada akun admin / dojo.']);
        }

        // IMPORTANT: fee map key biasanya INT, kita normalisasi biar aman di akses (int) key
        $beltFees = DB::table('fee_configurations')
            ->where('province_id', $provinceId)
            ->pluck('amount', 'belt_level_id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        $beltIds = collect($member_data)->pluck('belt_level_id')->map(fn($v) => (int) $v)->unique()->values()->all();
        $beltMap = BeltLevel::whereIn('id', $beltIds)->get()->keyBy('id');

        foreach ($member_data as $i => $item) {
            $beltId = (int) $item['belt_level_id'];
            $belt = $beltMap->get($beltId);
            $unit = (int) ($beltFees[$beltId] ?? 0);

            if ($unit <= 0) {
                return redirect()->route('admin.members.create')
                    ->withErrors(['fee' => "Biaya iuran belum dikonfigurasi untuk sabuk yang dipilih (province_id={$provinceId})."]);
            }

            $member_data[$i]['belt_name'] = $belt
                ? strtoupper($belt->name) . ' (' . $belt->kyu_dan . ')'
                : 'N/A';

            $member_data[$i]['unit_fee'] = $unit;

            // pastikan field opsional ada biar gampang dibuat hidden input di review blade
            $member_data[$i]['email'] = $item['email'] ?? '';
            $member_data[$i]['parent_name'] = $item['parent_name'] ?? '';
        }

        $admin_fee = (int) config('services.membership.admin_fee', 0);
        $subtotal = array_sum(array_map(fn($m) => (int) ($m['unit_fee'] ?? 0), $member_data));
        $total = $subtotal + $admin_fee;

        // NOTE: $beltFees tidak wajib dipakai blade (bisa pakai unit_fee), tapi kita tetap kirim untuk debugging/tampilan jika perlu
        return view('admin.members.review', compact('member_data', 'beltFees', 'admin_fee', 'total', 'provinceId'));
    }

    /**
     * STORE: create member + payment (pending) + redirect ke DOKU
     * Ini dipanggil oleh tombol "Konfirmasi & Bayar Sekarang" di halaman review.
     */
    public function store(Request $request)
    {
        $admin = auth()->user();
        $role = strtolower((string) $admin->role);

        // guard: kalau akses store tanpa payload members, jangan balik ke previous (bisa lari ke create)
        if (!$request->has('members')) {
            return redirect()->route('admin.members.create')
                ->withErrors(['store' => 'Data member tidak ditemukan. Silakan ulangi dari form pendaftaran.']);
        }

        $validated = $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.parent_name' => ['nullable', 'string', 'max:255'],
            'members.*.whatsapp' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s()-]{8,30}$/'],
            'members.*.belt_level_id' => ['required', 'integer', 'exists:belt_levels,id'],
            'members.*.email' => ['nullable', 'email', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
        ], [
            'members.*.whatsapp.regex' => 'Format WhatsApp tidak valid.',
        ]);

        $provinceId = $this->resolveProvinceId($admin, $role, (int) ($validated['province_id'] ?? 0));
        if ($provinceId <= 0) {
            // FIX: jangan return back() (bisa balik ke create), arahkan ke create dengan pesan jelas
            return redirect()->route('admin.members.create')
                ->withErrors(['province_id' => 'Province belum di-set pada akun admin / dojo.']);
        }

        $feeMap = DB::table('fee_configurations')
            ->where('province_id', $provinceId)
            ->pluck('amount', 'belt_level_id')
            ->map(fn($v) => (int) $v)
            ->toArray();

        $adminFee = (int) config('services.membership.admin_fee', 0);

        $fullDojoName = $admin->dojo->name ?? 'DOJO';
        $firstWord = strtoupper(explode(' ', trim($fullDojoName))[0] ?? 'DOJO');
        $defaultPass = $firstWord . '123';

        try {
            $payment = DB::transaction(function () use ($validated, $admin, $provinceId, $feeMap, $adminFee, $defaultPass) {
                $total = 0;
                $paidForMembers = [];

                foreach ($validated['members'] as $idx => $data) {
                    $beltLevelId = (int) $data['belt_level_id'];

                    $unit = (int) ($feeMap[$beltLevelId] ?? 0);
                    if ($unit <= 0) {
                        throw new \RuntimeException("Biaya iuran belum di-set untuk belt_level_id={$beltLevelId} (province_id={$provinceId}).");
                    }

                    // Email unik fallback (hindari duplicate)
                    $email = !empty($data['email'])
                        ? $data['email']
                        : (preg_replace('/[^0-9]/', '', $data['whatsapp']) . '.' . strtolower(Str::random(4)) . '@perguruan.local');

                    // OPTIONAL: cegah WhatsApp double di database (kalau mau strict)
                    // if (User::where('whatsapp', $data['whatsapp'])->exists()) {
                    //     throw new \RuntimeException("WhatsApp {$data['whatsapp']} sudah terdaftar.");
                    // }

                    $member = User::create([
                        'name' => $data['name'],
                        'parent_name' => $data['parent_name'] ?? null,
                        'email' => $email,
                        'whatsapp' => $data['whatsapp'],
                        'password' => Hash::make($defaultPass),
                        'role' => 'member',
                        'is_active' => false,
                        'province_id' => $provinceId,
                        'city_id' => $admin->city_id,
                        'dojo_id' => $admin->dojo_id,
                        'belt_level_id' => $beltLevelId,
                    ]);

                    $total += $unit;

                    $paidForMembers[] = [
                        'user_id' => $member->id,
                        'name' => $member->name,
                        'whatsapp' => $member->whatsapp,
                        'belt_level_id' => $beltLevelId,
                        'amount' => $unit,
                    ];
                }

                $total += $adminFee;

                return Payment::create([
                    'user_id' => $admin->id,
                    'type' => 'iuran',
                    'reference' => 'IURAN:BULK:' . now()->format('Y-m') . ':PAYER:' . $admin->id,
                    'invoice_number' => 'COLL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'amount' => $total,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes((int) config('services.doku.expire_minutes', 60)),
                    'meta' => [
                        'kind' => 'collective_membership',
                        'province_id' => $provinceId,
                        'dojo_id' => $admin->dojo_id,
                        'admin_fee' => $adminFee,
                        'count' => count($paidForMembers),
                        'payer' => [
                            'id' => $admin->id,
                            'name' => $admin->name,
                            'role' => $admin->role ?? null,
                        ],
                    ],
                    'payload' => [
                        'paid_for_members' => $paidForMembers,
                    ],
                ]);
            });

            $checkout = $this->doku->createCheckout($payment, [
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->whatsapp ?? null,
            ]);

            if (empty($checkout['payment_url'])) {
                return redirect()->route('admin.dashboard')
                    ->withErrors(['payment' => 'Member tersimpan, tapi gagal membuat payment URL dari DOKU.']);
            }

            $payment->update(['payment_url' => $checkout['payment_url']]);

            return redirect()->away($checkout['payment_url']);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('admin.members.create')
                ->withErrors(['store' => 'ERROR DB: ' . $e->getMessage()]);
        }
    }

    /**
     * Province resolver:
     * - PB: boleh override dari form
     * - lainnya: pakai admin->province_id, fallback ke admin->dojo->province_id
     */
    protected function resolveProvinceId($admin, string $role, int $overrideProvinceId = 0): int
    {
        $provinceId = (int) ($admin->province_id ?? 0);

        if ($role === 'pb') {
            $provinceId = (int) ($overrideProvinceId ?: ($admin->province_id ?? 0));
        } else {
            if ($provinceId <= 0) {
                $provinceId = (int) ($admin->dojo->province_id ?? 0);
            }
        }

        return $provinceId;
    }
}