@extends('layouts.app')

@section('content')

    {{-- Главный контейнер. Используем flex для центрирования и вертикального потока. --}}
    <div class="flex justify-center min-h-screen bg-white pt-10 pb-20">

        {{-- Основной контент (Максимальная ширина 960px) --}}
        <div class="w-full max-w-[960px] px-4 space-y-8">

            {{-- Сообщения об успехе/ошибке --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Заголовок страницы --}}
            <div class="w-full py-4">
                <h1 class="text-[34px] leading-tight text-black/90 font-normal">{{ __('Общедоступные треки') }}</h1>
                <p class="mt-1 text-base text-black/60">{{ __('Откройте для себя треки других пользователей и добавьте их к себе') }}</p>
            </div>

            {{-- Поисковая строка --}}
            <div class="w-full">
                <form method="GET" action="{{ route('tracks.sharing') }}" class="relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Поиск общедоступных треков"
                           class="w-full h-[61px] px-6 text-[30px] leading-[35px] border-2 border-black rounded-[30px] outline-none focus:ring-0 placeholder-black/40 font-normal">
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

            {{-- Список треков --}}
            @if($tracks->isEmpty())
                <div class="w-full h-[112px] text-center p-10 bg-white rounded-[30px] border-2 border-dashed border-gray-300 flex flex-col items-center justify-center">
                    <h3 class="mt-2 text-xl font-medium text-gray-900">{{ __('Пока нет доступных треков для шеринга') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Возможно, вы станете первым, кто поделится своим треком!') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4"> {{-- Используем две колонки для компактности --}}
                    @foreach ($tracks as $track)
                        @if ($track->user)
                            {{-- Дизайн карточки трека --}}
                            <div class="group h-[160px] rounded-[30px] border-2 border-black bg-white p-6 transition-transform hover:shadow-lg flex flex-col justify-between">
                                <div class="flex items-start justify-between gap-4">
                                    {{-- Название трека --}}
                                    <h3 class="text-[26px] leading-8 font-normal text-black/90 line-clamp-2 pr-4">{{ $track->name }}</h3>

                                    {{-- Автор трека --}}
                                    <span class="px-3 h-8 rounded-[20px] border-2 border-black text-sm bg-white flex-shrink-0 flex items-center justify-center text-black/80 font-normal" title="{{ __('Автор') }}">
                                        {{ $track->user->name }}
                                    </span>
                                </div>

                                {{-- Кнопка "Добавить трек" --}}
                                <div class="mt-4">
                                    <form action="{{ route('tracks.clone', $track) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full h-[48px] px-5 rounded-[20px] border-2 border-black bg-black text-white text-lg transition-all hover:bg-white hover:text-black">
                                            {{ __('Добавить трек') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
