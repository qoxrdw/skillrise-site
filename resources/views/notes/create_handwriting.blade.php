@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Back link --}}
            <div class="mb-6">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center text-sm text-black/70 hover:text-black transition">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Назад к треку') }}
                </a>
            </div>

            <div class="rounded-[20px] border-2 border-black/10 bg-white shadow-xl">
                <div class="p-6 md:p-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-black/90 mb-6">{{ __('Новая рукописная заметка') }}</h1>

                    <form id="handwriting-form" method="POST" action="{{ route('notes.store.handwriting', $track) }}">
                        @csrf

                        {{-- Скрытые поля для передачи данных на сервер --}}
                        <input type="hidden" name="content_json" id="content_json">
                        <input type="hidden" name="content_base64" id="content_base64">

                        {{-- Панель инструментов --}}
                        <div id="toolbar" class="flex flex-wrap items-center gap-2 p-3 mb-4 rounded-[14px] border-2 border-gray-200 bg-gray-50 sticky top-0 z-10">
                            {{-- Перо. Начальное состояние: черная кнопка --}}
                            <button type="button" data-tool="pen" class="tool-button h-10 w-10 rounded-[12px] border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center text-lg">✏️</button>
                            {{-- Ластик. Начальное состояние: белая кнопка --}}
                            <button type="button" data-tool="eraser" class="tool-button h-10 w-10 rounded-[12px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center text-lg">🧼</button>
                            {{-- Выделитель. Начальное состояние: белая кнопка --}}
                            <button type="button" data-tool="highlighter" class="tool-button h-10 w-10 rounded-[12px] border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center text-lg">🖍️</button>

                            {{-- Выбор цвета --}}
                            <input type="color" id="color-picker" value="#000000" class="h-10 w-10 rounded-[12px] border-2 border-gray-300 cursor-pointer">

                            {{-- Очистить --}}
                            <button type="button" id="clear-canvas" class="h-10 px-4 rounded-[14px] border-2 border-gray-300 bg-white text-black/80 hover:bg-red-600 hover:border-red-700 hover:text-white transition flex items-center justify-center text-sm ml-auto">{{ __('Очистить') }}</button>

                            {{-- Сохранить --}}
                            <button type="submit" id="save-note-btn" class="h-10 px-4 rounded-[14px] border-2 border-black bg-green-600 text-white hover:bg-green-700 transition flex items-center justify-center text-sm">{{ __('Сохранить') }}</button>
                        </div>

                        {{-- Холст для рисования --}}
                        <div class="relative rounded-[14px] border-2 border-gray-300 overflow-hidden z-0">
                            {{-- Добавляем bg-white, чтобы видеть, что Canvas занимает место --}}
                            <canvas id="handwriting-canvas" class="w-full" style="min-height: 80vh;"></canvas>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- !!! ИСПРАВЛЕНО: Объединение тегов <script> и замена cdn.jsdelivr.net на cdnjs.cloudflare.com !!! --}}

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"
        id="fabric-cdn"
        {{-- Удаляем onload из этого тега, чтобы избежать ошибки ReferenceError --}}
    ></script>

    <script>
        // Используем document.addEventListener('DOMContentLoaded', ...) для гарантии
        // того, что все элементы DOM (кнопки, canvas) существуют до того, как
        // мы попытаемся их найти и вызвать initCanvas.
        document.addEventListener('DOMContentLoaded', function() {
            // Проверяем, что библиотека Fabric.js загрузилась
            if (typeof fabric !== 'undefined') {
                // Используем небольшую задержку (100 мс) только после того, как DOM готов,
                // чтобы дать браузеру время рассчитать размеры контейнера.
                setTimeout(initCanvas, 100);
            } else {
                console.error('Fabric.js не загружен. Проверьте CSP или путь к CDN.');
            }
        });

        function initCanvas() {

            const canvasElement = document.getElementById('handwriting-canvas');

            if (!canvasElement || typeof fabric === 'undefined') {
                console.error('Инициализация не удалась: элемент Canvas или библиотека Fabric.js не найдены.');
                return;
            }

            const container = canvasElement.parentElement;

            // Используем getBoundingClientRect для точного размера контейнера
            const containerRect = container.getBoundingClientRect();
            const containerWidth = containerRect.width;
            const containerHeight = 800;

            // Устанавливаем нативные размеры Canvas
            canvasElement.width = containerWidth;
            canvasElement.height = containerHeight;

            // Инициализация Fabric.js
            const canvas = new fabric.Canvas('handwriting-canvas', {
                isDrawingMode: true,
                selection: false,
                skipTargetFind: true,
                width: containerWidth,
                height: containerHeight
            });

            // Принудительно пересчитываем смещения и перерисовываем холст
            canvas.calcOffset();
            canvas.renderAll();

            // Улучшенные настройки для работы со стилусом и сенсором
            canvas.stopContextMenu = true;
            if (canvas.freeDrawingBrush) {
                canvas.freeDrawingBrush.decimate = 0;
            }

            let currentTool = 'pen';
            let currentColor = '#000000';
            let penWidth = 5;

            // Настройка Fabric.js для Pen
            const setPenMode = () => {
                canvas.isDrawingMode = true;
                canvas.off('mouse:down');

                canvas.skipTargetFind = true; // Снова отключаем поиск объектов

                canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
                canvas.freeDrawingBrush.width = penWidth;
                canvas.freeDrawingBrush.color = currentColor;
                canvas.freeDrawingBrush.decimate = 0;
                canvas.renderAll();
            };

            // Настройка Fabric.js для Highlighter
            const setHighlighterMode = () => {
                canvas.isDrawingMode = true;
                canvas.off('mouse:down');

                canvas.skipTargetFind = true; // Снова отключаем поиск объектов

                canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
                canvas.freeDrawingBrush.width = 25;
                canvas.freeDrawingBrush.color = currentColor + '33';
                canvas.freeDrawingBrush.strokeLineCap = 'round';
                canvas.freeDrawingBrush.decimate = 0;
                canvas.renderAll();
            };

            // Настройка Fabric.js для Eraser (Ластик)
            const eraserHandler = function(options) {
                if (options.target) {
                    canvas.remove(options.target);
                    canvas.renderAll(); // Явный ререндер после удаления
                }
            };

            const setEraserMode = () => {
                canvas.isDrawingMode = false;

                // !!! ГЛАВНОЕ ИСПРАВЛЕНИЕ: Включаем поиск объектов, чтобы Ластик работал !!!
                canvas.skipTargetFind = false;
                canvas.selection = false; // Убедимся, что выделение рамкой остается выключенным

                // Перед включением режима Ластика, нужно убедиться, что он не прикреплен
                canvas.off('mouse:down', eraserHandler);
                // Прикрепляем обработчик Ластика
                canvas.on('mouse:down', eraserHandler);
            };

            // --- Панель инструментов: УТОЧНЕННЫЙ ПОИСК И ЛОГИКА ---
            const toolButtons = document.querySelectorAll('#toolbar .tool-button');
            const colorPicker = document.getElementById('color-picker');
            const clearButton = document.getElementById('clear-canvas');
            const form = document.getElementById('handwriting-form');
            const jsonInput = document.getElementById('content_json');
            const base64Input = document.getElementById('content_base64');

            toolButtons.forEach(btn => {
                btn.addEventListener('click', () => {

                    // Сброс активного состояния для всех кнопок
                    toolButtons.forEach(b => {
                        b.classList.remove('bg-black', 'text-white', 'border-black');
                        b.classList.add('bg-white', 'text-black/80', 'border-gray-300');
                    });

                    // Установка активного состояния для нажатой кнопки
                    currentTool = btn.dataset.tool;

                    btn.classList.add('bg-black', 'text-white', 'border-black');
                    btn.classList.remove('bg-white', 'text-black/80', 'border-gray-300');

                    // Применяем выбранный инструмент
                    if (currentTool === 'pen') {
                        setPenMode();
                    } else if (currentTool === 'highlighter') {
                        setHighlighterMode();
                    } else if (currentTool === 'eraser') {
                        setEraserMode();
                    }
                });
            });

            // ФОРСИРОВАНИЕ НАЧАЛЬНОГО СОСТОЯНИЯ
            const penButton = document.querySelector('[data-tool="pen"]');
            if (penButton) {
                penButton.click();
            }

            // Смена цвета
            colorPicker.addEventListener('input', (e) => {
                currentColor = e.target.value;
                if (currentTool === 'pen') {
                    setPenMode();
                } else if (currentTool === 'highlighter') {
                    setHighlighterMode();
                }
            });

            // Очистка
            clearButton.addEventListener('click', () => {
                if (confirm('Вы уверены, что хотите очистить весь холст?')) {
                    canvas.clear();
                    if (currentTool === 'pen') setPenMode();
                    if (currentTool === 'highlighter') setHighlighterMode();
                }
            });

            // Логика сохранения
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const json_data = canvas.toJSON();
                jsonInput.value = JSON.stringify(json_data);

                const base64_data = canvas.toDataURL({
                    format: 'png',
                    quality: 1.0
                });
                base64Input.value = base64_data;

                form.submit();
            });

            // Корректировка размеров при ресайзе окна
            window.addEventListener('resize', () => {
                const newRect = container.getBoundingClientRect();
                canvas.setWidth(newRect.width);
                canvas.setHeight(containerHeight);
                canvas.calcOffset();
                canvas.renderAll();
            });

        }
    </script>
@endsection
