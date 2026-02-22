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

    // RELASI (Hanya tulis satu kali saja per fungsi)
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
}