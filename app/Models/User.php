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

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function hasRole(string|array $roles): bool
    {
        $userRoles = $this->allRoles();

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (in_array(strtolower(trim($role)), $userRoles, true)) {
                    return true;
                }
            }

            return false;
        }

        return in_array(strtolower(trim($roles)), $userRoles, true);
    }

    public function allRoles(): array
    {
        $roles = is_array($this->roles) ? $this->roles : [];
        $roles[] = $this->role;

        return collect($roles)
            ->filter()
            ->map(fn ($role) => strtolower(trim((string) $role)))
            ->unique()
            ->values()
            ->all();
    }

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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function assignedExamsAsExaminer()
    {
        return $this->belongsToMany(Exam::class, 'exam_examiners', 'user_id', 'exam_id')
            ->withTimestamps();
    }

    public function beltHistories()
    {
        return $this->hasMany(BeltHistory::class);
    }

    public function currentBelt()
    {
        return $this->belongsTo(BeltLevel::class, 'current_belt_level_id');
    }

    public function getIsActiveStatusAttribute(): bool
    {
        return (bool) $this->is_active;
    }
}