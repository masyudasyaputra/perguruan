<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamScore extends Model
{
    protected $fillable = [
        'exam_id',
        'member_id',
        'examiner_id',
        'kihon',
        'kata',
        'kumite',
        'result',
        'new_belt_level_id', // WAJIB ADA AGAR DATA TERSIMPAN
    ];
}