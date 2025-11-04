<?php

namespace App\Livewire\Division;

use App\Services\DivisionService;
use Livewire\Component;
use Livewire\WithPagination;

class DivisionIndex extends Component
{
    use WithPagination;

    public $divisionId, $name, $modalTitle;
    protected $listeners = ['openModal' => 'openModal'];

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    private DivisionService $divisionService;

    public function boot(DivisionService $divisionService)
    {
        $this->divisionService = $divisionService;
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['divisionId', 'name']);

        if ($id) {
            $division = $this->divisionService->show($id);
            $this->divisionId = $division->id;
            $this->name = $division->name;
            $this->modalTitle = 'Edit Division';
        } else {
            $this->modalTitle = 'Add Division';
        }

        // gunakan Livewire v3 style dispatch
        $this->dispatch('show-modal')->toBrowser();
    }

    public function save()
    {
        $this->validate();

        if ($this->divisionId) {
            $this->divisionService->update($this->divisionId, ['name' => $this->name]);
            session()->flash('message', 'Division updated successfully.');
        } else {
            $this->divisionService->store(['name' => $this->name]);
            session()->flash('message', 'Division created successfully.');
        }

        $this->dispatch('hide-modal')->toBrowser();
        $this->reset(['divisionId', 'name']);
    }

    public function render()
    {
        $divisions = $this->divisionService->paginate(10);

        return view('livewire.division.division-index', [
            'divisions' => $divisions
        ])->extends('main-layout.index')
          ->section('content-child');
    }
}
