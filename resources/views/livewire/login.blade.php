<div class="min-h-screen bg-gradient-to-br from-blue-500 via-blue-400 to-cyan-300 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-sm bg-opacity-95">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-full mb-4">
                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Bienvenido</h1>
                <p class="text-gray-600">Inicia sesión en tu cuenta</p>
            </div>

            @if($error)
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    {{ $error }}
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>
                        Email
                    </label>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                        placeholder="tu@email.com"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>
                        Contraseña
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showPassword ? 'text' : 'password' }}"
                            wire:model="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none pr-12"
                            placeholder="••••••••"
                            required
                        />
                        <button
                            type="button"
                            wire:click="togglePassword"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-500 transition-colors"
                        >
                            <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove>
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Iniciando sesión...
                    </span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <p>¿Olvidaste tu contraseña?</p>
            </div>
        </div>
    </div>
</div>
