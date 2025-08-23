<?php

namespace App\Livewire\Traits;

use Livewire\WithPagination;

trait Sortable {

    use WithPagination;

    public string $sort_by        = 'id'; // default to ID
    public string $sort_direction = 'desc'; // default to most recent first

    public function sort($column) : void
    {
        if ($this->sort_by === $column) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $column;
            $this->sort_direction = 'asc';
        }
    }

}
