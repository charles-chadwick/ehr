<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
    >

    <title>Laravel</title>

    <!-- Fonts -->
    <link
            rel="preconnect"
            href="https://fonts.bunny.net"
    >

    @fluxAppearance
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-screen  bg-zinc-100">
@persist('toast')
<flux:toast position="top end" />
@endpersist
<div class="m-6 mx-auto mt-4 sm:px-6 lg:px-8">
    {{ $slot }}
</div>
@fluxScripts
@livewireScripts
</body>
</html>
