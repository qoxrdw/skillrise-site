<?php
use Illuminate\Support\Facades\Storage; // Используем для генерации URL аудиофайла
?>
@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Back link --}}
            <div class="mb-4 md:mb-6">
                <a href="{{ route('tracks.index') }}" class="inline-flex items-center text-sm text-black/70 hover:text-black transition">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('К списку треков') }}
                </a>
            </div>

            {{-- Hero header --}}
            <div class="mb-8 md:mb-10">
                {{-- Обводка стала мягче: border-2 border-gray-300 --}}
                <div class="relative rounded-[20px] border-2 border-gray-300 bg-gradient-to-br from-white via-gray-50 to-gray-100">
                    <div class="px-6 md:px-10 py-6 md:py-8">
                        <div class="flex flex-col md:flex-row md:items-center md:gap-6">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-3">
                                    {{-- УДАЛЕНО: ID трека --}}

                                    {{-- Public/Private Pill --}}
                                    @if ($track->is_public)
                                        {{-- Используем более мягкую рамку и бэкграунд для "Опубликован" --}}
                                        <span class="inline-flex items-center px-3 h-8 rounded-full border-2 border-gray-300 text-xs md:text-sm bg-gray-100 text-black/80">{{ __('Опубликован') }}</span>
                                    @else
                                        {{-- Используем более мягкую рамку для "Приватный" --}}
                                        <span class="inline-flex items-center px-3 h-8 rounded-full border-2 border-gray-300 text-xs md:text-sm bg-white text-black/80">{{ __('Приватный') }}</span>
                                    @endif
                                </div>
                                <h1 class="text-[28px] md:text-[36px] leading-tight text-black/90 truncate">{{ $track->name }}</h1>
                                {{-- Stats Pills --}}
                                <div class="mt-3 flex items-center gap-2 text-sm text-black/70">
                                    {{-- Рамки стали мягче: border-2 border-gray-300 --}}
                                    <span class="inline-flex items-center px-3 h-8 rounded-full border-2 border-gray-300 bg-white">{{ __('Заметки') }}: {{ $notes->count() }}</span>
                                    <span class="inline-flex items-center px-3 h-8 rounded-full border-2 border-gray-300 bg-white">{{ __('Упражнения') }}: {{ $track->exercises->count() }}</span>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-2 md:justify-end">

                                {{-- Rename Toggle Button (Вторичная, центрирование) --}}
                                <button id="rename-toggle" type="button" class="h-10 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center">{{ __('Переименовать') }}</button>

                                {{-- Rename Form --}}
                                <form id="rename-form" method="POST" action="{{ route('tracks.update', $track) }}" class="hidden items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" value="{{ old('name', $track->name) }}" class="h-10 px-3 rounded-[12px] border-2 border-gray-300 w-64" required>
                                    {{-- Кнопка "Сохранить" (Основная) --}}
                                    <button type="submit" class="h-10 px-4 rounded-[14px] border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center">{{ __('Сохранить') }}</button>
                                    {{-- Кнопка "Отмена" (Вторичная) --}}
                                    <button id="rename-cancel" type="button" class="h-10 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center">{{ __('Отмена') }}</button>
                                </form>

                                {{-- Share/Unshare Button (Вторичная, центрирование) --}}
                                @if (!$track->is_public)
                                    <form action="{{ route('tracks.share', $track) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('{{ __('Вы уверены, что хотите поделиться этим треком? Он станет виден всем пользователям.') }}');">
                                        @csrf
                                        <button type="submit" class="h-10 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center">{{ __('Поделиться') }}</button>
                                    </form>
                                @else
                                    <form action="{{ route('tracks.unshare', $track) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('{{ __('Снять трек с публикации? Он станет приватным.') }}');">
                                        @csrf
                                        <button type="submit" class="h-10 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center">{{ __('Снять с публикации') }}</button>
                                    </form>
                                @endif

                                {{-- Dropdown для выбора типа заметки --}}
                                <div class="relative group">
                                    <button type="button" id="create-note-toggle" class="h-10 px-4 rounded-[14px] border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center">
                                        {{ __('Новая заметка') }}
                                        <svg class="w-4 h-4 ml-2 transition-transform duration-300 transform" id="note-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div id="create-note-menu" class="absolute right-0 mt-2 w-56 rounded-[14px] border-2 border-black bg-white shadow-xl z-10 hidden origin-top-right">
                                        <div class="p-1">
                                            <a href="{{ route('notes.create', $track) }}" class="flex items-center p-3 text-sm text-black/80 rounded-[10px] hover:bg-gray-100 transition">
                                                {{ __('Текстовая заметка') }}
                                            </a>
                                            <a href="{{ route('notes.create.handwriting', $track) }}" class="flex items-center p-3 text-sm text-black/80 rounded-[10px] hover:bg-gray-100 transition">
                                                <span class="mr-2">{{ __('Рукописная заметка') }}</span>
                                                {{-- Добавляем метку "BETA" или "PROTOTYPE" --}}
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-600 border border-red-300 uppercase leading-none tracking-wider">
        {{ __('BETA') }}
    </span>
                                            </a>
                                            {{-- 🎙️ НОВОЕ: Голосовая заметка --}}
                                            <a href="{{ route('notes.create.voice', $track) }}" class="flex items-center p-3 text-sm text-black/80 rounded-[10px] hover:bg-gray-100 transition">
                                                <span class="mr-2">{{ __('Голосовая заметка') }}</span>
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-600 border border-blue-300 uppercase leading-none tracking-wider">
                                                    {{ __('NEW') }}
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ... (остальной код) --}}

            {{-- Messages (Рамка стала мягче) --}}
            @if (session('success'))
                <div class="mb-6 p-4 border-2 border-gray-300 rounded-[14px] bg-white text-black/80">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-6 p-4 border-2 border-gray-300 rounded-[14px] bg-white text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Content split: Notes and Exercises --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Notes --}}
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl md:text-2xl text-black/90">{{ __('Заметки') }}</h2>
                        {{-- Кнопка "Добавить" (ЗАМЕНЕНА на иконку плюса). Теперь ведет на текстовую заметку, как базовый вариант --}}
                        <a href="{{ route('notes.create', $track) }}" class="h-9 w-9 px-0 rounded-[12px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white text-xl transition flex items-center justify-center">
                            {{-- Иконка плюса --}}
                            +
                        </a>
                    </div>
                    @if($notes->isEmpty())
                        {{-- Рамка стала мягче --}}
                        <div class="p-8 text-center border-2 border-dashed border-gray-300 rounded-[14px] bg-white">
                            <p class="text-black/70">{{ __('Заметок пока нет. Создайте первую.') }}</p>
                        </div>
                    @else
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($notes as $note)
                                @php
                                    // 💡 ИСПРАВЛЕНИЕ: Используем новое поле 'type' для надежного выбора маршрута
                                    $noteType = $note->type ?? 'text';
                                    $isHandwriting = $noteType === 'handwriting';
                                    $isVoice = $noteType === 'voice';

                                    // Определяем правильный маршрут редактирования. Голосовые заметки не редактируются.
                                    $editRoute = $isVoice ? '#' : (
                                        $isHandwriting
                                            ? route('notes.edit.handwriting', [$track, $note])
                                            : route('notes.edit', [$track, $note])
                                    );
                                    // Заголовок для карточки
                                    $cardTitle = $isVoice ? __('Голосовая заметка') : ($note->getFirstLine() ?: __('(Без названия)'));
                                @endphp
                                {{-- Карточки: Рамка стала мягче --}}
                                <li class="group rounded-[14px] border-2 border-gray-300 bg-white p-5 hover:-translate-y-0.5 transition hover:border-black">

                                    @if ($isVoice)
                                        {{-- 🎙️ РЕНДЕР АУДИО ПЛЕЕРА ДЛЯ ГОЛОСОВОЙ ЗАМЕТКИ --}}
                                        <h3 class="block text-[18px] leading-6 text-black/90 truncate mb-4">{{ $cardTitle }}</h3>
                                        {{-- Используем Storage::url() для доступа к файлу, сохраненному на диске 'public' --}}
                                        <audio controls class="w-full h-10 bg-gray-100 rounded-[10px]">
                                            <source src="{{ Storage::url($note->content) }}" type="audio/webm">
                                            <source src="{{ Storage::url($note->content) }}" type="audio/mp4">
                                            {{ __('Ваш браузер не поддерживает элемент аудио.') }}
                                        </audio>
                                    @else
                                        {{-- Текст и рукописные заметки (ссылка на редактирование) --}}
                                        <a href="{{ $editRoute }}" class="block text-[18px] leading-6 text-black/90 truncate">{{ $cardTitle }}</a>
                                    @endif

                                    <div class="mt-3 flex items-center justify-between text-xs text-black/60">
                                        <span>{{ $note->created_at->isoFormat('LL') }}</span>
                                        <div class="flex items-center gap-2">

                                            @if (!$isVoice)
                                                {{-- Кнопка "Редакт." (только для текстовых и рукописных) --}}
                                                <a href="{{ $editRoute }}" class="h-8 px-3 rounded-[10px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center">{{ __('Редакт.') }}</a>
                                            @endif

                                            <form action="{{ route('notes.destroy', [$track, $note]) }}" method="POST" onsubmit="return confirm('{{ __('Удалить заметку безвозвратно?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                {{-- Кнопка "Удалить" (Опасная, центрирование) --}}
                                                <button type="submit" class="h-8 px-3 rounded-[10px] border-2 border-gray-300 bg-white text-black/80 hover:bg-red-600 hover:border-red-700 hover:text-white transition flex items-center justify-center">{{ __('Удалить') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Exercises --}}
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl md:text-2xl text-black/90">{{ __('Упражнения') }}</h2>
                        {{-- Кнопка "Создать" (ЗАМЕНЕНА на иконку плюса) --}}
                        <a href="{{ route('exercises.create', $track) }}" class="h-9 w-9 px-0 rounded-[12px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white text-xl transition flex items-center justify-center">
                            {{-- Иконка плюса --}}
                            +
                        </a>
                    </div>
                    @if($track->exercises->isEmpty())
                        {{-- Рамка стала мягче --}}
                        <div class="p-8 text-center border-2 border-dashed border-gray-300 rounded-[14px] bg-white">
                            <p class="text-black/70">{{ __('Упражнений пока нет. Добавьте первое.') }}</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach($track->exercises as $exercise)
                                {{-- Карточки: Рамка стала мягче --}}
                                <li class="rounded-[14px] border-2 border-gray-300 bg-white p-5 hover:-translate-y-0.5 transition hover:border-black">
                                    <div class="flex items-start justify-between gap-4">
                                        <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="flex-1 text-[18px] md:text-[20px] leading-6 text-black/90 hover:underline">{{ $exercise->title }}</a>
                                        <div class="flex items-center gap-2">
                                            {{-- Кнопка "Начать" (Вторичная, центрирование) --}}
                                            <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="h-9 px-3 rounded-[12px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white text-sm transition flex items-center justify-center">{{ __('Начать') }}</a>
                                            <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST" onsubmit="return confirm('{{ __('Удалить упражнение?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                {{-- Кнопка "Удалить" (Опасная, центрирование) --}}
                                                <button type="submit" class="h-9 px-3 rounded-[12px] border-2 border-gray-300 bg-white text-black/80 hover:bg-red-600 hover:border-red-700 hover:text-white text-sm transition flex items-center justify-center">{{ __('Удалить') }}</button>
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
        const toggle = document.getElementById('rename-toggle');
        const form = document.getElementById('rename-form');
        const cancelBtn = document.getElementById('rename-cancel');
        if (toggle && form) {
            toggle.addEventListener('click', () => {
                toggle.classList.add('hidden');
                form.classList.remove('hidden');
                form.classList.add('flex');
            });
        }
        if (cancelBtn && form && toggle) {
            cancelBtn.addEventListener('click', () => {
                // Скрываем форму и показываем кнопку "Переименовать"
                form.classList.add('hidden');
                form.classList.remove('flex');
                toggle.classList.remove('hidden');
            });
        }


        // ... (существующий JS для rename)

        // Логика Dropdown для выбора типа заметки
        const toggleButton = document.getElementById('create-note-toggle');
        const menu = document.getElementById('create-note-menu');
        const icon = document.getElementById('note-toggle-icon');

        if (toggleButton && menu) {
            toggleButton.addEventListener('click', () => {
                const isVisible = menu.classList.toggle('hidden');
                if (!isVisible) {
                    icon.classList.add('rotate-180');
                } else {
                    icon.classList.remove('rotate-180');
                }
            });

            document.addEventListener('click', (event) => {
                if (!toggleButton.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });
        }
    </script>
@endsection
