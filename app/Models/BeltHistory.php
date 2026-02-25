<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeltHistory extends Model
{
    protected $fillable = [
        'user_id',
        'belt_level_id',
        'exam_id',
        'achieved_at',
        'description'
    ];
}