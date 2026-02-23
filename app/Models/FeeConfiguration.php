<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'belt_level_id',
        'amount',
    ];

    // Relasi ke Provinsi
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // Relasi ke Tingkatan Sabuk
    public function beltLevel()
    {
        return $this->belongsTo(BeltLevel::class);
    }
}