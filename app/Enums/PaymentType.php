<?php

namespace App\Enums;

enum PaymentType: string
{
    case Iuran = 'iuran';
    case Ujian = 'ujian';
}