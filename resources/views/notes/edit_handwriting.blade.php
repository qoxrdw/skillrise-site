@extends('layouts.app')

@section('content')
    <div class="fixed inset-0 bg-[#E5E5E5] z-50 flex flex-col font-sans overflow-hidden">

        {{-- Header --}}
        <div class="h-[70px] bg-white border-b border-black flex items-center justify-between px-8 shrink-0 z-30">
            <div class="flex items-center gap-6">
                <a href="{{ route('tracks.show', $track) }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-[16px] font-bold uppercase tracking-widest text-black/40">{{ __('Edit Note') }}</h1>
            </div>

            {{-- Пагинация + Undo --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 mr-4 border-r border-black/10 pr-4">
                    <button type="button" id="undo-btn" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-gray-100 transition-all" title="Отменить (Ctrl+Z)">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14L4 9l5-5"/><path d="M20 20v-7a4 4 0 00-4-4H4"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-6 bg-white border border-black rounded-full px-5 py-2">
                    <button type="button" id="prev-page" class="hover:text-blue-600 disabled:opacity-10"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg></button>
                    <span class="text-[13px] font-bold tabular-nums">PAGE <span id="current-page-num">1</span> / <span id="total-pages-num">1</span></span>
                    <button type="button" id="next-page" class="hover:text-blue-600 disabled:opacity-10"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></button>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" id="add-page" class="h-11 px-5 border border-black rounded-full text-[14px] font-medium hover:bg-black hover:text-white transition-all">+ Лист</button>
                <button type="button" onclick="document.getElementById('handwriting-form').requestSubmit()" class="h-11 px-8 bg-black text-white rounded-full text-[14px] font-bold hover:bg-black/90 transition-all shadow-lg">{{ __('Обновить') }}</button>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden relative">

            {{-- Sidebar Инструменты --}}
            <div class="w-[85px] bg-white border-r border-black flex flex-col items-center py-6 gap-6 z-20 overflow-y-auto scrollbar-hide">

                <div class="flex flex-col gap-2">
                    <button type="button" data-tool="hand" class="tool-btn w-12 h-12 rounded-xl flex items-center justify-center text-xl border border-transparent hover:border-black transition-all" title="Рука (H)">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v5"></path>
                            <path d="M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v6"></path>
                            <path d="M10 10.5V5a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"></path>
                            <path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"></path>
                        </svg>
                    </button>
                    <button type="button" data-tool="select" class="tool-btn w-12 h-12 rounded-xl flex items-center justify-center text-xl border border-transparent hover:border-black transition-all" title="Выделение (V)">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3zM13 13l6 6"/></svg>
                    </button>
                    <button type="button" data-tool="pen" class="tool-btn active w-12 h-12 rounded-xl flex items-center justify-center text-xl border border-transparent hover:border-black transition-all">✏️</button>
                    <button type="button" data-tool="highlighter" class="tool-btn w-12 h-12 rounded-xl flex items-center justify-center text-xl border border-transparent hover:border-black transition-all">🖍️</button>
                    <button type="button" data-tool="eraser" class="tool-btn w-12 h-12 rounded-xl flex items-center justify-center text-xl border border-transparent hover:border-black transition-all">🧼</button>
                </div>

                <div class="w-10 h-[1px] bg-black/10"></div>

                <div class="flex flex-col gap-3">
                    <button type="button" data-tool="rect" class="tool-btn w-12 h-12 rounded-xl flex items-center justify-center border border-transparent hover:border-black transition-all">
                        <div class="w-5 h-5 border-2 border-current"></div>
                    </button>
                    <button type="button" data-tool="circle" class="tool-btn w-12 h-12 rounded-xl flex items-center justify-center border border-transparent hover:border-black transition-all">
                        <div class="w-5 h-5 border-2 border-current rounded-full"></div>
                    </button>
                </div>

                <div class="w-10 h-[1px] bg-black/10"></div>

                <div class="flex flex-col gap-4" id="palette-container">
                    <button type="button" data-color="#000000" class="color-btn w-6 h-6 rounded-full bg-black ring-2 ring-offset-2 ring-black"></button>
                    <button type="button" data-color="#3B82F6" class="color-btn w-6 h-6 rounded-full bg-blue-500 hover:scale-110 transition-transform"></button>
                    <button type="button" data-color="#EF4444" class="color-btn w-6 h-6 rounded-full bg-red-500 hover:scale-110 transition-transform"></button>
                    <div class="relative w-8 h-8 flex items-center justify-center">
                        <input type="color" id="color-picker" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="w-6 h-6 rounded-full border border-black bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500"></div>
                    </div>
                </div>

                <div class="mt-auto pb-4 flex flex-col items-center gap-2">
                    <span class="text-[10px] font-bold opacity-30">SIZE</span>
                    <input type="range" id="brush-size" min="1" max="50" value="4" class="h-24 w-1 accent-black appearance-none bg-black/10 rounded-full outline-none" style="-webkit-appearance: slider-vertical;">
                </div>
            </div>

            {{-- Viewport --}}
            <div class="flex-1 overflow-auto bg-[#E5E5E5] flex justify-center items-start p-10 scrollbar-hide">
                <div id="canvas-container" class="bg-white shadow-[0_30px_60px_rgba(0,0,0,0.15)] border border-black/5 origin-top transition-transform duration-200">
                    <canvas id="handwriting-canvas"></canvas>
                </div>
            </div>
        </div>

        <form id="handwriting-form" method="POST" action="{{ route('notes.update.handwriting', [$track, $note]) }}" class="hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="content_json" id="content_json">
            <input type="hidden" name="content_base64" id="content_base64">
        </form>
    </div>

    <style>
        .tool-btn.active { background: black !important; color: white !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Загрузка существующих данных
            // ... внутри DOMContentLoaded ...

// Безопасная загрузка данных
            let pages = [null];
            const rawDataFromBackend = {!! json_encode($note->content) !!};

            if (rawDataFromBackend) {
                try {
                    // Проверяем, пришла ли строка или уже массив/объект
                    let parsedData = (typeof rawDataFromBackend === 'string')
                        ? JSON.parse(rawDataFromBackend)
                        : rawDataFromBackend;

                    // Если это строка внутри строки (двойная сериализация), парсим еще раз
                    if (typeof parsedData === 'string') {
                        parsedData = JSON.parse(parsedData);
                    }

                    if (Array.isArray(parsedData)) {
                        pages = parsedData;
                    } else if (parsedData && typeof parsedData === 'object') {
                        pages = [parsedData]; // старый формат
                    }
                } catch (e) {
                    console.error("Критическая ошибка загрузки заметок:", e);
                    pages = [null];
                }
            }

            let currentPage = 0;
            let canvas;
            let history = [];
            let currentTool = 'pen';
            let currentColor = '#000000';

            const canvasWidth = 1600;
            const canvasHeight = 1100;

            let isDragging = false;
            let lastPosX, lastPosY;

            function initCanvas() {
                canvas = new fabric.Canvas('handwriting-canvas', {
                    isDrawingMode: true,
                    width: canvasWidth,
                    height: canvasHeight,
                    backgroundColor: '#ffffff',
                    selection: false
                });

                // Первичная загрузка контента
                if (pages[currentPage]) {
                    canvas.loadFromJSON(pages[currentPage], () => {
                        canvas.renderAll();
                        saveState();
                    });
                }

                updateBrush();

                // Обработчик событий мыши
                canvas.on('mouse:down', function(o) {
                    let pointer = canvas.getPointer(o.e);

                    if (currentTool === 'hand') {
                        isDragging = true;
                        lastPosX = o.e.clientX || (o.e.touches ? o.e.touches[0].clientX : 0);
                        lastPosY = o.e.clientY || (o.e.touches ? o.e.touches[0].clientY : 0);
                        return;
                    }

                    if ((currentTool === 'rect' || currentTool === 'circle') && !canvas.findTarget(o.e)) {
                        createShape(pointer);
                    }
                });

                canvas.on('mouse:move', function(o) {
                    if (isDragging && currentTool === 'hand') {
                        let e = o.e;
                        let clientX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                        let clientY = e.clientY || (e.touches ? e.touches[0].clientY : 0);

                        let vpt = canvas.viewportTransform;
                        vpt[4] += clientX - lastPosX;
                        vpt[5] += clientY - lastPosY;
                        canvas.requestRenderAll();

                        lastPosX = clientX;
                        lastPosY = clientY;
                    }
                });

                canvas.on('mouse:up', () => isDragging = false);
                canvas.on('path:created', saveState);
                canvas.on('object:modified', saveState);

                fitToScreen();
                window.addEventListener('resize', fitToScreen);
                updatePaginationUI();
            }

            function createShape(pointer) {
                let brushSize = parseInt(document.getElementById('brush-size').value);
                let size = brushSize * 15;
                let shape;
                const props = {
                    left: pointer.x, top: pointer.y,
                    fill: 'transparent', stroke: currentColor,
                    strokeWidth: brushSize, originX: 'center', originY: 'center'
                };

                if (currentTool === 'rect') {
                    shape = new fabric.Rect({ ...props, width: size, height: size });
                } else {
                    shape = new fabric.Circle({ ...props, radius: size / 2 });
                }
                canvas.add(shape);
                canvas.setActiveObject(shape);
                saveState();
            }

            function updateBrush() {
                canvas.isDrawingMode = false;
                canvas.selection = false;
                canvas.off('mouse:down', eraserHandler);
                canvas.defaultCursor = (currentTool === 'hand') ? 'grab' : 'default';

                if (currentTool === 'pen' || currentTool === 'highlighter') {
                    canvas.isDrawingMode = true;
                    canvas.skipTargetFind = true;
                    let brush = new fabric.PencilBrush(canvas);
                    brush.width = parseInt(document.getElementById('brush-size').value);
                    brush.color = (currentTool === 'highlighter') ? currentColor + '44' : currentColor;
                    if (currentTool === 'highlighter') brush.width *= 5;
                    canvas.freeDrawingBrush = brush;
                }
                else if (currentTool === 'select') {
                    canvas.skipTargetFind = false;
                    canvas.selection = true;
                }
                else if (currentTool === 'eraser') {
                    canvas.skipTargetFind = false;
                    canvas.on('mouse:down', eraserHandler);
                }
                else if (currentTool === 'rect' || currentTool === 'circle' || currentTool === 'hand') {
                    canvas.skipTargetFind = false;
                }
                canvas.renderAll();
            }

            function eraserHandler(opt) {
                if (opt.target) { canvas.remove(opt.target); saveState(); }
            }

            function saveState() {
                history.push(JSON.stringify(canvas));
                if (history.length > 30) history.shift();
            }

            // Динамическая палитра
            function addColorToPalette(color) {
                currentColor = color;
                const palette = document.getElementById('palette-container');
                const exists = Array.from(palette.querySelectorAll('.color-btn')).some(b => b.dataset.color === color);

                if (!exists) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.dataset.color = color;
                    btn.className = 'color-btn w-6 h-6 rounded-full ring-2 ring-offset-2 ring-transparent transition-transform hover:scale-110';
                    btn.style.backgroundColor = color;
                    btn.onclick = function() { selectColor(this); };
                    palette.insertBefore(btn, document.getElementById('color-picker').parentElement);
                }
                updateBrush();
            }

            function selectColor(btn) {
                document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('ring-black'));
                btn.classList.add('ring-black');
                currentColor = btn.dataset.color;
                document.getElementById('color-picker').value = currentColor;
                updateBrush();
            }

            // Масштабирование
            function fitToScreen() {
                const container = document.getElementById('canvas-container');
                const viewport = container.parentElement;
                const scale = Math.min((viewport.clientWidth - 80) / canvasWidth, (viewport.clientHeight - 80) / canvasHeight);
                container.style.transform = scale < 1 ? `scale(${scale})` : `scale(1)`;
            }

            // Кнопки инструментов
            document.querySelectorAll('.tool-btn').forEach(btn => {
                btn.onclick = () => {
                    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentTool = btn.dataset.tool;
                    updateBrush();
                };
            });

            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.onclick = function() { selectColor(this); };
            });

            document.getElementById('color-picker').onchange = (e) => addColorToPalette(e.target.value);
            document.getElementById('brush-size').oninput = updateBrush;

            document.getElementById('undo-btn').onclick = () => {
                if (history.length > 1) {
                    history.pop();
                    canvas.loadFromJSON(history[history.length - 1], () => canvas.renderAll());
                } else if (history.length === 1) {
                    history.pop();
                    canvas.clear().setBackgroundColor('#ffffff');
                }
            };

            // Переключение страниц
            function switchPage(index) {
                pages[currentPage] = canvas.toJSON();
                currentPage = index;
                canvas.clear().setBackgroundColor('#ffffff');
                canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
                history = [];
                if (pages[currentPage]) {
                    canvas.loadFromJSON(pages[currentPage], () => { canvas.renderAll(); saveState(); });
                }
                updatePaginationUI();
            }

            function updatePaginationUI() {
                document.getElementById('current-page-num').innerText = currentPage + 1;
                document.getElementById('total-pages-num').innerText = pages.length;
                document.getElementById('prev-page').disabled = currentPage === 0;
                document.getElementById('next-page').disabled = currentPage === pages.length - 1;
            }

            document.getElementById('add-page').onclick = () => { pages.push(null); switchPage(pages.length - 1); };
            document.getElementById('prev-page').onclick = () => { if (currentPage > 0) switchPage(currentPage - 1); };
            document.getElementById('next-page').onclick = () => { if (currentPage < pages.length - 1) switchPage(currentPage + 1); };

            // Сохранение
            document.getElementById('handwriting-form').onsubmit = function(e) {
                e.preventDefault();

                // Снимаем выделение с объектов, чтобы они не сохранялись в "активном" синем состоянии
                canvas.discardActiveObject();

                // Сохраняем текущую страницу в массив перед отправкой
                pages[currentPage] = canvas.toObject();

                // Фильтруем пустые страницы, если нужно, или отправляем как есть
                document.getElementById('content_json').value = JSON.stringify(pages);

                // Генерируем превью (картинку первой страницы или текущей)
                document.getElementById('content_base64').value = canvas.toDataURL({
                    format: 'png',
                    quality: 0.3
                });

                this.submit();
            };

            initCanvas();
        });
    </script>
@endsection
