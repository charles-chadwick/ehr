<?php

namespace App\Enums;

enum PatientStatus: string
{
    case Active      = 'Active';
    case Inactive    = 'Inactive';
    case Prospective = 'Prospective';
}
