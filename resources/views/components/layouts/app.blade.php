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
        });
    </script>
    @stack('scripts')
</body>
</html>


