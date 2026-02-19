<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeltLevel extends Model
{
   protected $fillable = ['name', 'membership_fee'];

public function users()
{
    return $this->hasMany(User::class);
}
}
