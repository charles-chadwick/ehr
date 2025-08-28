<?php

use Livewire\Volt\Component;

new class extends Component {

    public const DX_URL = "https://clinicaltables.nlm.nih.gov/api/icd10cm/v3/search?sf=code,name&terms=";
    public string $query   = "";
    public string $results = "";

    public function search() {

    }

}; ?>

<div>

</div>
