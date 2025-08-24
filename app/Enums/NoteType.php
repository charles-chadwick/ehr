<?php

namespace App\Enums;

enum NoteType: string
{
    case Admin = 'Admin';
    case NeedToKnow = 'Need To Know';
    case MedicalHistory = 'Medical History';
}
