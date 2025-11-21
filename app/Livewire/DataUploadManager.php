<?php

namespace App\Livewire;

use App\Models\DataUpload;
use App\Models\Institucion;
use App\Services\UploadAnalyzer;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class DataUploadManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    protected $listeners = [
        'upload-completed' => '$refresh',
    ];

    public string $tipo = 'inscripciones';
    public string $titulo = 'Inscripciones';
    public bool $mostrarEvaluacion = false;

    public $institucion_id = null;
    public $grado = '';
    public $seccion = '';
    public $evaluacion_tipo = '';

    public $grados = ['1°', '2°', '3°', '4°', '5°', '6°'];
    public $secciones = ['A', 'B', 'C', 'D'];

    public bool $showPreview = false;
    public array $previewMeta = [];
    public ?string $previewFile = null;
    public array $previewTable = [];
    public ?string $previewMessage = null;

    public bool $showEditModal = false;
    public ?int $editingId = null;
    public string $editingTipo = '';
    public array $editForm = [
        'institucion_id' => '',
        'grado' => '',
        'seccion' => '',
        'evaluacion_tipo' => '',
    ];
    
    public bool $showDeleteModal = false;
    public ?int $uploadToDeleteId = null;
    public string $uploadToDeleteLabel = '';

    protected bool $isAdmin = false;
    protected ?int $lockedInstitutionId = null;
    public bool $canSelectInstitution = false;
    public bool $institutionMissing = false;

    protected UploadAnalyzer $analyzer;

    public function boot(UploadAnalyzer $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    public function mount(string $tipo = 'inscripciones'): void
    {
        $user = auth()->user();
        abort_unless($user, 401);

        $this->isAdmin = $user->isAdmin();
        $this->lockedInstitutionId = $this->isAdmin ? null : $user->institucion_id;
        $this->canSelectInstitution = $this->isAdmin;

        if (!$this->isAdmin) {
            if (!$this->lockedInstitutionId) {
                $this->institutionMissing = true;
            } else {
                $this->institucion_id = $this->lockedInstitutionId;
            }
        }

        $this->tipo = $tipo;
        $map = [
            'inscripciones' => 'Inscripciones',
            'asistencias' => 'Asistencias',
            'notas' => 'Notas',
        ];
        $this->titulo = $map[$tipo] ?? ucfirst($tipo);
        $this->mostrarEvaluacion = $tipo === 'notas';
    }

    public function loadPreview(int $uploadId): void
    {
        $upload = $this->loadUpload($uploadId);
        $table = $this->analyzer->extractTable($upload, 25);

        $this->previewMeta = [
            'institucion' => $upload->institucion->nombre ?? '—',
            'tipo' => $upload->tipo,
            'grado' => $upload->grado,
            'seccion' => $upload->seccion,
            'evaluacion' => $upload->evaluacion_tipo,
            'formato' => $upload->formato,
        ];
        $this->previewFile = $upload->archivo;
        $this->previewTable = [
            'headers' => $table['headers'],
            'rows' => $table['rows'],
        ];
        $this->previewMessage = $table['message'];
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->previewMeta = [];
        $this->previewFile = null;
        $this->previewTable = [];
        $this->previewMessage = null;
    }

    public function startEdit(int $uploadId): void
    {
        $upload = $this->loadUpload($uploadId);
        $this->editingId = $upload->id;
        $this->editingTipo = $upload->tipo;
        $this->editForm = [
            'institucion_id' => $upload->institucion_id,
            'grado' => $upload->grado,
            'seccion' => $upload->seccion,
            'evaluacion_tipo' => $upload->evaluacion_tipo,
        ];
        $this->showEditModal = true;
    }

    public function updateUpload(): void
    {
        if (!$this->editingId) {
            return;
        }

        $rules = [
            'editForm.institucion_id' => ['required', 'exists:instituciones,id'],
            'editForm.grado' => ['required', 'string'],
            'editForm.seccion' => ['required', 'string'],
        ];

        if ($this->editingTipo === 'notas') {
            $rules['editForm.evaluacion_tipo'] = ['nullable', 'in:parcial,tarea,examen,practica,proyecto,otro'];
        }

        $validated = $this->validate($rules);
        $data = $validated['editForm'];

        if (!$this->canSelectInstitution) {
            $data['institucion_id'] = $this->lockedInstitutionId;
        }

        DataUpload::whereKey($this->editingId)->update([
            'institucion_id' => $data['institucion_id'],
            'grado' => mb_strtoupper($data['grado']),
            'seccion' => mb_strtoupper($data['seccion']),
            'evaluacion_tipo' => $this->editingTipo === 'notas' ? $data['evaluacion_tipo'] : null,
        ]);

        $this->dispatch('swal', icon: 'success', title: 'Registro actualizado');
        $this->closeEditModal();
        $this->resetPage();
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingId = null;
        $this->editingTipo = '';
        $this->editForm = [
            'institucion_id' => '',
            'grado' => '',
            'seccion' => '',
            'evaluacion_tipo' => '',
        ];
    }

    public function confirmDelete(int $uploadId): void
    {
        $upload = $this->loadUpload($uploadId);
        $this->uploadToDeleteId = $uploadId;
        $this->uploadToDeleteLabel = "archivo de {$upload->tipo}";
        $this->showDeleteModal = true;
    }
    
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->uploadToDeleteId = null;
        $this->uploadToDeleteLabel = '';
    }

    public function deleteUpload(): void
    {
        if (!$this->uploadToDeleteId) {
            return;
        }

        $upload = $this->loadUpload($this->uploadToDeleteId, false);
        if (!$upload) {
            return;
        }

        try {
            if ($upload->archivo) {
                Storage::disk('public')->delete($upload->archivo);
            }

            $upload->delete();
            $this->showDeleteModal = false;
            $this->uploadToDeleteId = null;
            $this->uploadToDeleteLabel = '';
            $this->dispatch('swal', icon: 'success', title: 'Archivo eliminado');
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('swal', icon: 'error', title: 'Error al eliminar archivo', text: $e->getMessage());
        }
    }

    public function render()
    {
        $uploads = $this->institutionMissing
            ? collect([])
            : DataUpload::with('institucion')
                ->where('tipo', $this->tipo)
                ->when(!$this->isAdmin && $this->lockedInstitutionId, function ($query) {
                    $query->where('institucion_id', $this->lockedInstitutionId);
                })
                ->latest()
                ->paginate(5);

        $instituciones = $this->institutionMissing
            ? collect([])
            : Institucion::orderBy('nombre')
                ->when(!$this->isAdmin && $this->lockedInstitutionId, fn ($q) => $q->whereKey($this->lockedInstitutionId))
                ->get();

        return view('livewire.data-upload-manager', [
            'uploads' => $uploads,
            'instituciones' => $instituciones,
            'canSelectInstitution' => $this->canSelectInstitution,
            'lockedInstitutionId' => $this->lockedInstitutionId,
            'institutionMissing' => $this->institutionMissing,
        ]);
    }

    protected function loadUpload(int $id, bool $fail = true): ?DataUpload
    {
        $query = DataUpload::with('institucion')->whereKey($id);
        if (!$this->isAdmin && $this->lockedInstitutionId) {
            $query->where('institucion_id', $this->lockedInstitutionId);
        }
        return $fail ? $query->firstOrFail() : $query->first();
    }
}
