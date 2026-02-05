@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white px-8 pt-8 pb-20 font-sans">
        <div class="max-w-[1000px]">

            {{-- Навигация (Back link) --}}
            <div class="mb-8">
                <a href="{{ route('tracks.index') }}" class="inline-flex items-center gap-2 text-[18px] text-black/50 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Назад к моим трекам') }}
                </a>
            </div>

            {{-- Заголовок страницы (Стиль как синий бокс, но белый) --}}
            <div class="mb-12 bg-white border border-black rounded-[30px] p-10 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-[16px] font-medium text-black/40 uppercase tracking-[0.2em] mb-3">
                        {{ __('System Updates') }}
                    </div>
                    <h1 class="text-[48px] font-normal text-black leading-tight mb-4">
                        {{ __('Уведомления') }}
                    </h1>
                </div>
            </div>

            {{-- Заглушка (Empty State) --}}
            <div class="h-[400px] border border-black border-dashed rounded-[30px] flex flex-col items-center justify-center p-10 text-center">
                {{-- Иконка колокольчика или письма --}}
                <div class="w-20 h-20 border border-black rounded-full flex items-center justify-center mb-6">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>

                <div class="max-w-[450px]">
                    <h3 class="text-[24px] font-normal text-black mb-3">
                        {{ __('Пока уведомлений нет') }}
                    </h3>
                    <p class="text-[18px] text-black/40 font-light italic">
                        {{ __('Но если у нас будет важное объявление или новости по вашим трекам, вы обязательно узнаете об этом здесь.') }}
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
