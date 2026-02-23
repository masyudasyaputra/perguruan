<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeltLevel extends Model
{
   protected $fillable = [
    'name', 
    'kyu_dan', 
    'order', 
    'membership_fee' // Pastikan ini ada jika Anda memakainya di seeder
];

public function users()
{
    return $this->hasMany(User::class);
}
}
