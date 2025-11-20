<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', ({ icon = 'success', title = '', text = '', timer = 2200 }) => {
                Swal.fire({
                    icon,
                    title,
                    text,
                    timer,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            });

            Livewire.on('confirm-delete', ({ id, label = 'registro', event }) => {
                Swal.fire({
                    title: `¿Eliminar ${label}?`,
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(event, { id });
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>


