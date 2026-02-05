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

media-src 'self' blob:; {{-- !!! ДОБАВЛЕНА ЭТА СТРОКА !!! --}}

">



    <title>{{ config('app.name', 'SkillRise') }}</title>



    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=work-sans:400,500,600&display=swap" rel="stylesheet" />



    <!-- Замените существующую ссылку на Google Fonts в <head> на эту -->



    <!-- ПОДКЛЮЧЕНИЕ ВСЕХ ШРИФТОВ -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <!-- Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Work Sans', sans-serif;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Alpine.js (необходим для работы списков) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>

<body class="font-sans antialiased bg-white text-gray-900" x-data="{ sidebarOpen: false }">

<div class="min-h-screen flex">

    {{-- Сайдбар --}}
    <aside class="w-[30%] bg-[#EBEBEB] fixed inset-y-0 left-0 z-40 border-r border-black/5">
        <div class="pt-12 px-12">
            {{-- Логотип --}}
            <div class="mb-12 md:mb-20">
                <x-logo class="w-20 h-12 md:w-[120px] md:h-[80px]" />
            </div>

            <nav class="space-y-8"> {{-- Увеличил вертикальный отступ между пунктами --}}
                <a href="{{ route('dashboard') }}" class="block text-[32px] font-normal text-black hover:opacity-70 transition-opacity">
                    Главная
                </a>

                {{-- Секция: Мои треки --}}
                <div x-data="{ open: false }">
                    <div class="flex items-center justify-between group">
                        <a href="{{ route('tracks.index') }}" class="text-[32px] font-normal text-black hover:opacity-70 transition-opacity">
                            Мои треки
                        </a>
                        <button @click="open = !open" class="p-2 hover:bg-black/5 rounded-md transition-colors">
                            <svg :class="{'rotate-0': open, '-rotate-90': !open}" class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="open" x-collapse class="mt-6 ml-8 space-y-5">
                        @auth
                            @foreach(auth()->user()->tracks as $t)
                                <a href="{{ route('tracks.show', $t) }}" class="block text-[24px] text-black/60 hover:text-black transition-colors truncate">
                                    {{ $t->name }}
                                </a>
                            @endforeach
                        @else
                            <span class="block text-[20px] text-black/40 italic">Войдите, чтобы увидеть треки</span>
                        @endauth
                    </div>
                </div>

                <a href="{{ route('tracks.sharing') }}" class="block text-[32px] font-normal text-black hover:opacity-70 transition-opacity">
                    Шеринг
                </a>

                <a href="{{ route('notifications.index') }}" class="block text-[32px] font-normal text-black hover:opacity-70 transition-opacity">
                    Уведомления
                </a>

                <a href="{{ route('profile.edit') }}" class="block text-[32px] font-normal text-black hover:opacity-70 transition-opacity">
                    Профиль
                </a>


            </nav>
        </div>
    </aside>



    {{-- Overlay for mobile when sidebar open --}}

    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 bg-black/40 z-30 md:hidden" @click="sidebarOpen=false"></div>



    <main class="ml-[30%] w-[70%]">
        @yield('content')
    </main>


</div>


@yield('scripts')

</body>

</html>
