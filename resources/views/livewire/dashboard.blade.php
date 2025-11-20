@php
    use Illuminate\Support\Facades\Storage;
@endphp
<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100">
    <livewire:sidebar />
    
    <div class="lg:ml-64 transition-all duration-300">
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="flex items-center justify-between px-4 py-4">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                    Dashboard
                </h1>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2 text-gray-600">
                        <div>
                            <p class="text-sm font-semibold">
                                {{ auth()->user()?->persona?->nombres }} {{ auth()->user()?->persona?->apellidos }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ auth()->user()?->role?->name }} · {{ auth()->user()?->institucion?->nombre ?? 'Sin institución' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6 space-y-8">
            @php
                $statCards = [
                    [
                        'label' => 'Total usuarios',
                        'value' => number_format($stats['usuarios']),
                        'icon' => 'fa-users',
                        'border' => 'border-blue-500',
                        'gradient' => 'from-blue-500 to-blue-600',
                    ],
                    [
                        'label' => 'Instituciones activas',
                        'value' => number_format($stats['instituciones']),
                        'icon' => 'fa-building',
                        'border' => 'border-cyan-500',
                        'gradient' => 'from-cyan-500 to-cyan-600',
                    ],
                    [
                        'label' => 'Archivos cargados',
                        'value' => number_format($stats['cargas']),
                        'icon' => 'fa-cloud-upload-alt',
                        'border' => 'border-indigo-500',
                        'gradient' => 'from-indigo-500 to-indigo-600',
                    ],
                    [
                        'label' => 'Listos para IA',
                        'value' => number_format($stats['ia_ready']),
                        'icon' => 'fa-robot',
                        'border' => 'border-emerald-500',
                        'gradient' => 'from-emerald-500 to-teal-500',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach($statCards as $card)
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 {{ $card['border'] }} hover:-translate-y-1 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wide">{{ $card['label'] }}</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $card['value'] }}</p>
                            </div>
                            <div class="bg-gradient-to-br {{ $card['gradient'] }} rounded-full p-4 text-white text-2xl">
                                <i class="fas {{ $card['icon'] }}"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-bolt"></i>
                        </span>
                        Accesos rápidos
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($quickActions as $action)
                            <a
                                href="{{ $action['route'] }}"
                                class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 hover:border-transparent hover:shadow-lg transition bg-white"
                            >
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $action['color'] }} text-white flex items-center justify-center text-xl">
                                    <i class="fas {{ $action['icon'] }}"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $action['label'] }}</p>
                                    <p class="text-sm text-slate-500">{{ $action['description'] }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-600">
                            <i class="fas fa-chart-pie"></i>
                        </span>
                        Distribución de cargas
                    </h2>
                    @php
                        $totalUploads = max(1, $stats['cargas']);
                    @endphp
                    <div class="space-y-4">
                        @foreach(['inscripciones' => 'Inscripciones', 'asistencias' => 'Asistencias', 'notas' => 'Notas'] as $key => $label)
                            @php
                                $value = $uploadsByTipo[$key] ?? 0;
                                $percent = round(($value / $totalUploads) * 100);
                                $colors = [
                                    'inscripciones' => 'from-blue-500 to-cyan-500',
                                    'asistencias' => 'from-indigo-500 to-purple-500',
                                    'notas' => 'from-emerald-500 to-teal-500',
                                ];
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm text-slate-600 mb-1">
                                    <span>{{ $label }}</span>
                                    <span>{{ $value }} ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $colors[$key] ?? 'from-slate-400 to-slate-500' }}" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-400 mt-4">Se consideran las últimas {{ $stats['cargas'] }} cargas realizadas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-cyan-100 text-cyan-600">
                            <i class="fas fa-history"></i>
                        </span>
                        Actividad reciente
                    </h2>
                    <div class="space-y-4 max-h-[360px] overflow-y-auto pr-2">
                        @forelse($recentUploads as $upload)
                            <div class="flex items-start gap-4 p-3 rounded-xl border border-slate-100 hover:border-blue-200 transition bg-white">
                                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                                    <i class="fas {{ [
                                        'inscripciones' => 'fa-clipboard-list',
                                        'asistencias' => 'fa-calendar-check',
                                        'notas' => 'fa-star',
                                    ][$upload->tipo] ?? 'fa-file' }}"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-800 capitalize">{{ $upload->tipo }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ $upload->institucion->nombre ?? '—' }} · {{ $upload->grado }}{{ $upload->seccion }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $upload->created_at->diffForHumans() }}</p>
                                </div>
                                <a href="{{ Storage::url($upload->archivo) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm font-semibold">
                                    Ver
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Todavía no se han cargado archivos.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-600">
                                <i class="fas fa-brain"></i>
                            </span>
                            Insights de IA
                        </h2>
                        @if($latestNotesMetrics)
                            <ul class="space-y-2 text-sm text-slate-600">
                                <li><strong>Promedio general:</strong> {{ $latestNotesMetrics['promedio'] }} / 20</li>
                                <li><strong>Aprobados:</strong> {{ $latestNotesMetrics['aprobados'] }} ({{ $latestNotesMetrics['porcentaje_aprobados'] }}%)</li>
                                <li><strong>Mejor nota:</strong> {{ $latestNotesMetrics['max'] }} · <strong>Menor:</strong> {{ $latestNotesMetrics['min'] }}</li>
                                <li><strong>Contexto:</strong> {{ $latestNotesMetrics['institucion'] ?? '—' }} · {{ $latestNotesMetrics['grado'] }}{{ $latestNotesMetrics['seccion'] }}</li>
                            </ul>
                        @else
                            <p class="text-sm text-slate-500">Carga un archivo de notas para calcular promedios y detectar brechas.</p>
                        @endif
                    </div>

                    <div class="border-t border-dashed border-slate-200 pt-4">
                        <h3 class="font-semibold text-slate-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-chart-line text-blue-500"></i>
                            Proyección de matrícula
                        </h3>
                        @if($enrollmentProjection)
                            <ul class="space-y-2 text-sm text-slate-600">
                                @foreach($enrollmentProjection['datos'] as $dato)
                                    <li>
                                        {{ $dato['anio'] }} · {{ $dato['matriculados'] }} estudiantes
                                    </li>
                                @endforeach
                            </ul>
                            <p class="text-xs text-slate-400 mt-2 uppercase tracking-wide">Próximos años</p>
                            <ul class="space-y-1 text-sm text-emerald-600">
                                @foreach($enrollmentProjection['proyecciones'] as $projection)
                                    <li>{{ $projection['anio'] }} ≈ <strong>{{ $projection['matriculados'] }}</strong> estudiantes</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-slate-500">Sube al menos dos archivos de inscripciones con columnas de año para proyectar la matrícula.</p>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</x-layouts.app>
