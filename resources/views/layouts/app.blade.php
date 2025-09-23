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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900">
<div class="min-h-screen flex">
    {{-- Sidebar in minimal style --}}
    <aside class="w-60 bg-gray-100 border-r border-gray-200 fixed inset-y-0 left-0 z-40">
        <div class="h-16 flex items-center px-6 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="text-2xl font-extrabold tracking-tight">SR</a>
        </div>
        <nav class="py-4">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Главная') }}
            </x-nav-link>
            <x-nav-link :href="route('tracks.index')" :active="request()->routeIs('tracks.index')">
                {{ __('Мои треки') }}
            </x-nav-link>
            <x-nav-link :href="route('tracks.sharing')" :active="request()->routeIs('tracks.sharing')">
                {{ __('Шеринг треков') }}
            </x-nav-link>
        </nav>
    </aside>

    {{-- Main area --}}
    <div class="flex-1 flex flex-col ml-60">
        <header class="h-16 flex items-center justify-between px-6 border-b border-gray-200 bg-white">
            @if (request()->routeIs('dashboard') || request()->routeIs('tracks.index') || request()->routeIs('tracks.show'))
                <form method="GET" action="{{ route('tracks.index') }}" class="relative w-full max-w-md">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск треков" class="w-full h-9 pl-9 pr-3 rounded-full border border-gray-300 outline-none focus:ring-0">
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
            @else
                <div></div>
            @endif
            <div class="ml-4 flex items-center gap-3">
                @auth
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="h-9 px-4 rounded-full border border-gray-900 text-sm">Выйти</button>
                    </form>
                @endauth
                <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
            </div>
        </header>

        <main class="flex-1 p-10 bg-white">
            @yield('content')
        </main>
    </div>
</div>

@yield('scripts')
</body>
</html>
