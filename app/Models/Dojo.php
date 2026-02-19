<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dojo extends Model
{
    protected $fillable = [
        'name',
        'sk_number',
        'sk_expiry_date',
        'city_id',
        'address',
        'phone_number',
        'sensei_name'
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
}