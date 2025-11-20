<?php

namespace App\Livewire;

use App\Models\DataUpload;
use App\Models\Institucion;
use App\Models\User;
use App\Services\UploadAnalyzer;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    protected UploadAnalyzer $analyzer;
    protected bool $isAdmin = false;
    protected ?int $scopedInstitutionId = null;
    protected bool $canManageUsers = false;

    public function boot(UploadAnalyzer $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    public function render()
    {
        $user = auth()->user();
        $this->isAdmin = $user?->isAdmin() ?? false;
        $this->scopedInstitutionId = $this->isAdmin ? null : ($user->institucion_id ?? null);
        if (!$this->isAdmin && !$this->scopedInstitutionId) {
            $this->scopedInstitutionId = null;
        }
        $this->canManageUsers = $user?->canManageUsers() ?? false;

        $stats = [
            'usuarios' => User::when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))->count(),
            'instituciones' => $this->isAdmin ? Institucion::count() : ($this->scopedInstitutionId ? 1 : Institucion::count()),
            'cargas' => DataUpload::when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))->count(),
            'ia_ready' => DataUpload::when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))
                ->whereIn('tipo', ['notas', 'inscripciones'])
                ->count(),
        ];

        $uploadsByTipo = DataUpload::select('tipo')
            ->selectRaw('COUNT(*) as total')
            ->when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $recentUploads = DataUpload::with('institucion')
            ->when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))
            ->latest()
            ->take(6)
            ->get();

        $latestNotesMetrics = $this->notesMetrics();
        $enrollmentProjection = $this->enrollmentProjection();

        $quickActions = [
            [
                'label' => 'Ver rendimiento académico',
                'description' => 'Promedios, aprobados y brechas',
                'icon' => 'fa-chart-line',
                'route' => route('prediccion'),
                'color' => 'from-blue-500 to-cyan-500',
            ],
            $this->canManageUsers ? [
                'label' => 'Gestionar usuarios',
                'description' => 'Altas, roles y accesos',
                'icon' => 'fa-user-shield',
                'route' => route('usuarios'),
                'color' => 'from-indigo-500 to-blue-500',
            ] : null,
            [
                'label' => 'Cargar nuevos archivos',
                'description' => 'Notas, asistencia, inscripciones',
                'icon' => 'fa-cloud-upload-alt',
                'route' => route('inscripciones'),
                'color' => 'from-emerald-500 to-teal-500',
            ],
            [
                'label' => 'Proyección de matrícula',
                'description' => 'Predice cupos y tendencia',
                'icon' => 'fa-chart-area',
                'route' => route('prediccion') . '#inscripciones',
                'color' => 'from-orange-500 to-amber-500',
            ],
        ];
        $quickActions = array_values(array_filter($quickActions));

        return view('livewire.dashboard', [
            'stats' => $stats,
            'uploadsByTipo' => $uploadsByTipo,
            'recentUploads' => $recentUploads,
            'latestNotesMetrics' => $latestNotesMetrics,
            'enrollmentProjection' => $enrollmentProjection,
            'quickActions' => $quickActions,
        ]);
    }

    private function notesMetrics(): ?array
    {
        $latestNotes = DataUpload::where('tipo', 'notas')
            ->when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))
            ->latest()
            ->first();

        if (!$latestNotes) {
            return null;
        }

        $rows = $this->analyzer->structuredRows($latestNotes)->filter(function ($row) use ($latestNotes) {
            return strtoupper($row['grado'] ?? $latestNotes->grado) === strtoupper($latestNotes->grado)
                && strtoupper($row['seccion'] ?? $latestNotes->seccion) === strtoupper($latestNotes->seccion);
        });

        if ($rows->isEmpty()) {
            return null;
        }

        $metrics = $this->analyzer->notesMetrics($rows);
        $metrics['grado'] = $latestNotes->grado;
        $metrics['seccion'] = $latestNotes->seccion;
        $metrics['institucion'] = $latestNotes->institucion?->nombre;

        return $metrics;
    }

    private function enrollmentProjection(): ?array
    {
        $enrollmentUploads = DataUpload::where('tipo', 'inscripciones')
            ->when($this->scopedInstitutionId, fn ($q) => $q->where('institucion_id', $this->scopedInstitutionId))
            ->latest()
            ->take(5)
            ->get();
        if ($enrollmentUploads->isEmpty()) {
            return null;
        }

        $rows = new Collection();
        foreach ($enrollmentUploads as $upload) {
            $rows = $rows->merge($this->analyzer->structuredRows($upload));
        }

        if ($rows->isEmpty()) {
            return null;
        }

        return $this->analyzer->enrollmentProjection($rows);
    }
}
