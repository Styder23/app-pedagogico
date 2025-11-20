<?php

namespace App\Livewire;

use App\Models\Institucion;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InstitutionsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $showModal = false;
    public $modalTitle = 'Nueva institución';
    public $editingId = null;

    public $nombre = '';
    public $codigo_ugel = '';
    public $direccion = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->modalTitle = 'Registrar institución';
        $this->showModal = true;
    }

    public function openEditModal(int $institutionId): void
    {
        $institucion = Institucion::findOrFail($institutionId);
        $this->editingId = $institucion->id;
        $this->nombre = $institucion->nombre;
        $this->codigo_ugel = $institucion->codigo_ugel;
        $this->direccion = $institucion->direccion;
        $this->modalTitle = 'Editar institución';
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'codigo_ugel' => ['nullable', 'string', 'max:50', 'unique:instituciones,codigo_ugel,' . $this->editingId],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        Institucion::updateOrCreate(['id' => $this->editingId], $data);

        $this->dispatch('swal', icon: 'success', title: 'Institución guardada');
        $this->showModal = false;
        $this->resetForm();
    }

    #[On('delete-institution-confirmed')]
    public function delete(int $institutionId): void
    {
        Institucion::findOrFail($institutionId)->delete();
        $this->dispatch('swal', icon: 'success', title: 'Institución eliminada');
        $this->resetPage();
    }

    public function confirmDeletion(int $id): void
    {
        $this->dispatch('confirm-delete', [
            'id' => $id,
            'label' => 'institución',
            'event' => 'delete-institution-confirmed',
        ]);
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'nombre',
            'codigo_ugel',
            'direccion',
        ]);
    }

    public function render()
    {
        $instituciones = Institucion::when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('codigo_ugel', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(8);

    return view('livewire.institutions-manager', [
            'instituciones' => $instituciones,
        ]);
    }
}
