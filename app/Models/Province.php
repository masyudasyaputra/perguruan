<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'name',
        'leader_name',
        'sk_number',
        'sk_expiry_date'
    ];
    // app/Models/Province.php
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function officials()
    {
        // Relasi ke tabel officials berdasarkan province_id
        return $this->hasMany(Official::class, 'province_id');
    }

    // Relasi "Has Many Through" jika ingin langsung akses dojo dari provinsi
    public function dojos()
    {
        return $this->hasManyThrough(Dojo::class, City::class);
    }
}
