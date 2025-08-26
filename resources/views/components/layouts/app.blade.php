<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
    >

    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link
            rel="preconnect"
            href="https://fonts.bunny.net"
    >
    <link
            href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
            rel="stylesheet"
    />
    @fluxAppearance
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

</head>
<body class="h-screen  bg-zinc-100">
@persist('toast')
<flux:toast />
@endpersist
<div class="max-w-7xl mx-auto m-6 sm:px-6 lg:px-8">
{{ $slot }}
</div>
@fluxScripts
@livewireScripts
</body>
</html>
