<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamScore extends Model
{
    protected $fillable = ['exam_id', 'member_id', 'examiner_id', 'kihon', 'kata', 'kumite', 'result'];
}