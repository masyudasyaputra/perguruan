<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    // app/Models/Province.php
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // Relasi "Has Many Through" jika ingin langsung akses dojo dari provinsi
    public function dojos()
    {
        return $this->hasManyThrough(Dojo::class, City::class);
    }
}
