<?php

namespace App\Livewire;

use App\Models\DataUpload;
use App\Models\Institucion;
use App\Services\UploadAnalyzer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class PerformancePrediction extends Component
{
    public $institucion_id = '';
    public $grado = '';
    public $seccion = '';

    public array $grados = ['1°', '2°', '3°', '4°', '5°', '6°'];
    public array $secciones = ['A', 'B', 'C', 'D', 'E'];

    public ?array $resultado = null;
    public array $componentes = [];

    protected UploadAnalyzer $analyzer;

    protected $rules = [
        'institucion_id' => ['required', 'exists:instituciones,id'],
        'grado' => ['required', 'string'],
        'seccion' => ['required', 'string'],
    ];

    protected bool $isAdmin = false;
    protected ?int $lockedInstitutionId = null;
    public bool $canSelectInstitution = false;
    public bool $institutionMissing = false;

    public function boot(UploadAnalyzer $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

public function mount(): void
{
    $user = auth()->user();
    abort_unless($user, 401);
    
    // Determinar roles y permisos
    $this->isAdmin = $user->rol_id === 1;
    
    // Para roles que tienen institución asignada (2, 3, 6)
    if (in_array($user->rol_id, [2, 3, 6])) {
        $this->lockedInstitutionId = $user->institucion_id;
        $this->institutionMissing = !$this->lockedInstitutionId;
        $this->canSelectInstitution = false;
        
        // Pre-seleccionar la institución del usuario
        if ($this->lockedInstitutionId) {
            $this->institucion_id = (string)$this->lockedInstitutionId;
        }
    } else {
        // Para admin (rol 1) y otros roles sin institución fija
        $this->lockedInstitutionId = null;
        $this->institutionMissing = false;
        $this->canSelectInstitution = true;
    }
}

    public function generar(): void
    {
        if ($this->institutionMissing) {
            $this->dispatch('swal', icon: 'error', title: 'Sin institución', text: 'No puedes generar predicciones hasta asignar una institución.');
            return;
        }

        // Para roles no admin, forzar la institución asignada
        $user = auth()->user();
        if (!$this->isAdmin && $this->lockedInstitutionId) {
            $this->institucion_id = (string)$this->lockedInstitutionId;
        }

        $this->validate();

        // Verificación de seguridad para roles no admin
        if (!$this->isAdmin && $this->lockedInstitutionId) {
            $selectedInstitution = (int)$this->institucion_id;
            $userInstitution = (int)$this->lockedInstitutionId;
            
            if ($selectedInstitution !== $userInstitution) {
                abort(403, 'No tienes permisos para acceder a esta institución.');
            }
        }

        $grado = $this->normalizeGrade($this->grado);
        $seccion = $this->normalizeSection($this->seccion);

        $baseUploads = DataUpload::where('institucion_id', $this->institucion_id);

        $notesUploads = (clone $baseUploads)->where('tipo', 'notas')->latest()->take(5)->get();
        $attendanceUploads = (clone $baseUploads)->where('tipo', 'asistencias')->latest()->take(5)->get();
        $enrollmentUploads = (clone $baseUploads)->where('tipo', 'inscripciones')->latest()->take(5)->get();

        $notesRows = $this->collectRows($notesUploads, $grado, $seccion);
        $attendanceRows = $this->collectRows($attendanceUploads, $grado, $seccion);
        $enrollmentRows = $this->collectRows($enrollmentUploads, $grado, $seccion);

        $notesMetrics = $notesRows->isNotEmpty() ? $this->analyzer->notesMetrics($notesRows) : null;
        $attendanceSummary = $attendanceRows->isNotEmpty() ? $this->attendanceSummary($attendanceRows) : null;
        $enrollmentProjection = $enrollmentRows->isNotEmpty()
            ? $this->analyzer->enrollmentProjection($enrollmentRows)
            : null;

        $enrollmentChart = null;
        $enrollmentTrend = null;
        if ($enrollmentProjection) {
            $enrollmentChart = $this->buildEnrollmentChart($enrollmentProjection);
            $enrollmentTrend = $this->enrollmentTrend($enrollmentProjection['datos'], $enrollmentProjection['proyecciones']);
            $enrollmentProjection = [
                'datos' => ($enrollmentProjection['datos'] instanceof Collection) ? $enrollmentProjection['datos']->toArray() : $enrollmentProjection['datos'],
                'proyecciones' => $enrollmentProjection['proyecciones'],
            ];
        }

        $academicScore = $notesMetrics ? round(($notesMetrics['promedio'] / 20) * 100, 1) : 0;
        $attendanceScore = $attendanceSummary['tasa_asistencia'] ?? 0;
        $enrollmentScore = $enrollmentChart
            ? ($this->stabilityScore($enrollmentProjection['datos'] ?? []) ?? 60)
            : 0;

        $prediction = round(($academicScore * 0.6) + ($attendanceScore * 0.3) + ($enrollmentScore * 0.1));

        $this->resultado = [
            'prediction' => $prediction,
            'label' => $this->clasificar($prediction),
            'notes' => $notesMetrics,
            'attendance' => $attendanceSummary,
            'enrollment' => $enrollmentProjection,
            'enrollment_chart' => $enrollmentChart,
            'enrollment_trend' => $enrollmentTrend,
        ];

        $this->componentes = [
            [
                'label' => 'Rendimiento académico',
                'value' => $academicScore,
                'registros' => $notesRows->count(),
                'color' => 'bg-blue-50 text-blue-600',
            ],
            [
                'label' => 'Compromiso (asistencia)',
                'value' => $attendanceScore,
                'registros' => $attendanceRows->count(),
                'color' => 'bg-green-50 text-green-600',
            ],
            [
                'label' => 'Tendencia de matrícula',
                'value' => $enrollmentScore,
                'registros' => $enrollmentRows->count(),
                'color' => 'bg-amber-50 text-amber-600',
            ],
        ];
    }

    public function render()
    {
        $instituciones = $this->institutionMissing
            ? collect([])
            : Institucion::orderBy('nombre')
                ->when(!$this->isAdmin && $this->lockedInstitutionId, 
                    fn ($q) => $q->whereKey($this->lockedInstitutionId)
                )
                ->get();

        return view('livewire.performance-prediction', [
            'instituciones' => $instituciones,
            'canSelectInstitution' => $this->canSelectInstitution,
            'lockedInstitutionId' => $this->lockedInstitutionId,
            'institutionMissing' => $this->institutionMissing,
        ]);
    }

    protected function collectRows(Collection $uploads, string $grado, string $seccion): Collection
    {
        $rows = new Collection();
        foreach ($uploads as $upload) {
            if (!$this->isAdmin && $this->lockedInstitutionId && $upload->institucion_id !== $this->lockedInstitutionId) {
                continue;
            }

            $uploadGrade = $this->normalizeGrade($upload->grado);
            $uploadSection = $this->normalizeSection($upload->seccion);
            if ($uploadGrade !== $grado || $uploadSection !== $seccion) {
                continue;
            }

            $rows = $rows->merge(
                $this->analyzer->structuredRows($upload)->map(function ($row) use ($uploadGrade, $uploadSection) {
                    $row['grado'] = $uploadGrade;
                    $row['seccion'] = $uploadSection;
                    return $row;
                })
            );
        }

        return $rows;
    }

    protected function attendanceSummary(Collection $rows): array
    {
        $normalized = $rows->map(function ($row) {
            $estado = strtolower($row['estado'] ?? '');
            return match (true) {
                str_contains($estado, 'pres') => 'presente',
                str_contains($estado, 'aus') => 'ausente',
                str_contains($estado, 'tar') => 'tardanza',
                str_contains($estado, 'jus') => 'justificado',
                default => 'otro',
            };
        });

        $total = $normalized->count();
        $presentes = $normalized->where(fn ($estado) => $estado === 'presente')->count();
        $ausentes = $normalized->where(fn ($estado) => $estado === 'ausente')->count();
        $tardanzas = $normalized->where(fn ($estado) => $estado === 'tardanza')->count();

        return [
            'total' => $total,
            'presentes' => $presentes,
            'ausentes' => $ausentes,
            'tardanzas' => $tardanzas,
            'tasa_asistencia' => $total ? round(($presentes / $total) * 100, 1) : 0,
        ];
    }

    protected function stabilityScore($datos): ?float
    {
        $data = collect($datos)->sortBy('anio')->values();
        if ($data->count() < 2) {
            return null;
        }

        $last = $data->last()['matriculados'];
        $prev = $data->slice(-2, 1)->first()['matriculados'];
        if ($prev == 0) {
            return 60;
        }

        $variation = (($last - $prev) / $prev) * 100;
        $score = 100 - min(100, abs($variation));

        return round(max(40, $score), 1);
    }

    protected function buildEnrollmentChart(array $projection): ?array
    {
        $datos = $projection['datos'];
        if ($datos instanceof Collection) {
            $datos = $datos->toArray();
        }

        if (empty($datos)) {
            return null;
        }

        return [
            'labels' => array_column($datos, 'anio'),
            'values' => array_column($datos, 'matriculados'),
        ];
    }

    protected function enrollmentTrend($datos, array $proyecciones = []): ?array
    {
        $collection = collect($datos)->sortBy('anio')->values();
        if ($collection->isEmpty()) {
            return null;
        }

        $current = $collection->last();
        $previous = $collection->count() >= 2 ? $collection->slice(-2, 1)->first() : null;
        $next = $proyecciones[0] ?? null;

        $trend = [
            'direction' => null,
            'delta' => null,
            'projection_direction' => null,
            'projection_delta' => null,
            'message' => null,
        ];

        if ($previous) {
            $diff = $current['matriculados'] - $previous['matriculados'];
            $trend['delta'] = $diff;
            $trend['direction'] = $diff >= 0 ? 'up' : 'down';
        }

        if ($next) {
            $projDiff = $next['matriculados'] - $current['matriculados'];
            $trend['projection_delta'] = $projDiff;
            $trend['projection_direction'] = $projDiff >= 0 ? 'up' : 'down';
            $trend['message'] = $projDiff >= 0
                ? "Se proyecta un incremento de {$projDiff} estudiantes para {$next['anio']}."
                : "Se proyecta una disminución de " . abs($projDiff) . " estudiantes para {$next['anio']}.";
        } elseif ($trend['delta'] !== null) {
            $trend['message'] = $trend['delta'] >= 0
                ? 'La matrícula ha venido en aumento en los últimos años.'
                : 'La matrícula mostró una ligera caída en el último año.';
        } else {
            $trend['message'] = 'Se requiere más historial para calcular la tendencia.';
        }

        return $trend;
    }

    protected function clasificar(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Excelente',
            $score >= 70 => 'Bueno',
            $score >= 55 => 'En observación',
            default => 'En riesgo',
        };
    }

    protected function normalizeGrade(?string $value): string
    {
        $value = Str::upper(trim($value ?? ''));
        $value = str_replace(['°', 'º', '.', ' ', '-'], '', $value);
        return $value;
    }

    protected function normalizeSection(?string $value): string
    {
        $value = Str::upper(trim($value ?? ''));
        return preg_replace('/[^A-Z0-9]/', '', $value);
    }
}
