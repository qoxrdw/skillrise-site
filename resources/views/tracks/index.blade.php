@extends('layouts.app')

@section('content')

    {{-- Главный контейнер. Используем flex для центрирования и вертикального потока. --}}
    <div class="flex justify-center min-h-screen bg-white pt-10 pb-20">

        {{-- Основной контент (Максимальная ширина 960px). --}}
        <div class="w-full max-w-[960px] px-4 space-y-8">

            {{-- Сообщения об успехе/ошибке --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Поисковая строка --}}
            <div class="w-full">
                <form method="GET" action="{{ route('tracks.index') }}" class="relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Поиск треков, упражнений"
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

            {{-- Кнопка "Создать трек" --}}
            <button @click="$dispatch('open-modal', 'create-track-modal')"
                    class="w-full h-[111px] border-2 border-black rounded-[30px] bg-white flex items-center justify-center text-[30px] leading-[35px] text-black/60 font-normal hover:bg-gray-50 transition-colors">
                <span class="flex items-center">
                    {{-- Иконка '+' --}}
                    <svg class="w-[29px] h-[28px] mr-4" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g opacity="0.6">
                            <path d="M16.5651 0.543999V27.328H12.5151V0.543999H16.5651ZM27.6351 11.884V15.934H1.39108V11.884H27.6351Z" fill="black"/>
                        </g>
                    </svg>
                    Создать трек
                </span>
            </button>

            {{-- Модальное окно создания трека --}}
            <x-modal name="create-track-modal" maxWidth="lg">
                <div class="p-6 md:p-8">
                    <h3 class="text-2xl md:text-3xl font-normal mb-6">Введите название трека</h3>

                    <div x-data x-cloak>
                        {{-- Форма: только поле ввода и ID --}}
                        <form id="create-track-form" method="POST" action="{{ route('tracks.store') }}" class="space-y-4">
                            @csrf
                            <input type="text" name="name" required autofocus
                                   class="w-full h-12 md:h-[61px] px-4 md:px-6 text-lg md:text-[24px] border-2 border-black rounded-[20px] outline-none placeholder-black/40"
                                   placeholder="Например: Биология, 9 класс">
                        </form>

                        {{-- Раздел кнопок, вынесенный из <form> --}}
                        <div class="flex items-center justify-end gap-3 pt-4">

                            <button type="button"
                                    @click="$dispatch('close')"
                                    class="h-11 px-5 rounded-[20px] border-2 border-black text-black transition-all hover:bg-black hover:text-white">
                                Отмена
                            </button>

                            <button type="submit" form="create-track-form"
                                    class="h-11 px-5 rounded-[20px] border-2 border-black bg-black text-white transition-all hover:opacity-90">
                                Создать трек
                            </button>
                        </div>
                    </div>
                </div>
            </x-modal>

            {{-- Список треков --}}
            <div class="space-y-4">
                @if($tracks->isEmpty())
                    <div class="w-full h-[112px] text-center p-10 bg-white rounded-[30px] border-2 border-dashed border-gray-300 flex flex-col items-center justify-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-xl font-medium text-gray-900">{{ __('Треков пока нет') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Начните свое обучение, создав первый трек!') }}</p>
                    </div>
                @else
                    {{-- Итерация по трекам с Alpine.js для выпадающего списка --}}
                    @foreach($tracks as $track)
                        {{-- Оборачиваем в x-data для Alpine.js. --}}
                        <div x-data="{ open: false }" class="space-y-4">

                            {{-- Заголовок Трека (Кнопка-триггер) --}}
                            <div class="w-full h-[112px] border-2 border-black rounded-[30px] bg-white flex items-center px-8 relative overflow-hidden">

                                {{-- Область для клика, которая разворачивает/сворачивает трек --}}
                                <button @click="open = !open"
                                        class="absolute inset-0 z-10 flex items-center pr-64 transition-colors hover:bg-gray-50">

                                    {{-- Иконка Chevron/Стрелка --}}
                                    <svg class="w-6 h-6 mr-6 ml-4 transition-transform duration-300 flex-shrink-0"
                                         :class="{ 'transform rotate-180': open, 'transform rotate-0': !open }"
                                         viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 10L12 15L17 10" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.8"/>
                                    </svg>

                                    {{-- Название Трека --}}
                                    <span class="flex-1 text-[30px] leading-[35px] font-normal text-black/90 text-left truncate">
                                        {{ $track->name }}
                                    </span>
                                </button>

                                {{-- Группа кнопок (Открыть/Удалить) - z-20, чтобы быть над кнопкой-триггером --}}
                                <div class="absolute right-0 top-0 h-full flex items-center gap-4 pr-8 bg-white z-20">
                                    <a href="{{ route('tracks.show', $track) }}"
                                       class="inline-flex items-center justify-center h-[48px] px-5 rounded-[20px] border-2 border-black text-base text-black transition-all duration-300 ease-in-out hover:bg-black hover:text-white">
                                        Открыть
                                    </a>
                                    <form method="POST" action="{{ route('tracks.destroy', $track) }}" onsubmit="return confirm('Вы уверены, что хотите удалить трек «{{ $track->name }}» и все связанные с ним заметки и упражнения? Это действие необратимо.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center h-[48px] px-5 rounded-[20px] border-2 border-black text-base text-black transition-all duration-300 ease-in-out hover:bg-red-600 hover:border-red-700 hover:text-white">
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div x-show="open" x-collapse.duration.300ms
                                 class="pl-16 pr-4 pb-4"> {{-- Сдвиг вправо для иерархии --}}

                                @php
                                    // Шаг 1: Разделяем и сортируем коллекции.
                                    // Сортировка: сначала по created_at (самые свежие первыми), затем по id (для стабильности).
                                    $notes = $track->notes->sortByDesc(['created_at', 'id'])->values();
                                    $exercises = $track->exercises->sortByDesc(['created_at', 'id'])->values();

                                    $noteCount = $notes->count();
                                    $exerciseCount = $exercises->count();
                                    $pairCount = min($noteCount, $exerciseCount);

                                    $displayItems = collect();

                                    // Шаг 2: Формирование "Полных пар" (Note 2/3 + Exercise 1/3)
                                    for ($i = 0; $i < $pairCount; $i++) {
                                        // Note (2/3, order-1: слева)
                                        $displayItems->push((object) [
                                            'item' => $notes[$i],
                                            'isNote' => true,
                                            'colSpan' => 'col-span-4',
                                            'orderClass' => 'order-1',
                                        ]);
                                        // Exercise (1/3, order-2: справа)
                                        $displayItems->push((object) [
                                            'item' => $exercises[$i],
                                            'isNote' => false,
                                            'colSpan' => 'col-span-2',
                                            'orderClass' => 'order-2',
                                        ]);
                                    }

                                    // Шаг 3: Определение оставшихся элементов
                                    $remainingNotes = $notes->slice($pairCount);
                                    $remainingExercises = $exercises->slice($pairCount);

                                    // Объединяем остатки и сортируем их по времени
                                    $remainingItems = $remainingNotes->merge($remainingExercises)->sortByDesc('created_at');

                                    // Шаг 4: Формирование элементов для оставшихся с гибкой шириной
                                    // -----------------------------------------------------------

                                    // Проверяем, остались ли только элементы одного типа (и их > 1)
                                    $onlyNotesRemain = $remainingExercises->isEmpty() && $remainingNotes->isNotEmpty();
                                    $onlyExercisesRemain = $remainingNotes->isEmpty() && $remainingExercises->isNotEmpty();

                                    // Гибкая ширина:
                                    // - Если остатки смешанные или остался только 1 элемент -> col-span-6 (полная ширина)
                                    // - Если остался только 1 тип и > 1 элемента -> col-span-3 (50% ширины, для двух колонок)
                                    $remainderColSpan = ($onlyNotesRemain || $onlyExercisesRemain) && $remainingItems->count() > 1
                                        ? 'col-span-3'
                                        : 'col-span-6';

                                    $remainingItemsDisplay = $remainingItems->map(function ($item) use ($remainderColSpan) {
                                        $isNote = $item instanceof \App\Models\Note;
                                        return (object) [
                                            'item' => $item,
                                            'isNote' => $isNote,
                                            'colSpan' => $remainderColSpan, // Применяем гибкую ширину
                                            'orderClass' => '',
                                        ];
                                    });

                                    // Шаг 5: Финальный список для вывода: Пары + Остатки
                                    $displayItems = $displayItems->merge($remainingItemsDisplay);
                                    // -----------------------------------------------------------
                                @endphp

                                @if($displayItems->isNotEmpty())
                                    {{-- Используем grid-cols-6 для точного контроля ширины --}}
                                    <div class="grid grid-cols-6 gap-4">
                                        @foreach ($displayItems as $itemWrapper)
                                            @php
                                                $item = $itemWrapper->item;
                                                $isNote = $itemWrapper->isNote;

                                                // 1. Определение заголовка и маршрута
                                                $title = $isNote ? $item->getFirstLine() : ($item->title ?? 'Без названия');
                                                $route = $isNote
                                                    ? route('notes.edit', ['track' => $track->id, 'note' => $item->id])
                                                    : route('exercises.take', ['track' => $track->id, 'exercise' => $item->id]);

                                                // 2. Классы для ширины и порядка
                                                $colSpan = $itemWrapper->colSpan;
                                                $orderClass = $itemWrapper->orderClass;

                                                // 3. Классы для цвета
                                                $cardClasses = 'bg-white';

                                                // 4. Иконки
                                                $noteIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3 flex-shrink-0 text-black/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>';
                                                $exerciseIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-3 flex-shrink-0 text-black/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>';
                                                $icon = $isNote ? $noteIcon : $exerciseIcon;
                                            @endphp

                                            <a href="{{ $route }}"
                                               class="h-[112px] border-2 border-black rounded-[30px] flex items-center px-6 transition-shadow hover:shadow-lg {{ $cardClasses }} {{ $colSpan }} {{ $orderClass }}">

                                                {!! $icon !!} {{-- Добавлена иконка --}}

                                                <span class="flex-1 text-[26px] leading-8 font-normal text-black/90 truncate">
                                {{ $title }}
                            </span>

                                                {{-- Иконка "Вперед" только для Упражнений, и только если это не col-span-6 --}}
                                                @if (!$isNote && $colSpan !== 'col-span-6')
                                                    <svg class="w-6 h-6 ml-4 flex-shrink-0" viewBox="0 0 19 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.6" d="M7.63672 0L18.4063 13.3154V14.6855L7.63672 28H0.000976562L11.7422 14.1055L0.000976563 0H7.63672Z" fill="black"/>
                                                    </svg>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div> {{-- end grid --}}
                                @else
                                    <div class="p-6 text-center text-gray-500 border-2 border-dashed rounded-[30px] border-gray-300">
                                        В этом треке пока нет заметок или упражнений.
                                    </div>
                                @endif
                            </div> {{-- end x-show --}}
                        </div> {{-- end x-data (Track block) --}}
                    @endforeach
                @endif
            </div> {{-- end space-y-4 --}}

        </div>
    </div>
@endsection
