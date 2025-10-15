@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white">

        {{-- Main Content Area Wrapper (Ограничивает ширину до 960px и центрирует контент) --}}
        {{-- Убрал лишние flex-классы, оставил только центрирование и отступы --}}
        <div class="w-full max-w-[960px] mx-auto px-4 pt-10 pb-8">

            {{-- Поисковая строка --}}
            <div class="relative mb-16">
                <form method="GET" action="{{ route('tracks.index') }}" class="relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Поиск треков, упражнений"
                           class="w-full h-[61px] px-6 text-[30px] leading-[35px] border-2 border-black rounded-[30px] outline-none focus:ring-0 placeholder-black/60 font-normal">
                    <button type="submit" class="absolute right-6 top-1/2 -translate-y-1/2">
                        {{-- SVG-иконка поиска --}}
                        <svg width="33" height="36" viewBox="0 0 33 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.6">
                                <path d="M9.17174 6.86766C14.2884 2.99212 21.5916 3.88454 25.6185 8.93051C29.7087 14.0564 28.8695 21.5273 23.744 25.618C18.6181 29.7085 11.1465 28.8698 7.05575 23.7441L6.86754 23.502C2.9921 18.3853 3.88445 11.0821 8.93039 7.05527L9.17174 6.86766ZM10.2982 8.77238C6.12084 12.1071 5.43747 18.1967 8.77165 22.3748L8.92988 22.5683L9.24729 22.9285C12.6048 26.5474 18.2013 27.0672 22.1774 24.0557L22.1766 24.0563L22.374 23.9013C26.4867 20.6189 27.2146 14.6677 24.0562 10.4971L23.9026 10.2998L23.587 9.92322C20.2498 6.14451 14.5372 5.55778 10.497 8.61752L10.4963 8.61675L10.2982 8.77238Z" fill="black" stroke="black" stroke-width="0.5" stroke-linecap="round"/>
                                <path d="M22.1403 27.2185C21.4307 26.399 21.5198 25.1594 22.3393 24.4498V24.4498C23.1588 23.7402 24.3984 23.8292 25.108 24.6488L31.427 31.9464C32.1366 32.7659 32.0475 34.0055 31.228 34.7151V34.7151C30.4085 35.4247 29.1689 35.3356 28.4593 34.5161L22.1403 27.2185Z" fill="black"/>
                            </g>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="px-4 mb-16"> {{-- Убран text-center --}}
                @auth
                    {{-- Убран mx-auto, добавлен text-left --}}
                    <h1 class="text-4xl md:text-6xl lg:text-[83px] leading-tight md:leading-[97px] font-normal text-black mb-8 max-w-4xl text-left">
                        Добро пожаловать, {{ Auth::user()->name }}!
                    </h1>
                @else
                    {{-- Убран mx-auto, добавлен text-left --}}
                    <h1 class="text-4xl md:text-6xl lg:text-[83px] leading-tight md:leading-[97px] font-normal text-black mb-8 max-w-4xl text-left">
                        Добро пожаловать, гость!
                    </h1>
                @endauth

                {{-- Убран mx-auto из блока описания --}}
                <div class="max-w-2xl text-lg md:text-2xl lg:text-[30px] leading-tight md:leading-[35px] text-black/60 mb-12 md:mb-16">
                    <p class="text-left mb-4">SkillRise поможет организовать ваше самообучение в одном месте.</p>
                    <p class="text-left">Обучение — это ваш путь к росту, и мы поможем вам пройти его уверенно.</p>
                </div>

                {{-- Убран justify-center из блока кнопки --}}
                <div class="flex">
                    <button class="group relative w-full max-w-[415px] h-12 md:h-[61px] border-2 border-black rounded-[30px] flex items-center justify-center text-lg md:text-2xl lg:text-[30px] leading-tight md:leading-[35px] font-normal text-black/60 transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-black/5 hover:text-black hover:shadow-lg active:scale-95 active:bg-black/10">
                        <span class="relative z-10">Как пользоваться?</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-black/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-[30px]"></div>
                    </button>
                </div>
            </div>
        </div>

        <div class="absolute bottom-4 right-4 md:bottom-8 md:right-8">
            <a href="{{ route('tracks.index') }}" class="group relative w-16 h-16 md:w-[89px] md:h-[89px] border-2 border-black rounded-full flex items-center justify-center text-3xl md:text-[54px] font-normal text-black transition-all duration-300 ease-in-out transform hover:scale-110 hover:bg-black hover:text-white hover:shadow-xl hover:rotate-90 active:scale-95 active:rotate-180">
                <span class="relative z-10 transition-all duration-300 group-hover:scale-125">+</span>
                <div class="absolute inset-0 bg-gradient-to-br from-transparent via-black/10 to-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></div>
            </a>
        </div>
    </div>
@endsection
