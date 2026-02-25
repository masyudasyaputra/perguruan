<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',

        // Role utama (primary)
        'role',

        // Multi-role tambahan (JSON)
        'roles',

        'is_active',
        'province_id',
        'city_id',
        'dojo_id',
        'belt_level_id',
        'whatsapp',
        'parent_name',
        'expired_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'expired_at' => 'datetime',
            'roles' => 'array',
        ];
    }

    /**
     * Mengecek apakah user punya role tertentu.
     * - Support: $user->hasRole('penguji')
     * - Support: $user->hasRole(['pb','pengprov'])
     * - Support multi-role via kolom JSON roles + kolom role (primary)
     */
    public function hasRole(string|array $roles): bool
    {
        $userRoles = $this->allRoles();

        if (is_array($roles)) {
            foreach ($roles as $r) {
                if (in_array($r, $userRoles, true))
                    return true;
            }
            return false;
        }

        return in_array($roles, $userRoles, true);
    }

    /**
     * Ambil semua role user (gabungan role utama + roles tambahan),
     * tanpa duplikasi dan tanpa nilai kosong.
     */
    public function allRoles(): array
    {
        $roles = is_array($this->roles) ? $this->roles : [];
        $roles[] = $this->role;

        return array_values(array_unique(array_filter($roles)));
    }

    // --- RELASI ---

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function dojo()
    {
        return $this->belongsTo(Dojo::class);
    }

    public function beltLevel()
    {
        return $this->belongsTo(BeltLevel::class);
    }

    /**
     * Jika Anda memiliki tabel payments, pastikan relasi ini ada.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function assignedExamsAsExaminer()
    {
        return $this->belongsToMany(Exam::class, 'exam_examiners', 'user_id', 'exam_id')
            ->withTimestamps();
    }

    // --- ATTRIBUTES ---

    /**
     * Status aktif berdasarkan pembayaran SUCCESS untuk belt yang sedang disandang.
     * Catatan: Pastikan status di DB memang 'SUCCESS' (bukan 'paid' / 'PAID' dll).
     */
    public function getIsActiveStatusAttribute(): bool
    {
        return $this->payments()
            ->where('belt_level_id', $this->belt_level_id)
            ->where('status', 'SUCCESS')
            ->exists();
    }

    // Relasi ke histori sabuk
    public function beltHistories()
    {
        return $this->hasMany(BeltHistory::class);
    }

    // Helper untuk mengambil sabuk terbaru
    public function currentBelt()
    {
        return $this->belongsTo(BeltLevel::class, 'current_belt_level_id');
    }
}