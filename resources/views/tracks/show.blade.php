@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white ml-20 px-8 pt-8 pb-20">
        {{-- Header с поиском и профилем (в точности по вашему шаблону) --}}
        <div class="flex items-center justify-between mb-16 max-w-[1000px]">
            <div class="flex-1 flex justify-center">
                <div class="relative w-full max-w-[450px]">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 opacity-60">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <form method="GET" action="{{ route('tracks.index') }}">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по трекам"
                               class="w-full h-[50px] pl-14 pr-6 border border-black rounded-full text-[18px] focus:ring-0 focus:border-black placeholder-black/50">
                    </form>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="w-[61px] h-[61px] border-2 border-black rounded-full flex items-center justify-center hover:bg-black/5 transition-all">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
            </a>
        </div>

        <div class="max-w-[1000px]" x-data="{ openCreation: true, openMaterials: true }">

            {{-- Секция уведомлений --}}
            @if (session('success'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-4"
                     class="fixed top-8 left-1/2 -translate-x-1/2 z-[100] w-full max-w-[500px] px-4">

                    <div class="bg-white border border-black rounded-[20px] p-5 shadow-xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-black rounded-full flex items-center justify-center shrink-0">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                            </div>
                            <span class="text-[18px] font-medium text-black">{{ session('success') }}</span>
                        </div>

                        <button @click="show = false" class="text-black/30 hover:text-black transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6L6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Главная карточка Трека (Голубая) --}}
            <div class="bg-[#C7E2FF] border border-black rounded-[30px] p-8 relative overflow-hidden mb-12">
                <div class="absolute right-8 top-8">
                    <form action="{{ route('tracks.toggle-publish', $track) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="h-[45px] px-6 border border-black rounded-[15px] bg-white text-black hover:bg-black hover:text-white transition-all text-[16px] font-medium flex items-center gap-2">

                            @if($track->is_public)
                                {{-- Иконка "зачеркнутый глаз" для снятия с публикации --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                Снять с публикации
                            @else
                                {{-- Иконка "внешняя ссылка" для публикации --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                Опубликовать
                            @endif
                        </button>
                    </form>
                </div>
                <div class="text-[20px] text-black/60 mb-8 uppercase tracking-wide">Трек ({{$track->is_public ? "публичный" : "приватный"}})</div>
                <h1 class="text-[48px] font-normal text-black mb-10 leading-tight">{{ $track->name }}</h1>

                <div class="flex items-center gap-12">
                    <div class="flex items-center gap-3 text-[20px]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        {{ $track->notes->count() }} заметок
                    </div>
                    <div class="flex items-center gap-3 text-[20px]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                        {{ $track->exercises->count() }} упражнений
                    </div>
                </div>
            </div>

            {{-- Секция Создание --}}
            <div class="mb-10" x-data="{ activeMenu: null }">
                <button @click="openCreation = !openCreation" class="flex items-center gap-3 text-[24px] font-medium mb-6 outline-none">
                    <svg :class="{'rotate-0': openCreation, '-rotate-90': !openCreation}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Создание
                </button>

                <div x-show="openCreation" x-collapse>
                    <div class="grid grid-cols-2 gap-6 items-start">

                        {{-- Кнопка: ЗАМЕТКИ --}}
                        <div class="relative">
                            <button @click="activeMenu === 'notes' ? activeMenu = null : activeMenu = 'notes'"
                                    :class="activeMenu === 'notes' ? 'bg-black text-white' : 'text-black/60 bg-white'"
                                    class="w-full h-[80px] border border-black rounded-[25px] flex items-center justify-between px-8 text-[22px] transition-colors hover:bg-black hover:text-white group">
                                Заметки
                                <span class="text-3xl font-light transition-transform" :class="activeMenu === 'notes' ? 'rotate-45' : ''">+</span>
                            </button>

                            {{-- Выпадающее меню заметок --}}
                            <div x-show="activeMenu === 'notes'"
                                 x-transition
                                 @click.away="activeMenu = null"
                                 class="absolute z-10 w-full mt-3 bg-white border border-black rounded-[25px] overflow-hidden shadow-xl">
                                <a href="{{ route('notes.create', ['track' => $track->id]) }}" class="flex items-center px-8 py-4 text-[18px] hover:bg-gray-100 border-b border-gray-100">
                                    <span class="mr-3 text-xl">📝</span> Создать текстовую заметку
                                </a>
                                <a href="{{ route('notes.create.handwriting', ['track' => $track->id]) }}" class="flex items-center px-8 py-4 text-[18px] hover:bg-gray-100 border-b border-gray-100">
                                    <span class="mr-3 text-xl">🎨</span> Создать рукописную заметку
                                </a>
{{--                                <a href="{{ route('notes.create.voice', ['track' => $track->id]) }}" class="flex items-center px-8 py-4 text-[18px] hover:bg-gray-100">--}}
{{--                                    <span class="mr-3 text-xl">🎙️</span> Создать голосовую заметку--}}
{{--                                </a>--}}
                            </div>
                        </div>

                        {{-- Кнопка: УПРАЖНЕНИЯ --}}
                        <div class="relative">
                            <button @click="activeMenu === 'exercises' ? activeMenu = null : activeMenu = 'exercises'"
                                    :class="activeMenu === 'exercises' ? 'bg-black text-white' : 'text-black/60 bg-white'"
                                    class="w-full h-[80px] border border-black rounded-[25px] flex items-center justify-between px-8 text-[22px] transition-colors hover:bg-black hover:text-white group">
                                Упражнения
                                <span class="text-3xl font-light transition-transform" :class="activeMenu === 'exercises' ? 'rotate-45' : ''">+</span>
                            </button>

                            {{-- Выпадающее меню упражнений --}}
                            <div x-show="activeMenu === 'exercises'"
                                 x-transition
                                 @click.away="activeMenu = null"
                                 class="absolute z-10 w-full mt-3 bg-white border border-black rounded-[25px] overflow-hidden shadow-xl">
                                <a href="{{ route('exercises.create', ['track' => $track->id]) }}" class="flex items-center px-8 py-4 text-[18px] hover:bg-gray-100 border-b border-gray-100">
                                    <span class="mr-3 text-xl">✍️</span> Создать упражнение вручную
                                </a>
                                <a href="{{ route('exercises.create-ai', ['track' => $track->id]) }}" class="flex items-center px-8 py-4 text-[18px] hover:bg-black hover:text-white bg-black/5 font-medium transition-colors">
                                    <span class="mr-3 text-xl">✨</span> Создать упражнение с AI на основе заметки
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mb-10">
                <button @click="openMaterials = !openMaterials" class="flex items-center gap-3 text-[24px] font-medium mb-6 outline-none">
                    <svg :class="{'rotate-0': openMaterials, '-rotate-90': !openMaterials}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                    Материалы
                </button>

                <div x-show="openMaterials" x-collapse class="space-y-4 pb-20">
                    @php
                        $max = max($track->notes->count(), $track->exercises->count());
                    @endphp

                    @for($i = 0; $i < $max; $i++)
                        <div class="flex gap-4">
                            {{-- Заметка (Левая часть) --}}
                            @if(isset($track->notes[$i]))
                                <div class="flex-1 relative group">
                                    <a href="{{ route('notes.edit', ['track' => $track->id, 'note' => $track->notes[$i]->id]) }}"
                                       class="flex h-[85px] border border-black rounded-[25px] items-center justify-between px-8 hover:bg-gray-50 transition-colors cursor-pointer">
                                        <div>
                                            <div class="text-[20px] text-black group-hover:underline">{{ $track->notes[$i]->getFirstLine() }}</div>
                                            <div class="flex items-center gap-6 mt-1 text-black/50 text-[14px]">
                                    <span class="flex items-center gap-1 italic">
                                        @php
                                            $types = ['text' => 'Текстовая', 'voice' => 'Голосовая', 'handwriting' => 'Рукописная'];
                                            echo $types[$track->notes[$i]->type] ?? 'Обычная';
                                        @endphp
                                        заметка
                                    </span>
                                            </div>
                                        </div>
                                    </a>
                                    {{-- Кнопка удаления заметки --}}
                                    <form action="{{ route('notes.destroy', ['track' => $track->id, 'note' => $track->notes[$i]->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Удалить эту заметку?')"
                                          class="absolute top-1/2 -translate-y-1/2 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:text-red-600 transition-colors">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- Задание (Правая часть 40%) --}}
                            @if(isset($track->exercises[$i]))
                                <div class="w-[40%] relative group">
                                    <a href="{{ route('exercises.take', ['track' => $track->id, 'exercise' => $track->exercises[$i]->id]) }}"
                                       class="flex bg-gray-100 h-[85px] border border-black rounded-[25px] items-center justify-between px-8 hover:bg-black/5 transition-colors cursor-pointer">
                                        <span class="text-[20px] truncate pr-8">{{ $track->exercises[$i]->title }}</span>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                    {{-- Кнопка удаления упражнения --}}
                                    <form action="{{ route('exercises.destroy', ['track' => $track->id, 'exercise' => $track->exercises[$i]->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Удалить это упражнение?')"
                                          class="absolute top-1/2 -translate-y-1/2 right-12 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:text-red-600 transition-colors">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endfor

                    @if($track->notes->isEmpty() && $track->exercises->isEmpty())
                        <div class="h-[85px] border border-black border-dashed rounded-[25px] flex items-center justify-center text-black/40 italic">
                            В этом треке пока пусто
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
