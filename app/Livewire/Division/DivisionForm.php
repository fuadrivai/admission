<?php

namespace App\Livewire\Division;

use App\Services\DivisionService;
use Livewire\Component;

class DivisionForm extends Component
{
    public $divisionId;
    public $division;
    public $name;
    private DivisionService $divisionService;

    public function boot(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }

    public function mount($division = null)
    {
        if ($division) {
            $this->division = $division;
            $this->name = $division->name;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->division) {
            $this->divisionService->put($this->division);
        } else {
            $this->divisionService->post($this->division);
        }

        $this->dispatch('divisionSaved');
        $this->dispatch('close-modal');
    }
    public function render()
    {
        return view('livewire.division.division-form');
    }
}
