<div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Instituciones</h2>
            <p class="text-slate-500 text-sm">Crea y administra instituciones educativas.</p>
        </div>
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Buscar por nombre o código"
                    class="pl-9 pr-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                />
            </div>
            <button
                wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold shadow hover:from-blue-600 hover:to-cyan-600 transition"
            >
                <i class="fas fa-university"></i>
                Nueva
            </button>
        </div>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @forelse($instituciones as $institucion)
            <div class="p-5 border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">{{ $institucion->nombre }}</h3>
                        <p class="text-slate-500 text-sm">{{ $institucion->codigo_ugel ?? 'Sin código' }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-blue-50 text-blue-600 font-semibold">
                        {{ $institucion->direccion ?? 'Dirección no registrada' }}
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-600">ID: {{ $institucion->id }}</p>

                <div class="mt-4 flex justify-end gap-2">
                    <button
                        wire:click="openEditModal({{ $institucion->id }})"
                        class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                    >
                        <i class="fas fa-edit mr-1"></i> Editar
                    </button>
                    <button
                        wire:click="confirmDeletion({{ $institucion->id }})"
                        class="px-4 py-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                    >
                        <i class="fas fa-trash mr-1"></i> Eliminar
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-10 text-center text-slate-500">
                No hay instituciones registradas aún.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $instituciones->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 relative">
                <button
                    wire:click="$set('showModal', false)"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-700"
                >
                    <i class="fas fa-times"></i>
                </button>

                <h3 class="text-2xl font-bold text-slate-800 mb-6">{{ $modalTitle }}</h3>

                <div class="grid gap-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-600">Nombre</label>
                        <input type="text" wire:model.defer="nombre" class="mt-1 input-primary" />
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-600">Código</label>
                        <input type="text" wire:model.defer="codigo_ugel" class="mt-1 input-primary" />
                        @error('codigo_ugel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-slate-600">Dirección</label>
                        <input type="text" wire:model.defer="direccion" class="mt-1 input-primary" />
                        @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
    @endif
</div>
