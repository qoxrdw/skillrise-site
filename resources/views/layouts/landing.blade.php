<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' https://cdn.jsdelivr.net {{ config('app.env') == 'local' ? 'http://localhost:3000' : '' }} 'unsafe-eval';
        style-src 'self' https://cdn.jsdelivr.net https://fonts.bunny.net 'unsafe-inline';
        font-src 'self' https://fonts.bunny.net;
        connect-src 'self' {{ config('app.env') == 'local' ? 'ws://localhost:3000' : '' }};
    ">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=work-sans:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900 overflow-x-hidden">
    @yield('content')
</body>
</html>
