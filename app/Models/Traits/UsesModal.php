<?php
/** @noinspection ALL */

namespace App\Models\Traits;
use Flux\Flux;

trait UsesModal {

    public string $modal = "";

    public function closeModal(array $except = []) : void
    {
        $except = array_merge($except, ['modal']);
        $this->resetExcept($except);
        Flux::modal($this->modal)
            ->close();
    }

}