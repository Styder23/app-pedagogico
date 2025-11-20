<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DataUpload;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\Institucion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataUploadController extends Controller
{
    private array $templates = [
        'inscripciones' => [
            'filename' => 'plantilla_inscripciones.csv',
            'headers' => ['AÑO', 'GRADO', 'SECCION', 'MATRICULADOS'],
            'rows' => [
                ['2022', '1°', 'A', '32'],
                ['2023', '1°', 'A', '35'],
                ['2024', '1°', 'A', '37'],
            ],
        ],
        'asistencias' => [
            'filename' => 'plantilla_asistencias.csv',
            'headers' => ['FECHA', 'GRADO', 'SECCION', 'ESTUDIANTE', 'ESTADO'],
            'rows' => [
                ['2024-03-01', '3°', 'B', 'FERNANDEZ GARCIA ANA', 'PRESENTE'],
                ['2024-03-01', '3°', 'B', 'GUTIERREZ PEREZ LUIS', 'AUSENTE'],
                ['2024-03-02', '3°', 'B', 'FERNANDEZ GARCIA ANA', 'PRESENTE'],
            ],
        ],
        'notas' => [
            'filename' => 'plantilla_notas.csv',
            'headers' => ['ESTUDIANTE', 'EVALUACION', 'TIPO', 'NOTA'],
            'rows' => [
                ['ROJAS DIAZ MARIA', 'Examen Bimestral 1', 'EXAMEN', '17'],
                ['ROJAS DIAZ MARIA', 'Trabajo Colaborativo', 'PROYECTO', '18'],
                ['SOTO QUISPE JUAN', 'Examen Bimestral 1', 'EXAMEN', '12'],
            ],
        ],
    ];

    public function store(Request $request, string $tipo): JsonResponse
    {
        abort_unless(in_array($tipo, ['inscripciones', 'asistencias', 'notas']), 404, 'Tipo de carga no permitido.');

        $rules = [
            'institucion_id' => ['required', 'exists:instituciones,id'],
            'grado' => ['required', 'string'],
            'seccion' => ['required', 'string'],
            'archivo' => ['required', 'file', 'max:10240', 'mimes:xlsx,xls,csv,pdf,doc,docx'],
        ];

        if ($tipo === 'notas') {
            $rules['evaluacion_tipo'] = ['required', 'in:parcial,tarea,examen,practica,proyecto,otro'];
        }

        $validated = $request->validate($rules);

        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!$user->isAdmin()) {
            if (!$user->institucion_id || (int) $validated['institucion_id'] !== (int) $user->institucion_id) {
                abort(403, 'No puedes cargar archivos para otra institución.');
            }
        }

        $grado = mb_strtoupper($validated['grado']);
        $seccion = mb_strtoupper($validated['seccion']);
        $institucion = Institucion::findOrFail($validated['institucion_id']);

        $file = $request->file('archivo');
        $extension = strtolower($file->getClientOriginalExtension());
        $slug = Str::slug("{$tipo}_{$institucion->nombre}_{$grado}_{$seccion}");
        $filename = "{$slug}_" . now()->format('Ymd_His') . ".{$extension}";
        $path = $file->storeAs("uploads/{$tipo}", $filename, 'public');

        Log::info('Archivo recibido vía controlador', [
            'tipo' => $tipo,
            'path' => $path,
            'usuario' => Auth::id(),
        ]);

        $upload = DataUpload::create([
            'tipo' => $tipo,
            'institucion_id' => $validated['institucion_id'],
            'grado' => $grado,
            'seccion' => $seccion,
            'evaluacion_tipo' => $tipo === 'notas' ? $validated['evaluacion_tipo'] : null,
            'archivo' => $path,
            'formato' => $extension,
            'usuario_id' => Auth::id(),
        ]);

        $this->registerByTipo($tipo, $validated['institucion_id'], $grado, $seccion, $path, $extension, $validated['evaluacion_tipo'] ?? null);

        return response()->json([
            'message' => 'Archivo cargado correctamente',
            'upload_id' => $upload->id,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ], 201);
    }

    public function template(string $tipo)
    {
        abort_unless(array_key_exists($tipo, $this->templates), 404, 'Plantilla no disponible.');

        $template = $this->templates[$tipo];

        return response()->streamDownload(function () use ($template) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            $delimiter = ';';
            fputcsv($handle, $template['headers'], $delimiter);
            foreach ($template['rows'] as $row) {
                fputcsv($handle, $row, $delimiter);
            }
            fclose($handle);
        }, $template['filename'], [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function registerByTipo(string $tipo, int $institucionId, string $grado, string $seccion, string $path, string $extension, ?string $evaluacionTipo = null): void
    {
        switch ($tipo) {
            case 'inscripciones':
                Enrollment::create([
                    'persona_id' => null,
                    'section_id' => null,
                    'institucion_id' => $institucionId,
                    'grado' => $grado,
                    'seccion' => $seccion,
                    'fecha_inscripcion' => now()->toDateString(),
                    'estado' => 'activo',
                    'observaciones' => 'Carga masiva de inscripciones',
                    'archivo_path' => $path,
                    'archivo_formato' => $extension,
                    'es_carga' => true,
                    'usuario_id' => Auth::id(),
                ]);
                break;
            case 'asistencias':
                Attendance::create([
                    'enrollment_id' => null,
                    'institucion_id' => $institucionId,
                    'grado' => $grado,
                    'seccion' => $seccion,
                    'fecha' => now()->toDateString(),
                    'estado' => 'presente',
                    'justificacion' => 'Carga masiva de asistencia',
                    'archivo_path' => $path,
                    'archivo_formato' => $extension,
                    'es_carga' => true,
                    'registrado_por' => Auth::id(),
                ]);
                break;
            case 'notas':
                Evaluation::create([
                    'nombre' => strtoupper($evaluacionTipo ?? 'Evaluación') . ' ' . $grado . $seccion,
                    'tipo' => $evaluacionTipo ?? 'otro',
                    'descripcion' => 'Carga masiva de notas',
                    'peso' => 1.00,
                    'orden' => 0,
                    'activo' => true,
                    'institucion_id' => $institucionId,
                    'grado' => $grado,
                    'seccion' => $seccion,
                    'archivo_path' => $path,
                    'archivo_formato' => $extension,
                    'es_carga' => true,
                    'usuario_id' => Auth::id(),
                ]);
                break;
        }
    }
}
