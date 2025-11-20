<div class="space-y-6">
    @if($institutionMissing)
        <div class="card-surface p-6 text-center">
            <h3 class="text-xl font-semibold text-slate-800">Sin institución asignada</h3>
            <p class="text-slate-500 mt-2">Contacta al administrador para asociarte a una institución y habilitar las predicciones.</p>
        </div>
    @else
    <div class="card-surface p-6">
        <h2 class="section-title">
            <span class="section-title__icon"><i class="fas fa-brain"></i></span>
            Predicción y análisis
        </h2>
        <p class="text-slate-500 text-sm mt-2">Selecciona una institución y un grado para estimar el rendimiento proyectado con base en inscripciones, asistencias y notas cargadas.</p>

        <div class="mt-6 grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-600">Institución</label>
                <select wire:model="institucion_id" class="mt-1 input-primary" @disabled(!$canSelectInstitution)>
                    <option value="">Selecciona institución</option>
                    @foreach($instituciones as $institucion)
                        <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                    @endforeach
                </select>
                @if(!$canSelectInstitution)
                    <p class="text-xs text-slate-500 mt-1">Solo puedes consultar tu institución.</p>
                @endif
                @error('institucion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-600">Grado</label>
                <select wire:model="grado" class="mt-1 input-primary uppercase">
                    <option value="">Selecciona grado</option>
                    @foreach($grados as $gra)
                        <option value="{{ $gra }}">{{ $gra }}</option>
                    @endforeach
                </select>
                @error('grado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-600">Sección</label>
                <select wire:model="seccion" class="mt-1 input-primary uppercase">
                    <option value="">Selecciona sección</option>
                    @foreach($secciones as $sec)
                        <option value="{{ $sec }}">{{ $sec }}</option>
                    @endforeach
                </select>
                @error('seccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-end">
                <button
                    wire:click="generar"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold shadow hover:from-blue-600 hover:to-cyan-600 transition"
                >
                    <i class="fas fa-chart-line"></i> Analizar
                </button>
            </div>
        </div>
    </div>

    @if($resultado)
        <div class="grid gap-6">
            <div class="card-surface p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <p class="text-xs uppercase text-blue-500 font-semibold tracking-[0.2em]">Rendimiento estimado</p>
                        <p class="text-6xl font-black text-slate-900 mt-2">{{ $resultado['prediction'] }}%</p>
                        <p class="text-lg font-semibold text-slate-700">{{ $resultado['label'] }}</p>
                        <p class="text-sm text-slate-500 max-w-xl">
                            Combinamos promedios de notas (60%), consistencia de asistencia (30%) y estabilidad de matrícula (10%).
                            Mientras más archivos cargues para este grado/sección, más precisa será la estimación.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
                        @foreach($componentes as $comp)
                            <div class="rounded-2xl p-4 {{ $comp['color'] }}">
                                <p class="text-xs uppercase font-semibold">{{ $comp['label'] }}</p>
                                <p class="text-2xl font-bold mt-1 text-slate-900">{{ $comp['value'] }}%</p>
                                <p class="text-xs text-slate-500">Registros analizados: {{ $comp['registros'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="card-surface p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase text-slate-500 font-semibold">Notas y evaluaciones</p>
                            <h3 class="text-xl font-bold text-slate-900">Rendimiento académico</h3>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-600 font-semibold">
                            {{ $resultado['notes']['total'] ?? 0 }} registros
                        </span>
                    </div>
                    @if($resultado['notes'])
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-xl border border-blue-100 p-4">
                                <p class="text-xs uppercase text-blue-500 font-semibold">Promedio general</p>
                                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $resultado['notes']['promedio'] }}</p>
                                <p class="text-xs text-slate-500">Máx: {{ $resultado['notes']['max'] }} · Mín: {{ $resultado['notes']['min'] }}</p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 p-4">
                                <p class="text-xs uppercase text-emerald-500 font-semibold">% Aprobados</p>
                                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $resultado['notes']['porcentaje_aprobados'] }}%</p>
                                <p class="text-xs text-slate-500">{{ $resultado['notes']['aprobados'] }} estudiantes ≥ 11</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-3">Fuente: último archivo de notas cargado para este grado y sección.</p>
                    @else
                        <p class="text-sm text-slate-500">Aún no hay archivos de notas para este grado. Sube uno desde Datos pedagógicos → Notas.</p>
                    @endif
                </div>

                <div class="card-surface p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase text-slate-500 font-semibold">Asistencias</p>
                            <h3 class="text-xl font-bold text-slate-900">Clima de aula</h3>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-600 font-semibold">
                            {{ $resultado['attendance']['total'] ?? 0 }} marcaciones
                        </span>
                    </div>
                    @if($resultado['attendance'])
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-xl border border-emerald-100 p-4">
                                <p class="text-xs uppercase text-emerald-500 font-semibold">Tasa de asistencia</p>
                                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $resultado['attendance']['tasa_asistencia'] }}%</p>
                                <p class="text-xs text-slate-500">{{ $resultado['attendance']['presentes'] }} presentes</p>
                            </div>
                            <div class="rounded-xl border border-amber-100 p-4">
                                <p class="text-xs uppercase text-amber-500 font-semibold">Alertas</p>
                                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $resultado['attendance']['ausentes'] }}</p>
                                <p class="text-xs text-slate-500">Ausencias registradas · Tardanzas: {{ $resultado['attendance']['tardanzas'] }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-3">Fuente: archivos de asistencia más recientes.</p>
                    @else
                        <p class="text-sm text-slate-500">Sin registros de asistencia. Sube un archivo con columnas FECHA, ESTUDIANTE y ESTADO.</p>
                    @endif
                </div>
            </div>

            <div class="card-surface p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase text-slate-500 font-semibold">Inscripciones</p>
                        <h3 class="text-xl font-bold text-slate-900">Proyección de matrícula</h3>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-amber-100 text-amber-600 font-semibold">
                        {{ count($resultado['enrollment']['datos'] ?? []) }} años analizados
                    </span>
                </div>
                @if($resultado['enrollment'])
                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex-1">
                            <table class="w-full text-sm text-slate-600">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-slate-400">
                                        <th class="pb-2">Año</th>
                                        <th class="pb-2">Matriculados</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($resultado['enrollment']['datos'] as $dato)
                                        <tr>
                                            <td class="py-2">{{ $dato['anio'] }}</td>
                                            <td class="py-2 font-semibold">{{ $dato['matriculados'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex-1 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-5 border border-blue-100">
                            <p class="text-xs uppercase text-blue-500 font-semibold mb-2">Pronóstico</p>
                            @if(!empty($resultado['enrollment']['proyecciones']))
                                @foreach($resultado['enrollment']['proyecciones'] as $projection)
                                    <p class="text-sm text-slate-600">
                                        <span class="font-bold text-blue-700">{{ $projection['anio'] }}:</span>
                                        {{ $projection['matriculados'] }} estudiantes estimados
                                    </p>
                                @endforeach
                            @else
                                <p class="text-sm text-slate-500">Se necesitan al menos dos años históricos para generar pronósticos.</p>
                            @endif

                            @if($resultado['enrollment_trend'])
                                <div class="mt-4 p-3 rounded-xl bg-white text-slate-700 text-sm flex items-start gap-3">
                                    <span class="text-xl {{ $resultado['enrollment_trend']['projection_direction'] === 'down' ? 'text-red-500' : 'text-emerald-500' }}">
                                        <i class="fas fa-arrow-{{ $resultado['enrollment_trend']['projection_direction'] === 'down' ? 'down' : 'up' }}"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold">Tendencia</p>
                                        <p>{{ $resultado['enrollment_trend']['message'] }}</p>
                                    </div>
                                </div>
                            @endif

                            <p class="text-xs text-slate-400 mt-4">Fuente: archivos de inscripciones con columna AÑO y MATRÍCULADOS.</p>
                        </div>
                    </div>

                    @if(!empty($resultado['enrollment_chart']))
                        @php $chartId = 'enrollment-chart-' . $this->getId(); @endphp
                        <div class="mt-6">
                            <canvas id="{{ $chartId }}" class="w-full h-64"></canvas>
                        </div>
                        @once
                            @push('scripts')
                                <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
                            @endpush
                        @endonce
                        @push('scripts')
                            <script>
                                (function(){
                                    const chartId = '{{ $chartId }}';
                                    const ctx = document.getElementById(chartId);
                                    if (!ctx || !window.Chart) return;
                                    window.enrollmentCharts = window.enrollmentCharts || {};
                                    if (window.enrollmentCharts[chartId]) {
                                        window.enrollmentCharts[chartId].destroy();
                                    }
                                    const dataset = @json($resultado['enrollment_chart']);
                                    window.enrollmentCharts[chartId] = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: dataset.labels,
                                            datasets: [{
                                                label: 'Matriculados',
                                                data: dataset.values,
                                                borderColor: '#2563eb',
                                                backgroundColor: 'rgba(37,99,235,0.2)',
                                                tension: 0.35,
                                                fill: true,
                                                borderWidth: 2,
                                                pointRadius: 4,
                                                pointBackgroundColor: '#2563eb',
                                            }],
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: { display: false },
                                            },
                                            scales: {
                                                y: { beginAtZero: true },
                                            },
                                        },
                                    });
                                })();
                            </script>
                        @endpush
                    @endif
                @else
                    <p class="text-sm text-slate-500">Agrega archivos de inscripciones con la columna AÑO para calcular tendencias y cupos esperados.</p>
                @endif
            </div>
    </div>
    @endif
</div>
@endif