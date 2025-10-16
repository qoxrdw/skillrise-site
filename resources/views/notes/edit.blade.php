@extends('layouts.app')

@section('content')
    {{-- Основной контейнер с белым фоном и вертикальным отступом --}}
    {{-- Добавлен bg-white для соответствия фону формы --}}
    <div class="py-12 bg-white">
        {{-- Контейнер для контента с автоматическими горизонтальными отступами для центрирования --}}
        {{-- Убрана фиксированная максимальная ширина и скорректированы горизонтальные отступы для контроля ширины --}}
        {{-- Увеличены горизонтальные отступы для уменьшения ширины формы (~50%) --}}
        <div class="mx-auto sm:px-24 lg:px-80"> {{-- Горизонтальные отступы увеличены для сужения формы --}}

            {{-- Хлебные крошки или ссылка "Назад" --}}
            <div class="mb-6">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors duration-300">
                    {{-- Иконка стрелки назад --}}
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Назад к треку: ') }} {{ $track->name }}
                </a>
            </div>

            {{-- Заголовок страницы --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">
                {{ __('Редактировать заметку в треке:') }} <span class="text-indigo-600">{{ $track->name }}</span>
            </h1>

            {{-- Сообщения сессии (обработка ошибок) --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg shadow-sm">
                    <div class="font-semibold mb-2">{{ __('Обнаружены ошибки:') }}</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Форма редактирования заметки --}}
            {{-- Убраны классы, отвечающие за внешнюю рамку и тень формы --}}
            <div class="bg-white"> {{-- Этот div уже имеет bg-white --}}
                <form id="note-form" method="POST" action="{{ route('notes.update', [$track, $note]) }}">
                    @csrf
                    @method('PATCH') {{-- Метод для обновления --}}
                    <div class="p-6 sm:p-8">
                        {{-- Контейнер редактора Quill --}}
                        {{-- Убраны классы рамки и фокуса из родительского div --}}
                        <div class="mb-6">
                            {{-- Сам редактор Quill --}}
                            {{-- Добавлены inline стили для удаления рамки Quill и установлена минимальная высота --}}
                            {{-- Убрана фиксированная высота, используется min-height для адаптивности --}}
                            <div id="editor" style="min-height: 70vh; border: none !important;" class="bg-white">
                                {{-- Quill будет инициализирован здесь с $note->content вашим JS --}}
                            </div>
                        </div>
                        {{-- Скрытое поле для сохранения содержимого Quill --}}
                        {{-- Значение для этого скрытого поля будет установлено вашим JS (notes-edit.js) --}}
                        {{-- Важно, чтобы это значение правильно передавалось из объекта $note в Quill, а затем в это поле при отправке --}}
                        <input type="hidden" name="content" id="content" value="{{ old('content', $note->content) }}">


                        {{-- Кнопки действий --}}
                        <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                            {{-- Кнопка "Отмена" --}}
                            <a href="{{ route('tracks.show', $track) }}"
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('Отмена') }}
                            </a>
                            {{-- Кнопка "Сохранить изменения" --}}
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{-- Иконка сохранения --}}
                                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Сохранить изменения') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Стили и скрипт Quill --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    {{-- Пользовательский скрипт для инициализации Quill и обработки отправки формы --}}
    {{-- Этот скрипт должен:
        1. Инициализировать Quill в #editor.
        2. Установить начальное содержимое Quill, используя значение из скрытого поля #content (которое содержит $note->content).
        3. При отправке формы обновить скрытое поле #content текущим содержимым из Quill.
    --}}
    @vite('resources/js/notes-edit.js')

    {{-- Дополнительные стили для настройки внешнего вида Quill --}}
    <style>
        /* Убираем рамку Quill редактора и контейнера */
        .ql-toolbar.ql-snow,
        .ql-container.ql-snow {
            border: none !important;
        }

        /* Убираем нижнюю границу у тулбара или делаем ее тонкой */
        .ql-toolbar.ql-snow {
            border-bottom: 1px solid #e5e7eb !important; /* Оставляем тонкую линию для разделения тулбара */
        }

        /* Настройка стилей для области редактирования */
        .ql-editor {
            /* padding: top right bottom left */
            /* Увеличен отступ сверху и скорректированы боковые отступы */
            padding: 100px 48px 24px 48px !important;
            font-size: 1.125rem !important; /* Увеличиваем размер текста */
            line-height: 1.75rem !important; /* Увеличиваем межстрочный интервал */
            min-height: calc(70vh - 124px); /* Минимальная высота с учетом верхнего и нижнего padding */
        }

        /* Выравнивание плейсхолдера по вертикали */
        .ql-editor::before {
            top: 100px !important; /* Устанавливаем верхний отступ, равный padding-top .ql-editor */
            left: 48px !important; /* Устанавливаем левый отступ, равный padding-left .ql-editor */
        }


        /* Дополнительные стили для заголовков в редакторе, если используются */
        .ql-editor h1 {
            font-size: 2.25rem !important; /* Пример: text-4xl */
            font-weight: bold !important;
            margin-bottom: 1.5rem !important; /* Пример: mb-6 */
        }

        .ql-editor h2 {
            font-size: 1.875rem !important; /* Пример: text-3xl */
            font-weight: bold !important;
            margin-bottom: 1.25rem !important; /* Пример: mb-5 */
        }
        /* ... добавьте стили для других элементов, если нужно (h3, h4, списки и т.д.) */
    </style>
@endsection
