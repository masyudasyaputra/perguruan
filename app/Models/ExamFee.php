<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamFee extends Model
{
    protected $fillable = ['belt_level_id', 'amount'];

    public function beltLevel()
    {
        return $this->belongsTo(BeltLevel::class, 'belt_level_id');
    }
}
