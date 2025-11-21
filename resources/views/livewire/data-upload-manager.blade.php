@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
<div>
@if($institutionMissing)
    <div class="card-surface p-6 text-center">
        <h3 class="text-xl font-semibold text-slate-800">Sin institución asignada</h3>
        <p class="text-slate-500 mt-2">Para gestionar archivos necesitas que tu usuario tenga una institución. Contacta al administrador.</p>
    </div>
@else
<div class="card-surface p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $titulo }}</h3>
            <p class="text-slate-500 text-sm">Carga archivos Excel, CSV, PDF o Word para {{ strtolower($titulo) }}.</p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div class="p-4 rounded-xl border border-blue-100 bg-blue-50/60">
            <p class="text-sm text-blue-900">
                Utiliza formatos consistentes para que el sistema pueda generar estadísticas, predicciones y gráficos.
                Cada archivo debe incluir solo los registros del grado y sección seleccionados.
            </p>
        </div>
        <div class="flex flex-wrap gap-3 justify-start md:justify-end">
            <a
                href="{{ route('uploads.template', $tipo) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 text-sm font-semibold transition"
            >
                <i class="fas fa-file-export"></i> Descargar plantilla
            </a>
            <button
                type="button"
                onclick="Swal.fire('Formato requerido', document.getElementById('format-help-{{ $tipo }}').innerHTML, 'info')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition"
            >
                <i class="fas fa-info-circle"></i> Ver especificaciones
            </button>
        </div>
    </div>

    <div id="format-help-{{ $tipo }}" class="hidden">
        @switch($tipo)
            @case('inscripciones')
                <p class="text-left text-sm text-slate-600">
                    Columnas obligatorias: <strong>AÑO</strong>, <strong>GRADO</strong>, <strong>SECCION</strong>, <strong>MATRICULADOS</strong>.
                    Cada fila representa el total anual de estudiantes matriculados para el grado y sección elegidos.
                </p>
                @break
            @case('asistencias')
                <p class="text-left text-sm text-slate-600">
                    Usa las columnas: <strong>FECHA</strong> (YYYY-MM-DD), <strong>GRADO</strong>, <strong>SECCION</strong>, <strong>ESTUDIANTE</strong>, <strong>ESTADO</strong> (Presente, Ausente, Tardanza, Justificado).
                </p>
                @break
            @case('notas')
                <p class="text-left text-sm text-slate-600">
                    Incluye: <strong>ESTUDIANTE</strong>, <strong>EVALUACION</strong>, <strong>TIPO</strong> (Examen, Parcial, Tarea, Proyecto, Práctica, Otro) y <strong>NOTA</strong> (0-20).
                </p>
                @break
        @endswitch
    </div>

    <form
        id="upload-form-{{ $tipo }}"
        class="mt-6 grid gap-4 md:grid-cols-2"
        enctype="multipart/form-data"
        method="POST"
        action="{{ route('uploads.store', $tipo) }}"
        data-token="{{ csrf_token() }}"
        data-type="{{ $tipo }}"
    >
        @csrf
        <div>
            <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                <i class="fas fa-school text-blue-500"></i> Institución
            </label>
            <select wire:model.defer="institucion_id" name="institucion_id" class="mt-1 input-primary" @disabled(!$canSelectInstitution)>
                <option value="">Selecciona institución</option>
                @foreach($instituciones as $institucion)
                    <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                @endforeach
            </select>
            @if(!$canSelectInstitution)
                <input type="hidden" name="institucion_id" value="{{ $lockedInstitutionId }}">
                <p class="text-xs text-slate-500 mt-1">Solo puedes gestionar archivos de tu institución.</p>
            @endif
            @error('institucion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                <i class="fas fa-layer-group text-cyan-500"></i> Grado
            </label>
            <select wire:model.defer="grado" name="grado" class="mt-1 input-primary uppercase">
                <option value="">Selecciona grado</option>
                @foreach($grados as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
            @error('grado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                <i class="fas fa-layer-group text-blue-500"></i> Sección
            </label>
            <select wire:model.defer="seccion" name="seccion" class="mt-1 input-primary uppercase">
                <option value="">Selecciona sección</option>
                @foreach($secciones as $sec)
                    <option value="{{ $sec }}">{{ $sec }}</option>
                @endforeach
            </select>
            @error('seccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        @if($mostrarEvaluacion)
            <div>
                <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                    <i class="fas fa-star text-amber-500"></i> Tipo de evaluación
                </label>
                <select wire:model.defer="evaluacion_tipo" name="evaluacion_tipo" class="mt-1 input-primary uppercase">
                    <option value="">Selecciona</option>
                    
                    <option value="tarea">Tarea</option>
                    <option value="examen">Examen</option>
                    <option value="practica">Trabajos</option>
                    
                </select>
                @error('evaluacion_tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        @endif

        <div class="md:col-span-2">
            <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                <i class="fas fa-file-upload text-green-500"></i> Archivo
            </label>
            <input
                type="file"
                name="archivo"
                wire:key="archivo-input-{{ $tipo }}"
                id="archivo-input-{{ $tipo }}"
                class="mt-1 input-primary"
                accept=".xlsx,.xls,.csv,.pdf,.doc,.docx"
            />
            <p class="text-xs text-slate-500 mt-1">Formatos permitidos: Excel, CSV, PDF o Word (máx. 10MB).</p>
            @error('archivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="md:col-span-2 flex justify-end">
            <button
                type="submit"
                data-upload-button
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold shadow transition hover:from-blue-600 hover:to-cyan-600 hover:-translate-y-0.5"
            >
                <i class="fas fa-cloud-upload-alt"></i>
                Subir archivo
            </button>
        </div>
    </form>

    <div class="mt-8">
        <h4 class="text-lg font-semibold text-slate-700 mb-3 flex items-center gap-2">
            <i class="fas fa-history text-blue-500"></i> Historial de cargas
        </h4>
        <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
        <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white uppercase text-xs tracking-wide">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Institución</th>
                        <th class="px-4 py-3">Grado/Sección</th>
                        @if($mostrarEvaluacion)
                            <th class="px-4 py-3">Evaluación</th>
                        @endif
                        <th class="px-4 py-3">Formato</th>
                        <th class="px-4 py-3">Archivo</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($uploads as $upload)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $upload->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $upload->institucion->nombre ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $upload->grado }} / {{ $upload->seccion }}</td>
                            @if($mostrarEvaluacion)
                                <td class="px-4 py-3 capitalize">{{ $upload->evaluacion_tipo ?? '—' }}</td>
                            @endif
                            <td class="px-4 py-3 font-semibold">{{ strtoupper($upload->formato) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ Storage::url($upload->archivo) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2 justify-center">
                                    <button
                                        type="button"
                                        wire:click="startEdit({{ $upload->id }})"
                                        class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    >
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="confirmDelete({{ $upload->id }})"
                                        class="p-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="{{ $mostrarEvaluacion ? 7 : 6 }}" class="px-4 py-6 text-center text-slate-500">
                                Aún no hay archivos cargados.
                        </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $uploads->links() }}
        </div>
    </div>
</div>

@if($showPreview && $previewFile)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-6 relative">
            <button
                wire:click="closePreview"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-700"
            >
                <i class="fas fa-times"></i>
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="section-title__icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase tracking-wide">Vista previa</p>
                    <h3 class="text-2xl font-bold text-slate-900">{{ $previewMeta['institucion'] ?? '—' }}</h3>
                    <p class="text-slate-500 text-sm">{{ strtoupper($previewMeta['tipo'] ?? '') }} · Grado {{ $previewMeta['grado'] ?? '—' }} · Sección {{ $previewMeta['seccion'] ?? '—' }}</p>
                </div>
            </div>

            @if(Str::endsWith($previewFile, ['.pdf']))
                <iframe src="{{ Storage::url($previewFile) }}" class="w-full h-[480px] rounded-xl border border-slate-200"></iframe>
            @else
                @if(!empty($previewTable) && !empty($previewTable['rows']))
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                                <tr>
                                    @foreach($previewTable['headers'] as $header)
                                        <th class="px-4 py-2">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($previewTable['rows'] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td class="px-4 py-2 text-slate-700">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-xs text-slate-500 px-4 py-2">Mostrando primeras {{ count($previewTable['rows']) }} filas.</p>
                    </div>
                @else
                    <div class="p-6 text-center border border-dashed border-slate-300 rounded-xl">
                        <i class="fas fa-file text-4xl text-blue-500 mb-3"></i>
                        <p class="text-slate-600">{{ $previewMessage ?? 'No es posible previsualizar este formato en línea.' }}</p>
                        <a
                            href="{{ Storage::url($previewFile) }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold"
                        >
                            <i class="fas fa-download"></i> Descargar archivo
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endif

@if($showEditModal)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative">
            <button
                wire:click="closeEditModal"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-700"
            >
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-xl font-bold text-slate-900 mb-4">Editar registro</h3>
            <form wire:submit.prevent="updateUpload" class="space-y-4">
                <div>
                    <label class="text-sm font-semibold text-slate-600">Institución</label>
                    <select wire:model="editForm.institucion_id" class="mt-1 input-primary" @disabled(!$canSelectInstitution)>
                        <option value="">Selecciona institución</option>
                        @foreach($instituciones as $institucion)
                            <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                        @endforeach
                    </select>
                    @if(!$canSelectInstitution)
                        <p class="text-xs text-slate-500 mt-1">Solo puedes asignar tu institución.</p>
                    @endif
                    @error('editForm.institucion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-600">Grado</label>
                        <input type="text" wire:model="editForm.grado" class="mt-1 input-primary uppercase" />
                        @error('editForm.grado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600">Sección</label>
                        <input type="text" wire:model="editForm.seccion" class="mt-1 input-primary uppercase" />
                        @error('editForm.seccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                @if($editingTipo === 'notas')
                    <div>
                        <label class="text-sm font-semibold text-slate-600">Tipo de evaluación</label>
                        <select wire:model="editForm.evaluacion_tipo" class="mt-1 input-primary uppercase">
                            <option value="">Selecciona</option>
                            <option value="parcial">Parcial</option>
                            <option value="tarea">Tarea</option>
                            <option value="examen">Examen</option>
                            <option value="practica">Práctica</option>
                            <option value="proyecto">Proyecto</option>
                            <option value="otro">Otro</option>
                        </select>
                        @error('editForm.evaluacion_tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                @endif
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeEditModal" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@push('scripts')
    <script>
        (() => {
            const formId = 'upload-form-{{ $tipo }}';
            window.__uploadFormsInit = window.__uploadFormsInit || {};
            if (window.__uploadFormsInit[formId]) return;
            window.__uploadFormsInit[formId] = true;

            const initForm = () => {
                const form = document.getElementById(formId);
                if (!form) return;
                const button = form.querySelector('[data-upload-button]');
                const token = form.dataset.token;

                const setLoading = (state) => {
                    if (!button) return;
                    if (state) {
                        button.disabled = true;
                        button.classList.add('opacity-70');
                        button.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Subiendo...';
                    } else {
                        button.disabled = false;
                        button.classList.remove('opacity-70');
                        button.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Subir archivo';
                    }
                };

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const formData = new FormData(form);
                    setLoading(true);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            console.error('Error al subir archivo', payload);
                            Swal.fire({ icon: 'error', title: payload.message || 'No se pudo subir el archivo' });
                            return;
                        }

                        Swal.fire({ icon: 'success', title: payload.message || 'Archivo cargado', timer: 2200, showConfirmButton: false });
                        form.reset();
                        window.Livewire && window.Livewire.dispatch('upload-completed');
                    } catch (error) {
                        console.error('Excepción subiendo archivo', error);
                        Swal.fire({ icon: 'error', title: 'Error inesperado al subir archivo' });
                    } finally {
                        setLoading(false);
                    }
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initForm);
            } else {
                initForm();
            }
        })();
    </script>
@endpush
@endif

@if($showDeleteModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" wire:click="cancelDelete">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4" wire:click.stop>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">¿Eliminar {{ $uploadToDeleteLabel }}?</h3>
                        <p class="text-slate-600 text-sm">Esta acción no se puede deshacer.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        wire:click="cancelDelete"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="deleteUpload"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition"
                    >
                        <i class="fas fa-trash mr-2"></i> Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
