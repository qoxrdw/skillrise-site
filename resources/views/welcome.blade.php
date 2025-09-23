@extends('layouts.app')

@section('content')
    <div class="py-16">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl font-extrabold leading-tight">{{ __('Добро пожаловать в SkillRise!') }}</h1>
            <p class="mt-6 text-gray-600 max-w-prose">{{ __('Ваша персональная платформа для организации обучения.') }}</p>

            <div class="mt-8 flex flex-wrap gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="h-9 px-5 rounded-full border border-gray-900 inline-flex items-center">{{ __('Перейти на главную') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="h-9 px-5 rounded-full border border-gray-900 inline-flex items-center">{{ __('Войти') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="h-9 px-5 rounded-full border border-gray-300 inline-flex items-center">{{ __('Зарегистрироваться') }}</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
@endsection
