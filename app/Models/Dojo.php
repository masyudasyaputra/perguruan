<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dojo extends Model
{
    protected $fillable = [
        'name',
        'province_id',
        'city_id',
        'address',
        'sensei_name',
        'phone_number',
        'sk_number',
        'sk_expiry_date'
    ];
    // Logika otomatis: Aktif jika SK belum expired
    public function getIsActiveAttribute()
    {
        if (!$this->sk_expiry_date)
            return false;
        return \Carbon\Carbon::parse($this->sk_expiry_date)->isFuture();
    }
    // Relasi ke Kota
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function members()
    {
        // Dojo memiliki banyak User melalui kolom dojo_id
        return $this->hasMany(User::class, 'dojo_id');
    }
}