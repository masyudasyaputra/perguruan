<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['province_id', 'name'];

    // Ini yang kurang! Tambahkan ini agar tidak error
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function dojos()
    {
        return $this->hasMany(Dojo::class);
    }
}