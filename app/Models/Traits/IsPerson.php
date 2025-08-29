<?php

/** @noinspection ALL */

namespace App\Models\Traits;

trait IsPerson
{
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameExtendedAttribute(): string
    {
        $name = "{$this->prefix} {$this->first_name}";
        if (isset($this->middle_name) && ! empty($this->middle_name)) {
            $name .= " {$this->middle_name}";
        }

        $name .= " {$this->last_name} {$this->suffix}";

        return trim($name);
    }
}
