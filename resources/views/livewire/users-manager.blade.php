<div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Usuarios</h2>
            <p class="text-slate-500 text-sm">Gestiona personas y credenciales vinculadas.</p>
        </div>
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Buscar por nombre, documento o correo"
                    class="pl-9 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                />
            </div>
            <button
                wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold shadow hover:from-blue-600 hover:to-cyan-600 transition"
            >
                <i class="fas fa-user-plus"></i>
                Nuevo
            </button>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
        <table class="w-full text-left min-w-[820px] text-sm">
            <thead class="bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-500 text-white uppercase tracking-wide text-xs">
                <tr>
                    <th class="px-4 py-3">Persona</th>
                    <th class="px-4 py-3">Documento</th>
                    <th class="px-4 py-3">Correo</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3">Institución</th>
                    <th class="px-4 py-3">Creado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($users as $user)
                    <tr class="text-sm text-slate-700">
                        <td class="px-4 py-4">
                            <div class="font-semibold">
                                {{ optional($user->persona)->nombres }} {{ optional($user->persona)->apellidos }}
                            </div>
                            <p class="text-slate-500 text-xs capitalize">{{ optional($user->persona)->tipo }}</p>
                        </td>
                        <td class="px-4">{{ optional($user->persona)->dni ?? '—' }}</td>
                        <td class="px-4">{{ $user->email }}</td>
                        <td class="px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->role?->name === 'Admin' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                                {{ $user->role->name ?? 'Sin rol' }}
                            </span>
                        </td>
                        <td class="px-4">{{ $user->institucion->nombre ?? optional($user->persona->institucion)->nombre ?? 'No asignada' }}</td>
                        <td class="px-4">{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td class="px-4 text-center">
                            <div class="inline-flex gap-2">
                                <button
                                    wire:click="openEditModal({{ $user->id }})"
                                    class="px-3 py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    wire:click="confirmUserDeletion({{ $user->id }})"
                                    class="px-3 py-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                            No se encontraron usuarios con los criterios actuales.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl relative overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-4 text-white flex items-center gap-3">
                    <i class="fas fa-user-cog text-2xl"></i>
                    <div>
                        <p class="text-sm uppercase tracking-wide">Gestión de usuarios</p>
                        <h3 class="text-2xl font-bold">{{ $modalTitle }}</h3>
                    </div>
                    <button
                        wire:click="$set('showModal', false)"
                        class="ml-auto text-white/80 hover:text-white"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                <button
                    wire:click="$set('showModal', false)"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-700"
                >
                    <i class="fas fa-times"></i>
                </button>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-id-card text-blue-500"></i> DNI (8 dígitos)
                        </label>
                        <input type="text" wire:model.defer="dni" maxlength="8" class="mt-1 input-primary uppercase" />
                        @error('dni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-user-tag text-cyan-500"></i> Tipo de persona
                        </label>
                        <select wire:model.defer="tipo" class="mt-1 input-primary uppercase">
                            <option value="docente">DOCENTE</option>
                            <option value="estudiante">ESTUDIANTE</option>
                        </select>
                        @error('tipo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-user-shield text-blue-500"></i> Rol
                        </label>
                        <select wire:model="role_id" class="mt-1 input-primary uppercase">
                            <option value="">Seleccione rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-school text-blue-500"></i> Institución
                        </label>
                        <select wire:model="institucion_id" class="mt-1 input-primary uppercase" @disabled(!$canSelectInstitution || !$this->requiresInstitucion)>
                            <option value="">Selecciona institución</option>
                            @foreach($instituciones as $institucion)
                                <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                            @endforeach
                        </select>
                        @error('institucion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @if(!$this->requiresInstitucion)
                            <p class="text-xs text-slate-500 mt-1">Este rol no requiere institución.</p>
                        @elseif(!$canSelectInstitution)
                            <p class="text-xs text-slate-500 mt-1">Solo puedes asignar usuarios a tu institución.</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-user text-indigo-500"></i> Nombres
                        </label>
                        <input type="text" wire:model.defer="nombres" class="mt-1 input-primary uppercase" />
                        @error('nombres') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-user text-cyan-500"></i> Apellidos
                        </label>
                        <input type="text" wire:model.defer="apellidos" class="mt-1 input-primary uppercase" />
                        @error('apellidos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-calendar text-blue-500"></i> Fecha de nacimiento
                        </label>
                        <input type="date" wire:model.defer="fecha_nacimiento" class="mt-1 input-primary" />
                        @error('fecha_nacimiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-phone text-green-500"></i> Celular (9 dígitos)
                        </label>
                        <input type="text" wire:model.defer="telefono" maxlength="9" class="mt-1 input-primary uppercase" />
                        @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-envelope text-blue-500"></i> Correo
                        </label>
                        <input type="email" wire:model.defer="email" class="mt-1 input-primary lowercase" />
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-rose-500"></i> Dirección
                        </label>
                        <input type="text" wire:model.defer="direccion" class="mt-1 input-primary uppercase" />
                        @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-key text-amber-500"></i> Contraseña
                        </label>
                        <input type="password" wire:model.defer="password" class="mt-1 input-primary" placeholder="{{ $editingUserId ? 'Opcional' : '' }}" />
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="fas fa-lock text-purple-500"></i> Confirmar contraseña
                        </label>
                        <input type="password" wire:model.defer="password_confirmation" class="mt-1 input-primary" placeholder="{{ $editingUserId ? 'Opcional' : '' }}" />
                        @error('password_confirmation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="save"
                        class="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold hover:from-blue-600 hover:to-cyan-600 shadow"
                    >
                        Guardar
                    </button>
                </div>
                </div>
            </div>
        </div>
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
                            <h3 class="text-xl font-bold text-slate-800">¿Eliminar usuario?</h3>
                            <p class="text-slate-600 text-sm">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-slate-600">
                            <span class="font-semibold">Usuario:</span> {{ $userToDeleteName }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button
                            wire:click="cancelDelete"
                            class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition"
                        >
                            Cancelar
                        </button>
                        <button
                            wire:click="deleteUser"
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
