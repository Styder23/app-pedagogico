@php
    use Illuminate\Support\Str;
@endphp
<div>
    @if($mobileOpen)
        <div
            class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 lg:hidden"
            wire:click="toggleMobile"
        ></div>
    @endif

    <aside
        class="fixed top-0 left-0 h-full bg-gradient-to-b from-blue-600 via-blue-500 to-cyan-500 text-white z-50 transition-all duration-300 ease-in-out shadow-2xl flex flex-col {{ $mobileOpen ? 'translate-x-0' : '-translate-x-full' }} lg:translate-x-0 {{ $isCollapsed ? 'lg:w-20' : 'lg:w-64' }}"
    >
        <div class="flex items-center justify-between p-4 border-b border-blue-400 border-opacity-30">
            <div class="flex items-center space-x-2 {{ $isCollapsed ? 'lg:justify-center lg:w-full' : '' }}">
                <div class="bg-white rounded-lg p-2">
                    <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                </div>
                @unless($isCollapsed)
                    <span class="font-bold text-lg hidden lg:inline">App IA</span>
                @endunless
            </div>
            <div class="flex items-center gap-2">
                <button
                    wire:click="toggleMobile"
                    class="lg:hidden text-white hover:text-cyan-200 transition-colors"
                >
                    <i class="fas {{ $mobileOpen ? 'fa-times' : 'fa-bars' }} text-xl"></i>
                </button>
                <button
                    wire:click="toggle"
                    class="hidden lg:flex items-center justify-center w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition"
                >
                    <i class="fas fa-chevron-{{ $isCollapsed ? 'right' : 'left' }}"></i>
                </button>
            </div>
        </div>

        @if($user)
            <div class="px-4 py-3 border-b border-blue-400/20">
                @if($isCollapsed)
                    <div class="flex justify-center">
                        <div class="w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center font-semibold">
                            {{ Str::upper(Str::substr($user->persona?->nombres ?? $user->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                @else
                    <p class="font-semibold text-white">
                        {{ trim(($user->persona?->nombres ?? '') . ' ' . ($user->persona?->apellidos ?? '')) ?: $user->name }}
                    </p>
                    <p class="text-sm text-blue-100">{{ $user->role?->name ?? 'Rol no asignado' }}</p>
                    <p class="text-xs text-blue-100">
                        {{ $user->institucion->nombre ?? $user->persona?->institucion?->nombre ?? 'Sin institución' }}
                    </p>
                @endif
            </div>
        @endif

        <nav class="mt-4 px-2 flex-1 overflow-y-auto">
            @php
                $linkClasses = fn($route) => 'w-full flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 transition-all hover:bg-blue-700 hover:bg-opacity-50 ' .
                    (request()->routeIs($route) ? 'bg-blue-700 bg-opacity-70 shadow-lg ' : '') .
                    ($isCollapsed ? 'lg:justify-center' : '');
            @endphp

            <a href="{{ route('dashboard') }}" wire:click="closeMobile" class="{{ $linkClasses('dashboard') }}">
                <i class="fas fa-home text-xl"></i>
                @unless($isCollapsed)
                    <span class="flex-1 text-left font-medium">Dashboard</span>
                @endunless
            </a>

            @if($abilities['manageUsers'])
                <a href="{{ route('usuarios') }}" wire:click="closeMobile" class="{{ $linkClasses('usuarios') }}">
                    <i class="fas fa-users text-xl"></i>
                    @unless($isCollapsed)
                        <span class="flex-1 text-left font-medium">Usuarios</span>
                    @endunless
                </a>
            @endif

            @if($abilities['manageInstitutions'])
                <a href="{{ route('instituciones') }}" wire:click="closeMobile" class="{{ $linkClasses('instituciones') }}">
                    <i class="fas fa-building text-xl"></i>
                    @unless($isCollapsed)
                        <span class="flex-1 text-left font-medium">Instituciones</span>
                    @endunless
                </a>
            @endif

            <div>
                <button
                    wire:click="toggleSubmenu('datos-pedagogicos')"
                    class="{{ $linkClasses('') }}"
                >
                    <i class="fas fa-book text-xl"></i>
                    @unless($isCollapsed)
                        <span class="flex-1 text-left font-medium">Datos Pedagógicos</span>
                        <i class="fas fa-chevron-{{ $openSubmenu === 'datos-pedagogicos' ? 'down' : 'right' }} text-sm transition-transform"></i>
                    @endunless
                </button>

                    @if(!$isCollapsed && $openSubmenu === 'datos-pedagogicos')
                    <div class="ml-4 mt-2 space-y-1 border-l-2 border-blue-400 border-opacity-30 pl-4">
                        <a href="{{ route('inscripciones') }}" wire:click="closeMobile" class="w-full flex items-center space-x-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-700 hover:bg-opacity-50 {{ request()->routeIs('inscripciones') ? 'bg-blue-700 bg-opacity-70' : '' }}">
                            <i class="fas fa-clipboard-list text-sm"></i>
                            <span class="text-sm font-medium">Inscripciones</span>
                        </a>
                        <a href="{{ route('asistencia') }}" wire:click="closeMobile" class="w-full flex items-center space-x-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-700 hover:bg-opacity-50 {{ request()->routeIs('asistencia') ? 'bg-blue-700 bg-opacity-70' : '' }}">
                            <i class="fas fa-calendar-check text-sm"></i>
                            <span class="text-sm font-medium">Asistencia</span>
                        </a>
                        <a href="{{ route('notas') }}" wire:click="closeMobile" class="w-full flex items-center space-x-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-700 hover:bg-opacity-50 {{ request()->routeIs('notas') ? 'bg-blue-700 bg-opacity-70' : '' }}">
                            <i class="fas fa-star text-sm"></i>
                            <span class="text-sm font-medium">Notas</span>
                        </a>
                    </div>
                @endif
            </div>

            <div>
                <button
                    wire:click="toggleSubmenu('inteligencia-artificial')"
                    class="{{ $linkClasses('') }}"
                >
                    <i class="fas fa-robot text-xl"></i>
                    @unless($isCollapsed)
                        <span class="flex-1 text-left font-medium">Inteligencia Artificial</span>
                        <i class="fas fa-chevron-{{ $openSubmenu === 'inteligencia-artificial' ? 'down' : 'right' }} text-sm transition-transform"></i>
                    @endunless
                </button>

                @if(!$isCollapsed && $openSubmenu === 'inteligencia-artificial')
                    <div class="ml-4 mt-2 space-y-1 border-l-2 border-blue-400 border-opacity-30 pl-4">
                        <a href="{{ route('prediccion') }}" wire:click="closeMobile" class="w-full flex items-center space-x-3 px-4 py-2 rounded-lg transition-all hover:bg-blue-700 hover:bg-opacity-50 {{ request()->routeIs('prediccion') ? 'bg-blue-700 bg-opacity-70' : '' }}">
                            <i class="fas fa-chart-line text-sm"></i>
                            <span class="text-sm font-medium">Predicción del rendimiento</span>
                        </a>
                    </div>
                @endif
            </div>
        </nav>

        <div class="p-4 border-t border-blue-400 border-opacity-30">
            <button
                wire:click="logout"
                class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 transition-all {{ $isCollapsed ? 'lg:justify-center' : '' }}"
            >
                <i class="fas fa-sign-out-alt text-xl"></i>
                @unless($isCollapsed)
                    <span class="font-medium">Cerrar Sesión</span>
                @endunless
            </button>
        </div>
    </aside>

    <button
        wire:click="toggleMobile"
        class="fixed top-4 left-4 z-40 bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-3 rounded-lg shadow-lg hover:shadow-xl transition-all lg:hidden {{ $mobileOpen ? 'opacity-0 pointer-events-none' : '' }}"
    >
        <i class="fas fa-bars text-xl"></i>
    </button>
</div>
