<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100">
        <livewire:sidebar />
        
        <div class="lg:ml-64 transition-all duration-300">
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="flex items-center justify-between px-4 py-4">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                        Inscripciones
                    </h1>
                </div>
            </header>

            <main class="p-6 space-y-6">
                <div class="page-hero">
                    <div class="page-hero__icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-wide text-white/80">Datos pedagógicos</p>
                        <h1 class="text-3xl font-bold">Carga de inscripciones</h1>
                        <p class="text-white/80 text-sm">Sube reportes oficiales por grado y sección para mantener actualizado el padrón.</p>
                    </div>
                </div>
                <livewire:data-upload-manager tipo="inscripciones" />
            </main>
        </div>
    </div>
</x-layouts.app>

