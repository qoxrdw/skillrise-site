@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white px-8 pt-8 pb-20 font-sans">
        <div class="max-w-[1000px]">

            {{-- Заголовок страницы (Стиль как в синем боксе, но белый) --}}
            <div class="mb-12 bg-white border border-black rounded-[30px] p-10 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-[16px] font-medium text-black/40 uppercase tracking-widest mb-3">
                        {{ __('Community Library') }}
                    </div>
                    <h1 class="text-[48px] font-normal text-black leading-tight mb-4">
                        {{ __('Общедоступные треки') }}
                    </h1>
                    <p class="text-[20px] text-black/50 max-w-[600px] font-light">
                        {{ __('Коллекция учебных программ, созданных другими пользователями. Вы можете добавить любой трек в свою библиотеку.') }}
                    </p>
                </div>
            </div>

            {{-- Поисковая строка (в стиле вашей главной/show) --}}
            <div class="mb-12">
                <form method="GET" action="{{ route('tracks.sharing') }}" class="relative max-w-[600px]">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 opacity-40">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Поиск по названию трека..."
                           class="w-full h-[65px] pl-16 pr-8 border border-black rounded-full text-[22px] focus:ring-0 focus:border-black placeholder-black/30 transition-all">
                </form>
            </div>

            {{-- Сообщения об успехе --}}
            @if (session('success'))
                <div class="mb-8 p-6 bg-green-50 border border-black rounded-[25px] text-green-800 flex items-center gap-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    <span class="text-[18px]">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Список треков --}}
            @if($tracks->isEmpty())
                <div class="h-[300px] border border-black border-dashed rounded-[30px] flex flex-col items-center justify-center text-black/30">
                    <span class="text-5xl mb-4">🔍</span>
                    <p class="text-[22px] italic">{{ __('Треков по вашему запросу не найдено') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($tracks as $track)
                        @if ($track->user)
                            <div class="group border border-black rounded-[30px] p-8 flex flex-col justify-between hover:bg-gray-50 transition-all min-h-[240px]">
                                <div>
                                    <div class="flex items-center justify-between mb-6">
                                        {{-- Автор трека --}}
                                        <div class="flex items-center gap-2">
                                            <span class="text-[14px] text-black/40 uppercase tracking-widest">{{ $track->user->name }}</span>
                                        </div>

                                        {{-- Статистика справа под ID --}}
                                        <div class="text-right">
                                            <div class="text-[12px] text-black/30 font-medium mb-1">
                                                ID: #{{ $track->id }}
                                            </div>
                                            <div class="text-[11px] text-black/40 uppercase tracking-tighter space-y-0.5">
                                                <div class="flex items-center justify-end gap-1">
                                                    <span>{{ $track->notes_count ?? $track->notes->count() }} заметок</span>
                                                </div>
                                                <div class="flex items-center justify-end gap-1">
                                                    <span>{{ $track->exercises_count ?? $track->exercises->count() }} упражнений</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Название трека --}}
                                    <h3 class="text-[28px] leading-tight font-normal text-black line-clamp-2 mb-8 group-hover:underline">
                                        {{ $track->name }}
                                    </h3>
                                </div>

                                {{-- Кнопка "Добавить к себе" --}}
                                <form action="{{ route('tracks.clone', $track) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="h-[55px] px-8 rounded-full border border-black bg-white text-black text-[18px] font-medium transition-all hover:bg-black hover:text-white flex items-center justify-center gap-3 w-full sm:w-auto">
                                        {{ __('Добавить в библиотеку') }}
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
