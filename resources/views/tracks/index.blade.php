@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Основной контейнер с отступом слева для сайдбара --}}
        <div class="w-full max-w-[1100px] ml-20 px-8 pt-10 pb-20">

            {{-- Верхняя панель: Поиск + Смайлик в разных концах --}}
            <div class="flex items-center justify-between mb-16 w-full"> {{-- Добавлен justify-between --}}

                <div class="w-full max-w-[550px] relative">
                    <form method="GET" action="{{ route('tracks.index') }}" class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск треков, упражнений"
                               class="w-full h-[61px] px-8 text-[24px] border-2 border-black rounded-[30px] outline-none focus:ring-0 placeholder-black/60 font-normal">
                        <button type="submit" class="absolute right-6 top-1/2 -translate-y-1/2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Смайлик теперь будет прижат к правому краю --}}
                <a href="{{ route('profile.edit') }}" class="w-[61px] h-[61px] border-2 border-black rounded-full flex items-center justify-center hover:bg-black/5 transition-all">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                </a>
            </div>

            <div class="space-y-4">
                {{-- Кнопка "Создать трек" по стилю Figma --}}
                <button @click="$dispatch('open-modal', 'create-track-modal')"
                        class="w-full h-[80px] border-2 border-zinc-500 rounded-[25px] flex items-center px-8 text-[28px] text-black/60 hover:bg-gray-50 transition-colors">
                    <span class="mr-6 text-4xl font-light">+</span>
                    <span>Создать трек</span>
                </button>

                @foreach($tracks as $track)
                    <div x-data="{ open: false }" class="border-2 border-black rounded-[30px] overflow-hidden bg-white">
                        {{-- Заголовок трека --}}
                        <div class="w-full h-[85px] flex items-center px-8 transition-colors"
                             :class="open ? 'bg-black/5' : 'bg-white'">

                            {{-- Зона клика для раскрытия (Шеврон) --}}
                            <div @click="open = !open" class="cursor-pointer p-2 -ml-2 hover:bg-black/10 rounded-full transition-all">
                                <svg class="w-8 h-8 transition-transform duration-300"
                                     :class="{ 'rotate-0': open, '-rotate-90': !open }"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>

                            {{-- Название трека — ТЕПЕРЬ КНОПКА/ССЫЛКА --}}
                            <a href="{{ route('tracks.show', $track) }}"
                               class="flex-1 text-[28px] font-normal text-black/90 text-left px-4 hover:underline decoration-1 underline-offset-4">
                                {{ $track->name }}
                            </a>

                            <div class="flex items-center gap-4">
                                {{-- Кнопка УДАЛЕНИЯ --}}
                                <form action="{{ route('tracks.destroy', $track) }}" method="POST" onsubmit="return confirm('Удалить этот трек и все его содержимое?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-black/40 hover:text-red-600 transition-colors">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </form>

                                {{-- Кнопка перехода внутрь (сохраняем для удобства) --}}
                                <a href="{{ route('tracks.show', $track) }}"
                                   class="p-2 hover:bg-black/10 rounded-full transition-colors text-black/60">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div x-show="open" x-collapse class="p-8 border-t-2 border-black/5 space-y-4">
                            @php
                                $notes = $track->notes;
                                $exercises = $track->exercises;
                                $maxCount = max($notes->count(), $exercises->count());
                            @endphp

                            @for($i = 0; $i < $maxCount; $i++)
                                <div class="grid grid-cols-10 gap-4">
                                    {{-- Заметка (левая колонка, 70% ширины) --}}
                                    <div class="col-span-7">
                                        @if(isset($notes[$i]))
                                            <a href="{{ route('notes.edit', ['track' => $track->id, 'note' => $notes[$i]->id]) }}"
                                               class="block w-full h-[80px] border-2 border-black rounded-[25px] flex items-center px-8 text-[22px] hover:bg-gray-50 transition-shadow">
                                                {{ $notes[$i]->getFirstLine() }}
                                            </a>
                                        @endif
                                    </div>

                                    {{-- Упражнение (правая колонка, 30% ширины) --}}
                                    <div class="col-span-3">
                                        @if(isset($exercises[$i]))
                                            <a href="{{ route('exercises.take', ['track' => $track->id, 'exercise' => $exercises[$i]->id]) }}"
                                               class="block w-full h-[80px] bg-black/5 border-2 border-black rounded-[25px] flex items-center justify-between px-6 text-[22px] hover:bg-black/10 transition-colors">
                                                <span class="truncate">{{ $exercises[$i]->title }}</span>
                                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endfor

                            @if($notes->isEmpty() && $exercises->isEmpty())
                                <div class="text-center py-6 text-black/40 text-xl italic">Трек пока пуст</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Модальное окно (оставляем логику как была) --}}
    <x-modal name="create-track-modal" maxWidth="lg">
        <div class="p-8">
            <h3 class="text-2xl font-normal mb-6">Назовите ваш новый путь</h3>
            <form id="create-track-form" method="POST" action="{{ route('tracks.store') }}">
                @csrf
                <input type="text" name="name" required autofocus class="w-full h-[61px] px-6 text-[22px] border-2 border-black rounded-[20px] outline-none" placeholder="Биология, 9 класс">
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="$dispatch('close')" class="h-12 px-6 rounded-[15px] border-2 border-black">Отмена</button>
                    <button type="submit" class="h-12 px-6 rounded-[15px] bg-black text-white">Создать</button>
                </div>
            </form>
        </div>
    </x-modal>
@endsection
