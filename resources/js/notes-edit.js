document.addEventListener('DOMContentLoaded', function () {
    // ─── Настройка шрифтов ───────────────────────────────────────────────────
    const Font = Quill.import('formats/font');
    const FONT_WHITELIST = [false, 'roboto', 'montserrat', 'playfair', 'monospace'];
    Font.whitelist = FONT_WHITELIST;
    Quill.register(Font, true);

    // ─── Парсим существующий контент ─────────────────────────────────────────
    // Поддерживаем оба формата: старый (plain HTML) и новый (JSON массив страниц)
    const rawContent = document.getElementById('editor').innerHTML.trim();
    let pages = [];

    try {
        const parsed = JSON.parse(rawContent);
        if (Array.isArray(parsed)) {
            // Новый формат: [{html: '...'}, ...]
            pages = parsed;
        } else {
            // JSON но не массив — оборачиваем
            pages = [{ html: rawContent }];
        }
    } catch (e) {
        // Старый формат: plain HTML — оборачиваем в массив из одной страницы
        pages = [{ html: rawContent }];
    }

    // Гарантируем что хотя бы одна страница есть
    if (pages.length === 0) pages = [{ html: '' }];

    let currentPage = 0;

    // ─── Инициализация Quill ─────────────────────────────────────────────────
    // Очищаем редактор перед инициализацией
    document.getElementById('editor').innerHTML = '';

    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Начните писать свою заметку...',
        modules: {
            toolbar: [
                [{ 'font': FONT_WHITELIST }],
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Загружаем первую страницу
    quill.root.innerHTML = pages[0].html || '';
    quill.history.clear();

    // ─── DOM элементы ────────────────────────────────────────────────────────
    const thumbnailsContainer = document.getElementById('page-thumbnails');
    const addPageBtn          = document.getElementById('add-page-btn');
    const pageCounter         = document.getElementById('page-counter');

    // ─── Сохраняем контент текущей страницы ──────────────────────────────────
    function saveCurrentPage() {
        const html = quill.root.innerHTML;
        pages[currentPage].html = (html === '<p><br></p>') ? '' : html;
    }

    // ─── Загружаем страницу в редактор ───────────────────────────────────────
    function loadPage(index) {
        saveCurrentPage();
        currentPage = index;
        quill.root.innerHTML = pages[index].html || '';
        quill.history.clear();
        renderThumbnails();
        updatePageCounter();
    }

    // ─── Счётчик страниц ─────────────────────────────────────────────────────
    function updatePageCounter() {
        if (pageCounter) {
            pageCounter.textContent = `${currentPage + 1} / ${pages.length}`;
        }
    }

    // ─── Рендер миниатюр ─────────────────────────────────────────────────────
    function renderThumbnails() {
        thumbnailsContainer.innerHTML = '';

        pages.forEach((page, index) => {
            const thumb = document.createElement('div');
            thumb.className = [
                'relative group cursor-pointer rounded-lg border-2 transition-all duration-150',
                'bg-white overflow-hidden flex-shrink-0',
                index === currentPage
                    ? 'border-black shadow-md'
                    : 'border-gray-200 hover:border-gray-400'
            ].join(' ');
            thumb.style.cssText = 'width:140px; height:100px;';

            // Превью контента
            const preview = document.createElement('div');
            preview.className = 'w-full h-full p-2 overflow-hidden pointer-events-none';
            preview.style.cssText = 'transform: scale(0.3); transform-origin: top left; width: 467px; height: 333px;';
            preview.innerHTML = page.html || '<p style="color:#ccc">Пустая страница</p>';
            thumb.appendChild(preview);

            // Номер страницы
            const badge = document.createElement('div');
            badge.className = [
                'absolute bottom-1 left-1/2 -translate-x-1/2',
                'text-[10px] font-medium px-1.5 py-0.5 rounded',
                index === currentPage ? 'bg-black text-white' : 'bg-gray-100 text-gray-500'
            ].join(' ');
            badge.textContent = index + 1;
            thumb.appendChild(badge);

            // Кнопка удаления
            if (pages.length > 1) {
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center';
                deleteBtn.innerHTML = '×';
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    deletePage(index);
                });
                thumb.appendChild(deleteBtn);
            }

            thumb.addEventListener('click', () => loadPage(index));
            thumbnailsContainer.appendChild(thumb);
        });
    }

    // ─── Добавить страницу ───────────────────────────────────────────────────
    function addPage() {
        saveCurrentPage();
        pages.push({ html: '' });
        loadPage(pages.length - 1);
    }

    // ─── Удалить страницу ────────────────────────────────────────────────────
    function deletePage(index) {
        if (pages.length <= 1) return;
        pages.splice(index, 1);
        const newIndex = index >= pages.length ? pages.length - 1 : index;
        currentPage = newIndex;
        quill.root.innerHTML = pages[currentPage].html || '';
        quill.history.clear();
        renderThumbnails();
        updatePageCounter();
    }

    // ─── Обработка формы ─────────────────────────────────────────────────────
    const form = document.getElementById('note-form');
    if (form) {
        form.addEventListener('submit', function () {
            saveCurrentPage();
            const contentInput = document.getElementById('content');
            contentInput.value = JSON.stringify(pages);
        });
    }

    // ─── Кнопка добавления страницы ──────────────────────────────────────────
    if (addPageBtn) {
        addPageBtn.addEventListener('click', addPage);
    }

    // ─── Экспорт в PDF ───────────────────────────────────────────────────────
    const exportBtn = document.getElementById('export-pdf-btn');
    const exportLabel = document.getElementById('export-pdf-label');

    if (exportBtn) {
        exportBtn.addEventListener('click', async () => {
            // Сохраняем текущую страницу перед экспортом
            saveCurrentPage();

            exportBtn.disabled = true;
            exportLabel.textContent = 'Генерация...';

            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

                const A4_W = 210; // мм
                const A4_H = 297; // мм

                for (let i = 0; i < pages.length; i++) {
                    // Создаём временный скрытый div с контентом страницы
                    const container = document.createElement('div');
                    container.style.cssText = `
                        position: fixed;
                        left: -9999px;
                        top: 0;
                        width: 794px;
                        min-height: 1123px;
                        background: white;
                        padding: 60px 70px;
                        font-size: 16px;
                        line-height: 1.75;
                        font-family: sans-serif;
                        box-sizing: border-box;
                    `;
                    container.innerHTML = pages[i].html || '<p>&nbsp;</p>';
                    document.body.appendChild(container);

                    // Рендерим в canvas
                    const canvas = await html2canvas(container, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        width: 794,
                        height: 1123,
                    });

                    document.body.removeChild(container);

                    const imgData = canvas.toDataURL('image/jpeg', 0.92);

                    // Добавляем страницу в PDF (не первую — с новой страницы)
                    if (i > 0) pdf.addPage();

                    pdf.addImage(imgData, 'JPEG', 0, 0, A4_W, A4_H);

                    exportLabel.textContent = `${i + 1} / ${pages.length}`;
                }

                // Имя файла из заголовка заметки
                const titleInput = document.querySelector('input[name="title"]');
                const fileName = (titleInput?.value?.trim() || 'заметка') + '.pdf';
                pdf.save(fileName);

            } catch (err) {
                console.error('PDF export error:', err);
                alert('Не удалось создать PDF. Попробуйте ещё раз.');
            } finally {
                exportBtn.disabled = false;
                exportLabel.textContent = 'PDF';
            }
        });
    }

    // ─── Инициализация ───────────────────────────────────────────────────────
    renderThumbnails();
    updatePageCounter();
});
