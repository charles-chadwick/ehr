<?php
/** @noinspection ALL */

namespace App\Models\Traits;

trait IsPerson {

    public function getFullNameAttribute() : string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameExtendedAttribute() : string
    {
        return trim("{$this->prefix} {$this->first_name} {$this->last_name} {$this->suffix}");
    }
}