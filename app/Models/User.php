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
        'role',
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
        ];
    }

    /**
     * Helper untuk mengecek role user.
     * Mendukung string tunggal 'admin' atau array ['pb', 'pengprov'].
     */
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
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
     * Jika Anda memiliki tabel payments, pastikan relasi ini ada
     * agar getIsActiveStatusAttribute tidak error.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class); // Sesuaikan nama model Payment Anda
    }

    // --- ATTRIBUTES ---

    public function getIsActiveStatusAttribute()
    {
        // Cek apakah ada pembayaran SUCCESS untuk belt_level_id yang sedang disandang user
        return $this->payments()
            ->where('belt_level_id', $this->belt_level_id)
            ->where('status', 'SUCCESS') 
            ->exists();
    }
}