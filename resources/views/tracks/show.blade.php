@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Back link --}}
            <div class="mb-4 md:mb-6">
                <a href="{{ route('tracks.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('К списку треков') }}
                </a>
            </div>

            {{-- Hero Header --}}
            <div class="mb-8 md:mb-10">
                {{-- Добавлен z-10 на Hero Header для создания контекста наложения над другими элементами страницы --}}
                <div class="relative rounded-[24px] border border-gray-200 bg-gradient-to-br from-purple-50 via-blue-50 to-cyan-50 overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 z-0">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-200/30 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-200/30 to-transparent rounded-full blur-2xl"></div>

                    <div class="relative px-6 md:px-10 py-8 md:py-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                            {{-- Left side (Содержимое остается прежним) --}}
                            <div class="flex items-center gap-4">
                                {{-- Icon --}}
                                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 6h12a1 1 0 100-2H4a1 1 0 100 2zM4 11h12a1 1 0 100-2H4a1 1 0 100 2zM4 16h12a1 1 0 100-2H4a1 1 0 100 2z"/>
                                    </svg>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        @if ($track->is_public)
                                            <span class="text-sm font-medium text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Трек</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Трек (приватный)</span>
                                        @endif
                                    </div>

                                    <h1 class="text-[32px] md:text-[40px] leading-tight font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-blue-900 bg-clip-text text-transparent truncate">
                                        {{ $track->name }}
                                    </h1>

                                    {{-- Clean counters (updated) --}}
                                    <div class="mt-3 flex items-center gap-4 text-gray-700 text-lg font-semibold">
                                        <span>{{ $notes->count() }} заметок</span>
                                        <span class="opacity-40">•</span>
                                        <span>{{ $track->exercises->count() }} упражнений</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex items-center gap-3">
                                {{-- Rename --}}
                                <button id="rename-toggle"
                                        class="group relative h-12 px-5 rounded-2xl border border-gray-300 bg-white text-gray-700 hover:border-gray-900 hover:text-gray-900 text-sm font-medium flex items-center gap-2 shadow-sm hover:shadow transition">
                                    Переименовать
                                </button>

                                {{-- Share / unshare --}}
                                @if (!$track->is_public)
                                    <form action="{{ route('tracks.share', $track) }}" method="POST" onsubmit="return confirm('Поделиться треком?');">
                                        @csrf
                                        <button type="submit"
                                                class="h-12 px-5 rounded-2xl bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 text-white text-sm font-medium shadow-lg hover:shadow-xl transition">
                                            Поделиться
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('tracks.unshare', $track) }}" method="POST" onsubmit="return confirm('Сделать приватным?');">
                                        @csrf
                                        <button type="submit"
                                                class="h-12 px-5 rounded-2xl border border-gray-300 bg-white text-gray-700 text-sm font-medium shadow-sm hover:shadow">
                                            Снять с публикации
                                        </button>
                                    </form>
                                @endif

                                {{-- Create note dropdown CONTAINER (Убрали класс relative) --}}
                                <div id="note-menu-container">
                                    <button type="button" id="create-note-toggle"
                                            class="h-12 px-5 rounded-2xl bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 text-sm font-medium flex items-center gap-2 shadow-sm hover:shadow">
                                        Новая заметка
                                        <svg id="note-toggle-icon" class="w-4 h-4 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DROPDOWN: Вынесен из Hero Header, позиционирование FIXED --}}
            <div id="create-note-menu"
                 class="fixed w-56 rounded-2xl border border-gray-200 bg-white shadow-xl z-[99999] hidden">
                <div class="p-1">
                    <a href="{{ route('notes.create', $track) }}"
                       class="flex items-center p-3 text-sm text-gray-700 rounded-xl hover:bg-gray-100 transition">
                        Текстовая заметка
                    </a>

                    <a href="{{ route('notes.create.handwriting', $track) }}"
                       class="flex items-center p-3 text-sm text-gray-700 rounded-xl hover:bg-gray-100 transition">
                        <span class="mr-2">Рукописная заметка</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-600 border border-red-300">BETA</span>
                    </a>

                    <a href="{{ route('notes.create.voice', $track) }}"
                       class="flex items-center p-3 text-sm text-gray-700 rounded-xl hover:bg-gray-100 transition">
                        <span class="mr-2">Голосовая заметка</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-600 border border-blue-300">NEW</span>
                    </a>
                </div>
            </div>

            {{-- Rename form (Остается прежним) --}}
            <form id="rename-form" method="POST" action="{{ route('tracks.update', $track) }}"
                  class="hidden mb-6 md:mb-8 items-center gap-2">
                @csrf
                @method('PATCH')

                <input type="text" name="name" value="{{ old('name', $track->name) }}"
                       class="h-10 px-4 rounded-2xl border border-gray-300 w-full md:w-96">

                <div class="flex items-center gap-2 ml-0 md:ml-4 mt-3 md:mt-0">
                    <button type="submit" class="h-10 px-4 rounded-2xl bg-black text-white">Сохранить</button>
                    <button id="rename-cancel" type="button"
                            class="h-10 px-4 rounded-2xl border border-gray-300 bg-white text-gray-700 hover:bg-black hover:text-white">
                        Отмена
                    </button>
                </div>
            </form>

            {{-- Success / Errors (Остается прежним) --}}
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-800 flex items-center gap-3 shadow-sm">
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Notes & Exercises (Остается прежним) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Notes (Остается прежним) --}}
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Заметки</h2>
                        <a href="{{ route('notes.create', $track) }}"
                           class="h-9 w-9 rounded-[12px] border border-gray-300 bg-white text-gray-700 hover:bg-gray-900 hover:text-white text-xl flex items-center justify-center">+</a>
                    </div>

                    @if($notes->isEmpty())
                        <div class="p-8 text-center rounded-2xl border border-dashed border-gray-300 bg-white">
                            <p class="text-gray-600">Заметок пока нет.</p>
                        </div>
                    @else
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($notes as $note)
                                @php
                                    $noteType = $note->type ?? 'text';
                                    $isHandwriting = $noteType === 'handwriting';
                                    $isVoice = $noteType === 'voice';
                                    $editRoute = $isVoice ? '#' : (
                                        $isHandwriting
                                            ? route('notes.edit.handwriting', [$track, $note])
                                            : route('notes.edit', [$track, $note])
                                    );
                                    $cardTitle = $isVoice ? 'Голосовая заметка' : ($note->getFirstLine() ?: '(Без названия)');
                                @endphp

                                <li class="group rounded-2xl border border-gray-200 bg-white p-5 hover:border-blue-300 hover:shadow-lg transition">

                                    @if ($isVoice)
                                        <h3 class="text-[18px] text-gray-900 mb-3">{{ $cardTitle }}</h3>

                                        <audio controls class="w-full h-10 bg-gray-100 rounded-lg">
                                            <source src="{{ Storage::url($note->content) }}" type="audio/webm">
                                            <source src="{{ Storage::url($note->content) }}" type="audio/mp4">
                                            Ваш браузер не поддерживает аудио.
                                        </audio>

                                    @else
                                        <a href="{{ $editRoute }}"
                                           class="block text-[18px] text-gray-900 hover:underline truncate">
                                            {{ $cardTitle }}
                                        </a>
                                    @endif

                                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                        <span>{{ $note->created_at->isoFormat('LL') }}</span>

                                        <div class="flex items-center gap-2">
                                            @if (!$isVoice)
                                                <a href="{{ $editRoute }}"
                                                   class="h-8 px-3 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-900 hover:text-white">Редакт.</a>
                                            @endif

                                            <form action="{{ route('notes.destroy', [$track, $note]) }}" method="POST" onsubmit="return confirm('Удалить заметку?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="h-8 px-3 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-red-600 hover:text-white">
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Exercises (Остается прежним) --}}
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Упражнения</h2>
                        <a href="{{ route('exercises.index', $track) }}"
                           class="h-9 w-9 rounded-[12px] border border-gray-300 bg-white text-gray-700 hover:bg-gray-900 hover:text-white text-xl flex items-center justify-center">+</a>
                    </div>

                    @if($track->exercises->isEmpty())
                        <div class="p-8 text-center rounded-2xl border border-dashed border-gray-300 bg-white">
                            <p class="text-gray-600">Упражнений пока нет.</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach($track->exercises as $exercise)
                                <li class="rounded-2xl border border-gray-200 bg-white p-5 hover:border-blue-300 hover:shadow-lg transition">
                                    <div class="flex items-start justify-between gap-4">

                                        <a href="{{ route('exercises.take', [$track, $exercise]) }}"
                                           class="flex-1 text-[18px] md:text-[20px] text-gray-900 hover:underline">
                                            {{ $exercise->title }}
                                        </a>

                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('exercises.take', [$track, $exercise]) }}"
                                               class="h-9 px-3 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-900 hover:text-white text-sm">
                                                Начать
                                            </a>

                                            <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST" onsubmit="return confirm('Удалить упражнение?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="h-9 px-3 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-red-600 hover:text-white text-sm">
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Rename toggle (Остается прежним)
        const renameToggle = document.getElementById('rename-toggle');
        const renameForm = document.getElementById('rename-form');
        const renameCancel = document.getElementById('rename-cancel');

        if (renameToggle && renameForm) {
            renameToggle.addEventListener('click', () => {
                renameToggle.classList.add('hidden');
                renameForm.classList.remove('hidden');
                renameForm.classList.add('flex');
            });
        }

        if (renameCancel) {
            renameCancel.addEventListener('click', () => {
                renameForm.classList.add('hidden');
                renameForm.classList.remove('flex');
                renameToggle.classList.remove('hidden');
            });
        }

        // Dropdown Logic (НОВЫЙ КОД ДЛЯ FIXED ПОЗИЦИОНИРОВАНИЯ)
        const btn = document.getElementById('create-note-toggle');
        const menu = document.getElementById('create-note-menu');
        const icon = document.getElementById('note-toggle-icon');

        /**
         * Рассчитывает и устанавливает позицию меню относительно кнопки.
         * Использует getBoundingClientRect для получения координат на экране.
         */
        function positionMenu() {
            const rect = btn.getBoundingClientRect();
            // 8px - отступ между кнопкой и меню (аналогично mt-2)
            const offset = 8;

            // Устанавливаем положение:
            // top: низ кнопки + отступ
            menu.style.top = `${rect.bottom + offset}px`;

            // left: левый край кнопки + ширина кнопки - ширина меню.
            // Это выравнивает правый край меню по правому краю кнопки.
            menu.style.left = `${rect.left + rect.width - menu.offsetWidth}px`;
        }

        if (btn && menu) {
            btn.addEventListener('click', e => {
                e.stopPropagation();

                const isHidden = menu.classList.toggle('hidden');
                icon.classList.toggle('rotate-180', !isHidden);

                if (!isHidden) {
                    positionMenu(); // Позиционируем при открытии
                }
            });

            // Перепозиционируем при изменении размера окна (для адаптивности)
            window.addEventListener('resize', () => {
                if (!menu.classList.contains('hidden')) {
                    positionMenu();
                }
            });

            // Скрытие при клике вне меню
            document.addEventListener('click', e => {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });
        }
    </script>
@endsection
