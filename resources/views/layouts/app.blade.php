<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com {{ config('app.env') == 'local' ? 'http://localhost:3000' : '' }} 'unsafe-eval' 'unsafe-inline';

    {{-- ⚠️ КРИТИЧНО: ДОБАВЛЕНЫ ДОМЕНЫ GOOGLE FONTS --}}
    style-src 'self' https://cdn.jsdelivr.net https://fonts.bunny.net https://fonts.googleapis.com 'unsafe-inline';
    font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com;

    connect-src 'self' {{ config('app.env') == 'local' ? 'ws://localhost:3000' : '' }};
">

    <title>{{ config('app.name', 'SkillRise') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=work-sans:400,500,600&display=swap" rel="stylesheet" />

    <!-- Замените существующую ссылку на Google Fonts в <head> на эту -->

    <!-- ПОДКЛЮЧЕНИЕ ВСЕХ ШРИФТОВ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900" x-data="{ sidebarOpen: false }">
<div class="min-h-screen flex">
    {{-- Sidebar with Figma design --}}
    <aside class="w-[614px] h-[1333px] bg-black/10 fixed inset-y-0 left-0 top-[-53px] z-40 rounded-[15px] transform transition-transform" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="pt-8 md:pt-12 px-6 md:px-24">
            <!-- Logo -->
            <div class="mb-8 md:mb-16">
                <x-logo class="w-16 h-10 md:w-[89px] md:h-[59px]" />
            </div>

            <!-- Navigation -->
            <nav class="space-y-2 md:space-y-4">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="group relative block text-lg md:text-[25px] leading-tight md:leading-[29px] font-normal text-black py-3 md:py-4 px-4 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-white/20 hover:shadow-lg hover:text-gray-900 active:scale-95 active:bg-white/30 {{ request()->routeIs('dashboard') ? 'font-medium bg-white/10 shadow-md' : '' }}">
                    <span class="relative z-10">{{ __('Главная') }}</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
                </x-nav-link>


                <div class="relative">
                    <x-nav-link :href="route('tracks.index')" :active="request()->routeIs('tracks.*')"
                        class="group relative flex items-center justify-between text-lg md:text-[25px] leading-tight md:leading-[29px] font-normal text-black py-3 md:py-4 px-4 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-white/20 hover:shadow-lg hover:text-gray-900 active:scale-95 active:bg-white/30 {{ request()->routeIs('tracks.*') ? 'font-medium bg-white/10 shadow-md' : '' }}">
                        <span class="relative z-10">{{ __('Мои треки') }}</span>
                        <svg class="w-3 h-3 md:w-4 md:h-4 text-black/80 transform transition-all duration-300 ease-in-out group-hover:text-black group-hover:scale-110 {{ request()->routeIs('tracks.*') ? 'rotate-0' : '-rotate-90' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
                    </x-nav-link>
                </div>

                <x-nav-link :href="route('tracks.sharing')" :active="request()->routeIs('tracks.sharing')"
                    class="group relative flex items-center justify-between text-lg md:text-[25px] leading-tight md:leading-[29px] font-normal text-black py-3 md:py-4 px-4 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-white/20 hover:shadow-lg hover:text-gray-900 active:scale-95 active:bg-white/30 {{ request()->routeIs('tracks.sharing') ? 'font-medium bg-white/10 shadow-md' : '' }}">
                    <span class="relative z-10">{{ __('Шеринг') }}</span>
                    <svg class="w-3 h-3 md:w-4 md:h-4 text-black/80 transform transition-all duration-300 ease-in-out group-hover:text-black group-hover:scale-110 -rotate-90" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
                </x-nav-link>
            </nav>
        </div>
    </aside>

    {{-- Overlay for mobile when sidebar open --}}
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 bg-black/40 z-30 md:hidden" @click="sidebarOpen=false"></div>

    {{-- Main area --}}
    <div class="flex-1 flex flex-col ml-0 md:ml-[614px]">
        <!-- Header removed for dashboard, content is handled in dashboard.blade.php -->
        @if (!request()->routeIs('dashboard'))
        <header class="h-16 flex items-center justify-between px-6 border-b border-gray-200 bg-white">
            @if (request()->routeIs('tracks.show'))
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
                <button type="button" class="md:hidden h-9 w-9 rounded-full border border-gray-300 flex items-center justify-center" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
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
        @endif

        <main class="flex-1 {{ request()->routeIs('dashboard') ? '' : 'p-0' }} bg-white">
            @yield('content')
        </main>
    </div>
</div>

@yield('scripts')
</body>
</html>
