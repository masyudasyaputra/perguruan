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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function __construct(
        protected DokuService $doku
    ) {
    }

    public function index()
    {
        return redirect()->route('admin.members.create');
    }

    public function create()
    {
        $user = auth()->user();
        $role = strtolower((string) $user->role);

        $beltLevels = BeltLevel::orderBy('id')->get();

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
            ->when(in_array($role, ['pengcab', 'admin_dojo']), function ($q) use ($user) {
                return $q->where('id', $user->city_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.members.create', compact('provinces', 'beltLevels', 'cities', 'role'));
    }

    public function review(Request $request)
    {
        $admin = auth()->user();
        $role = strtolower((string) $admin->role);

        if ($request->isMethod('get')) {
            return redirect()
                ->route('admin.members.create')
                ->withErrors([
                    'review' => 'Silakan isi form pendaftaran terlebih dahulu.',
                ]);
        }

        $validated = $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.parent_name' => ['nullable', 'string', 'max:255'],
            'members.*.whatsapp' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9+\s()-]{8,30}$/',
                'distinct',
                Rule::unique('users', 'whatsapp'),
            ],
            'members.*.belt_level_id' => ['required', 'integer', 'exists:belt_levels,id'],
            'members.*.email' => ['nullable', 'email', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
        ], [
            'members.*.whatsapp.regex' => 'Format WhatsApp tidak valid.',
            'members.*.whatsapp.distinct' => 'Nomor WhatsApp antar member dalam form tidak boleh sama.',
            'members.*.whatsapp.unique' => 'Nomor WhatsApp sudah terdaftar di sistem.',
        ]);

        $memberData = $validated['members'];

        $provinceId = $this->resolveProvinceId($admin, $role, (int) ($validated['province_id'] ?? 0));
        if ($provinceId <= 0) {
            return redirect()
                ->route('admin.members.create')
                ->withErrors([
                    'province_id' => 'Province belum di-set pada akun admin / dojo.',
                ]);
        }

        $beltFees = DB::table('fee_configurations')
            ->where('province_id', $provinceId)
            ->pluck('amount', 'belt_level_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();

        $beltIds = collect($memberData)
            ->pluck('belt_level_id')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        $beltMap = BeltLevel::whereIn('id', $beltIds)->get()->keyBy('id');
        $hasKyuDanColumn = Schema::hasColumn('belt_levels', 'kyu_dan');

        foreach ($memberData as $i => $item) {
            $beltId = (int) $item['belt_level_id'];
            $belt = $beltMap->get($beltId);
            $unit = (int) ($beltFees[$beltId] ?? 0);

            if ($unit <= 0) {
                return redirect()
                    ->route('admin.members.create')
                    ->withErrors([
                        'fee' => "Biaya iuran belum dikonfigurasi untuk sabuk yang dipilih (province_id={$provinceId}).",
                    ]);
            }

            $beltName = $belt ? strtoupper($belt->name) : 'N/A';

            if ($belt && $hasKyuDanColumn && !empty($belt->kyu_dan)) {
                $beltName .= ' (' . $belt->kyu_dan . ')';
            }

            $memberData[$i]['belt_name'] = $beltName;
            $memberData[$i]['unit_fee'] = $unit;
            $memberData[$i]['email'] = $item['email'] ?? '';
            $memberData[$i]['parent_name'] = $item['parent_name'] ?? '';
        }

        $adminFee = (int) config('services.membership.admin_fee', 0);
        $subtotal = array_sum(array_map(fn ($member) => (int) ($member['unit_fee'] ?? 0), $memberData));
        $total = $subtotal + $adminFee;

        return view('admin.members.review', [
            'member_data' => $memberData,
            'beltFees' => $beltFees,
            'admin_fee' => $adminFee,
            'total' => $total,
            'provinceId' => $provinceId,
        ]);
    }

    public function store(Request $request)
    {
        $admin = auth()->user();
        $role = strtolower((string) $admin->role);

        if (!$request->has('members')) {
            return redirect()
                ->route('admin.members.create')
                ->withErrors([
                    'store' => 'Data member tidak ditemukan. Silakan ulangi dari form pendaftaran.',
                ]);
        }

        $validated = $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.parent_name' => ['nullable', 'string', 'max:255'],
            'members.*.whatsapp' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9+\s()-]{8,30}$/',
                'distinct',
                Rule::unique('users', 'whatsapp'),
            ],
            'members.*.belt_level_id' => ['required', 'integer', 'exists:belt_levels,id'],
            'members.*.email' => ['nullable', 'email', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
        ], [
            'members.*.whatsapp.regex' => 'Format WhatsApp tidak valid.',
            'members.*.whatsapp.distinct' => 'Nomor WhatsApp antar member dalam form tidak boleh sama.',
            'members.*.whatsapp.unique' => 'Nomor WhatsApp sudah terdaftar di sistem.',
        ]);

        $provinceId = $this->resolveProvinceId($admin, $role, (int) ($validated['province_id'] ?? 0));
        if ($provinceId <= 0) {
            return redirect()
                ->route('admin.members.create')
                ->withErrors([
                    'province_id' => 'Province belum di-set pada akun admin / dojo.',
                ]);
        }

        $feeMap = DB::table('fee_configurations')
            ->where('province_id', $provinceId)
            ->pluck('amount', 'belt_level_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();

        $adminFee = (int) config('services.membership.admin_fee', 0);

        $fullDojoName = $admin->dojo->name ?? 'DOJO';
        $firstWord = strtoupper(explode(' ', trim($fullDojoName))[0] ?? 'DOJO');
        $defaultPass = $firstWord . '123';

        try {
            $payment = DB::transaction(function () use ($validated, $admin, $provinceId, $feeMap, $adminFee, $defaultPass) {
                $total = 0;
                $paidForMembers = [];

                foreach ($validated['members'] as $data) {
                    $beltLevelId = (int) $data['belt_level_id'];
                    $unit = (int) ($feeMap[$beltLevelId] ?? 0);

                    if ($unit <= 0) {
                        throw new \RuntimeException(
                            "Biaya iuran belum di-set untuk belt_level_id={$beltLevelId} (province_id={$provinceId})."
                        );
                    }

                    // fail-safe tambahan
                    if (User::where('whatsapp', $data['whatsapp'])->exists()) {
                        throw new \RuntimeException("Nomor WhatsApp {$data['whatsapp']} sudah terdaftar.");
                    }

                    $email = !empty($data['email'])
                        ? $data['email']
                        : preg_replace('/[^0-9]/', '', $data['whatsapp']) . '.' . strtolower(Str::random(4)) . '@perguruan.local';

                    if (User::where('email', $email)->exists()) {
                        $email = preg_replace('/[^0-9]/', '', $data['whatsapp']) . '.' . strtolower(Str::random(6)) . '@perguruan.local';
                    }

                    $member = User::create([
                        'name' => $data['name'],
                        'parent_name' => $data['parent_name'] ?? null,
                        'email' => $email,
                        'whatsapp' => $data['whatsapp'],
                        'password' => Hash::make($defaultPass),
                        'role' => 'member',
                        'roles' => ['member'],
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
                $expiresAt = now()->addMinutes((int) config('services.doku.expire_minutes', 60));

                return Payment::create([
                    'user_id' => $admin->id,
                    'type' => 'membership_fee',
                    'reference' => 'IURAN:BULK:' . now()->format('Y-m') . ':PAYER:' . $admin->id,
                    'invoice_number' => $this->makeInvoiceNumber(),
                    'amount' => $total,
                    'status' => 'pending',
                    'expires_at' => $expiresAt,
                    'expired_at' => $expiresAt,
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
                    'callback_payload' => [
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
                return redirect()
                    ->route('admin.dashboard')
                    ->withErrors([
                        'payment' => $checkout['message'] ?? 'Member tersimpan, tetapi gagal membuat payment URL dari DOKU.',
                    ]);
            }

            $payment->update([
                'payment_url' => $checkout['payment_url'],
            ]);

            return redirect()->away($checkout['payment_url']);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.members.create')
                ->withErrors([
                    'store' => 'ERROR DB / DOKU: ' . $e->getMessage(),
                ]);
        }
    }

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

    protected function makeInvoiceNumber(): string
    {
        return 'INK' . now()->format('ymdHis') . strtoupper(Str::random(4));
    }
}