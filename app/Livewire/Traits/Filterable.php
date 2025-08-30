<?php

namespace App\Livewire\Traits;

trait Filterable
{
    public string $filter_by = "";
    public string $filter_on = "";

    public function filter($on, $by) : void
    {
        $this->filter_by = $by;
        $this->filter_on = $on;
    }
}
