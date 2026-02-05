@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white">

        {{-- Основной контейнер с отступом слева (ml-20) --}}
        <div class="w-full max-w-[1200px] ml-20 md:ml-40 px-4 pt-10 pb-8">

            {{-- Верхняя панель: Поиск (укороченный) + Смайлик --}}
            <div class="flex items-center gap-10 mb-32">
                {{-- Поисковая строка: теперь имеет ограничение по ширине max-w-[550px] --}}
                <div class="w-full max-w-[800px] relative">
                    <form method="GET" action="{{ route('tracks.index') }}" class="relative">
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Поиск треков, упражнений"
                               class="w-full h-[61px] px-8 text-[28px] border-2 border-black rounded-[30px] outline-none focus:ring-0 placeholder-black/60 font-normal">
                        <button type="submit" class="absolute right-6 top-1/2 -translate-y-1/2">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Кнопка-смайлик (Профиль) --}}
                <a href="{{ route('profile.edit') }}"
                   class="w-[61px] h-[61px] border-2 border-black rounded-full flex items-center justify-center hover:bg-black/5 transition-all duration-300 transform hover:scale-105 active:scale-95">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-black">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                </a>
            </div>

            {{-- Блок приветствия --}}
            <div class="mb-16">
                @auth
                    <h1 class="text-4xl md:text-6xl lg:text-[83px] leading-tight font-normal text-black mb-10 max-w-4xl">
                        Добро пожаловать, {{ Auth::user()->name }}!
                    </h1>
                @else
                    <h1 class="text-4xl md:text-6xl lg:text-[83px] leading-tight font-normal text-black mb-10 max-w-4xl">
                        Добро пожаловать, гость!
                    </h1>
                @endauth

                <div class="max-w-2xl text-lg md:text-2xl lg:text-[30px] leading-tight text-black/60 mb-20">
                    <p class="mb-4">SkillRise поможет организовать ваше самообучение в одном месте.</p>
                    <p>Обучение — это ваш путь к росту, и мы поможем вам пройти его уверенно.</p>
                </div>

                {{-- Кнопка "Как пользоваться" (rounded-12px) --}}
                <div class="flex">
                    <button class="group relative w-full max-w-[415px] h-[61px] border-2 border-gray-500 rounded-[12px] flex items-center justify-center text-[28px] font-normal text-black/60 transition-all duration-300 hover:scale-105 hover:bg-black/5 hover:text-black">
                        Как пользоваться?
                    </button>
                </div>
            </div>
        </div>

        {{-- Плавающая кнопка "+" --}}
        <div class="absolute bottom-8 right-8">
            <a href="{{ route('tracks.index') }}" class="w-[89px] h-[89px] border-2 border-black rounded-full flex items-center justify-center text-[54px] font-normal text-black transition-all duration-300 hover:scale-110 hover:bg-black hover:text-white hover:rotate-90 active:scale-95">
                +
            </a>
        </div>
    </div>
@endsection
