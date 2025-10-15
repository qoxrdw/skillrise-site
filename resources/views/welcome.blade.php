@extends('layouts.landing')

@section('content')
<div class="relative min-h-screen w-full bg-white">
    <!-- Logo -->
    <div class="absolute top-6 left-6 md:top-12 md:left-24">
        <x-logo class="w-[60px] h-[40px] md:w-[89px] md:h-[59px]" />
    </div>

    <!-- Main Content Container -->
    <div class="flex flex-col items-center justify-center min-h-screen px-4 pt-20 md:pt-0">
        <!-- Main Title -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-6xl lg:text-[83px] leading-tight md:leading-[97px] font-normal text-black mb-6 max-w-[850px]">
                {{ __('Добро пожаловать в SkillRise!') }}
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl lg:text-[37px] leading-tight md:leading-[43px] font-normal text-black max-w-[850px] px-4">
                {{ __('Ваша персональная платформа для организации обучения') }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mt-8 md:mt-12 w-full max-w-4xl px-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="w-full sm:w-[415px] h-16 md:h-[112px] border-2 border-black rounded-[20px] flex items-center justify-center text-lg md:text-2xl lg:text-[33px] leading-tight md:leading-[39px] font-normal text-black hover:bg-gray-50 transition-colors">
                        {{ __('Перейти на главную') }}
                    </a>
                @else
                    <!-- Login Button -->
                    <a href="{{ route('login') }}"
                       class="w-full sm:w-[415px] h-16 md:h-[112px] border-2 border-black rounded-[20px] flex items-center justify-center text-lg md:text-2xl lg:text-[33px] leading-tight md:leading-[39px] font-normal text-black hover:bg-gray-50 transition-colors">
                        {{ __('Войти') }}
                    </a>

                    @if (Route::has('register'))
                        <!-- Register Button -->
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-[415px] h-16 md:h-[112px] bg-gray-300/60 border-2 border-black rounded-[20px] flex items-center justify-center text-lg md:text-2xl lg:text-[33px] leading-tight md:leading-[39px] font-normal text-black hover:bg-gray-300/80 transition-colors">
                            {{ __('Зарегистрироваться') }}
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</div>
@endsection
