@extends('layouts.app')

@section('content')
    {{-- Полноэкранный режим --}}
    <div class="fixed inset-0 bg-gray-50 z-50 flex flex-col">
        {{-- Верхняя панель навигации --}}
        <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center text-sm text-black/70 hover:text-black transition">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Назад к треку') }}
            </a>
            <h1 class="text-lg font-bold text-black/90">{{ __('Редактирование рукописной заметки') }}</h1>
            <div class="w-24"></div>
        </div>

        {{-- Основной контент --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <form id="handwriting-form" method="POST" action="{{ route('notes.update.handwriting', [$track, $note]) }}" class="flex-1 flex flex-col">
                @csrf
                @method('PATCH')

                <input type="hidden" name="content_json" id="content_json">
                <input type="hidden" name="content_base64" id="content_base64">

                {{-- Панель инструментов --}}
                <div id="toolbar" class="flex items-center gap-2 p-3 bg-white border-b border-gray-200">
                    {{-- Инструменты рисования --}}
                    <div class="flex items-center gap-2 border-r border-gray-300 pr-3">
                        <button type="button" data-tool="pen" class="tool-button h-10 w-10 rounded-lg border-2 border-black bg-black text-white hover:opacity-90 transition flex items-center justify-center text-lg" title="Перо">
                            ✏️
                        </button>
                        <button type="button" data-tool="marker" class="tool-button h-10 w-10 rounded-lg border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center text-lg" title="Маркер">
                            🖊️
                        </button>
                        <button type="button" data-tool="pencil" class="tool-button h-10 w-10 rounded-lg border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center text-lg" title="Карандаш">
                            ✐
                        </button>
                        <button type="button" data-tool="highlighter" class="tool-button h-10 w-10 rounded-lg border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center text-lg" title="Выделитель">
                            🖍️
                        </button>
                        <button type="button" data-tool="eraser" class="tool-button h-10 w-10 rounded-lg border-2 border-gray-300 bg-white text-black/80 hover:bg-black hover:text-white transition flex items-center justify-center text-lg" title="Ластик">
                            🧼
                        </button>
                    </div>

                    {{-- Выбор цвета --}}
                    <div class="flex items-center gap-2 border-r border-gray-300 pr-3">
                        <label class="text-sm text-gray-600 font-medium">Цвет:</label>
                        <input type="color" id="color-picker" value="#000000" class="h-10 w-12 rounded-lg border-2 border-gray-300 cursor-pointer">

                        {{-- Быстрые цвета --}}
                        <button type="button" data-color="#000000" class="quick-color h-8 w-8 rounded-md border-2 border-gray-300 bg-black hover:scale-110 transition"></button>
                        <button type="button" data-color="#EF4444" class="quick-color h-8 w-8 rounded-md border-2 border-gray-300 bg-red-500 hover:scale-110 transition"></button>
                        <button type="button" data-color="#3B82F6" class="quick-color h-8 w-8 rounded-md border-2 border-gray-300 bg-blue-500 hover:scale-110 transition"></button>
                        <button type="button" data-color="#10B981" class="quick-color h-8 w-8 rounded-md border-2 border-gray-300 bg-green-500 hover:scale-110 transition"></button>
                        <button type="button" data-color="#F59E0B" class="quick-color h-8 w-8 rounded-md border-2 border-gray-300 bg-amber-500 hover:scale-110 transition"></button>
                        <button type="button" data-color="#8B5CF6" class="quick-color h-8 w-8 rounded-md border-2 border-gray-300 bg-purple-500 hover:scale-110 transition"></button>
                    </div>

                    {{-- Размер пера --}}
                    <div class="flex items-center gap-2 border-r border-gray-300 pr-3">
                        <label class="text-sm text-gray-600 font-medium">Размер:</label>
                        <input type="range" id="brush-size" min="1" max="50" value="5" class="w-32 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <span id="brush-size-display" class="text-sm font-medium text-gray-700 w-8">5</span>
                    </div>

                    {{-- Действия --}}
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" id="clear-canvas" class="h-10 px-4 rounded-lg border-2 border-gray-300 bg-white text-black/80 hover:bg-red-600 hover:border-red-700 hover:text-white transition flex items-center justify-center text-sm">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Очистить') }}
                        </button>
                        <button type="submit" id="save-note-btn" class="h-10 px-6 rounded-lg border-2 border-green-600 bg-green-600 text-white hover:bg-green-700 hover:border-green-700 transition flex items-center justify-center text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                            </svg>
                            {{ __('Сохранить') }}
                        </button>
                    </div>
                </div>

                {{-- Холст для рисования --}}
                <div class="flex-1 overflow-hidden bg-white">
                    <canvas id="handwriting-canvas" class="w-full h-full"></canvas>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof fabric !== 'undefined') {
                setTimeout(initCanvas, 100);
            } else {
                console.error('Fabric.js не загружен.');
            }
        });

        function initCanvas() {
            const canvasElement = document.getElementById('handwriting-canvas');
            if (!canvasElement || typeof fabric === 'undefined') {
                console.error('Canvas элемент или библиотека Fabric.js не найдены.');
                return;
            }

            const container = canvasElement.parentElement;
            const containerRect = container.getBoundingClientRect();
            const containerWidth = containerRect.width;
            const containerHeight = containerRect.height;

            canvasElement.width = containerWidth;
            canvasElement.height = containerHeight;

            const canvas = new fabric.Canvas('handwriting-canvas', {
                isDrawingMode: true,
                selection: false,
                skipTargetFind: true,
                width: containerWidth,
                height: containerHeight,
                backgroundColor: '#FFFFFF'
            });

            canvas.calcOffset();
            canvas.renderAll();

            // ЗАГРУЗКА СУЩЕСТВУЮЩЕГО КОНТЕНТА
            const existingContentJson = @json($note->content ?? 'null');
            if (existingContentJson && existingContentJson !== 'null') {
                try {
                    canvas.loadFromJSON(existingContentJson, () => {
                        canvas.renderAll();
                    });
                } catch (e) {
                    console.error('Ошибка при загрузке JSON-данных заметки:', e);
                }
            }

            // Настройки кисти
            let currentTool = 'pen';
            let currentColor = '#000000';
            let brushSize = 5;

            // Настройка разных типов кистей
            const setPenMode = () => {
                canvas.isDrawingMode = true;
                canvas.off('mouse:down');
                canvas.skipTargetFind = true;
                canvas.selection = false;

                canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
                canvas.freeDrawingBrush.width = brushSize;
                canvas.freeDrawingBrush.color = currentColor;
                canvas.freeDrawingBrush.strokeLineCap = 'round';
                canvas.freeDrawingBrush.strokeLineJoin = 'round';
            };

            const setMarkerMode = () => {
                canvas.isDrawingMode = true;
                canvas.off('mouse:down');
                canvas.skipTargetFind = true;
                canvas.selection = false;

                canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
                canvas.freeDrawingBrush.width = brushSize * 1.5;
                canvas.freeDrawingBrush.color = currentColor;
                canvas.freeDrawingBrush.strokeLineCap = 'square';
                canvas.freeDrawingBrush.strokeLineJoin = 'miter';
            };

            const setPencilMode = () => {
                canvas.isDrawingMode = true;
                canvas.off('mouse:down');
                canvas.skipTargetFind = true;
                canvas.selection = false;

                canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
                canvas.freeDrawingBrush.width = brushSize * 0.7;
                canvas.freeDrawingBrush.color = currentColor;
                canvas.freeDrawingBrush.strokeLineCap = 'round';
                canvas.freeDrawingBrush.strokeLineJoin = 'round';
                const hexToRgba = (hex, alpha = 0.8) => {
                    const r = parseInt(hex.slice(1, 3), 16);
                    const g = parseInt(hex.slice(3, 5), 16);
                    const b = parseInt(hex.slice(5, 7), 16);
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                };
                canvas.freeDrawingBrush.color = hexToRgba(currentColor, 0.8);
            };

            const setHighlighterMode = () => {
                canvas.isDrawingMode = true;
                canvas.off('mouse:down');
                canvas.skipTargetFind = true;
                canvas.selection = false;

                canvas.freeDrawingBrush = new fabric.PencilBrush(canvas);
                canvas.freeDrawingBrush.width = brushSize * 3;
                canvas.freeDrawingBrush.color = currentColor + '33';
                canvas.freeDrawingBrush.strokeLineCap = 'square';
            };

            const eraserHandler = function(options) {
                if (options.target) {
                    canvas.remove(options.target);
                    canvas.renderAll();
                }
            };

            const setEraserMode = () => {
                canvas.isDrawingMode = false;
                canvas.skipTargetFind = false;
                canvas.selection = false;
                canvas.off('mouse:down', eraserHandler);
                canvas.on('mouse:down', eraserHandler);
            };

            // Панель инструментов
            const toolButtons = document.querySelectorAll('.tool-button');
            const colorPicker = document.getElementById('color-picker');
            const quickColorButtons = document.querySelectorAll('.quick-color');
            const brushSizeSlider = document.getElementById('brush-size');
            const brushSizeDisplay = document.getElementById('brush-size-display');
            const clearButton = document.getElementById('clear-canvas');
            const form = document.getElementById('handwriting-form');
            const jsonInput = document.getElementById('content_json');
            const base64Input = document.getElementById('content_base64');

            // Обработчики инструментов
            toolButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    toolButtons.forEach(b => {
                        b.classList.remove('bg-black', 'text-white', 'border-black');
                        b.classList.add('bg-white', 'text-black/80', 'border-gray-300');
                    });

                    currentTool = btn.dataset.tool;
                    btn.classList.add('bg-black', 'text-white', 'border-black');
                    btn.classList.remove('bg-white', 'text-black/80', 'border-gray-300');

                    switch(currentTool) {
                        case 'pen': setPenMode(); break;
                        case 'marker': setMarkerMode(); break;
                        case 'pencil': setPencilMode(); break;
                        case 'highlighter': setHighlighterMode(); break;
                        case 'eraser': setEraserMode(); break;
                    }
                });
            });

            // Инициализация начального состояния
            const penButton = document.querySelector('[data-tool="pen"]');
            if (penButton) penButton.click();

            // Обработчик цвета
            colorPicker.addEventListener('input', (e) => {
                currentColor = e.target.value;
                if (currentTool === 'pen') setPenMode();
                else if (currentTool === 'marker') setMarkerMode();
                else if (currentTool === 'pencil') setPencilMode();
                else if (currentTool === 'highlighter') setHighlighterMode();
            });

            // Быстрые цвета
            quickColorButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    currentColor = btn.dataset.color;
                    colorPicker.value = currentColor;
                    if (currentTool === 'pen') setPenMode();
                    else if (currentTool === 'marker') setMarkerMode();
                    else if (currentTool === 'pencil') setPencilMode();
                    else if (currentTool === 'highlighter') setHighlighterMode();
                });
            });

            // Размер кисти
            brushSizeSlider.addEventListener('input', (e) => {
                brushSize = parseInt(e.target.value);
                brushSizeDisplay.textContent = brushSize;
                if (currentTool === 'pen') setPenMode();
                else if (currentTool === 'marker') setMarkerMode();
                else if (currentTool === 'pencil') setPencilMode();
                else if (currentTool === 'highlighter') setHighlighterMode();
            });

            // Очистка
            clearButton.addEventListener('click', () => {
                if (confirm('Вы уверены, что хотите очистить весь холст?')) {
                    canvas.clear();
                    canvas.backgroundColor = '#FFFFFF';
                    canvas.renderAll();
                    if (currentTool === 'pen') setPenMode();
                    else if (currentTool === 'marker') setMarkerMode();
                    else if (currentTool === 'pencil') setPencilMode();
                    else if (currentTool === 'highlighter') setHighlighterMode();
                }
            });

            // Сохранение
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

            // Ресайз
            window.addEventListener('resize', () => {
                const newRect = container.getBoundingClientRect();
                canvas.setWidth(newRect.width);
                canvas.setHeight(newRect.height);
                canvas.calcOffset();
                canvas.renderAll();
            });
        }
    </script>
@endsection
