<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Confirmed   = 'Confirmed';
    case Cancelled   = 'Cancelled';
    case Rescheduled = 'Rescheduled';
    case Pending     = 'Pending';
    case Completed   = 'Completed';
    case NoShow      = 'No Show';
}
