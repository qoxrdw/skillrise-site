document.addEventListener('DOMContentLoaded', function () {
    console.log('Notes edit script loaded');

    // 1. ИМПОРТ И НАСТРОЙКА ШРИФТОВ QUILL
    const Font = Quill.import('formats/font');
    const Size = Quill.import('formats/size');
    // Список шрифтов
    const FONT_WHITELIST = [
        'sans-serif',    // Work Sans (по умолчанию)
        'serif',         // Times New Roman
        'roboto',        // Roboto
        'opensans',      // Open Sans
        'montserrat',    // Montserrat
        'raleway',       // Raleway
        'georgia',       // Georgia
        'verdana',       // Verdana
        'spectral',      // Spectral
        'merriweather'   // Merriweather
    ];

    // Список размеров шрифтов (как в Word)
    const SIZE_WHITELIST = [
        '8px', '9px', '10px', '11px', '12px', '14px', '16px', '18px',
        '20px', '22px', '24px', '26px', '28px', '36px', '48px', '72px'
    ];

    // Настройка статических свойств
    Font.whitelist = FONT_WHITELIST;
    Font.default = 'sans-serif';

    Size.whitelist = SIZE_WHITELIST;

    // Регистрируем настроенные модули в Quill
    Quill.register(Font, true);
    Quill.register(Size, true);

    // 2. Инициализация Quill с настроенным тулбаром
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Введите текст заметки...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'size': SIZE_WHITELIST }], // Добавлен выбор размера
                [{ 'font': FONT_WHITELIST }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],

                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],

                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],

                ['link'],

                ['clean']
            ]
        }
    });
    console.log('Quill initialized for editing');

    // 2.1 Загрузка существующего контента
    const contentInput = document.querySelector('#content');
    if (contentInput && contentInput.value) {
        try {
            quill.root.innerHTML = contentInput.value;
            console.log('Existing content loaded');
        } catch (e) {
            console.error('Error loading content:', e);
        }
    }

    // 2.2 ИСПРАВЛЕНИЕ: Сохранение форматирования при нажатии Enter
    // Удаляем стандартный биндинг Enter
    delete quill.keyboard.bindings['Enter'];

    // Добавляем свой биндинг с сохранением форматирования
    quill.keyboard.addBinding({
        key: 'Enter',
        handler: function(range, context) {
            // Сохраняем текущее форматирование символа (не строки!)
            const currentFormat = quill.getFormat(range.index);

            // Вставляем новую строку стандартным способом
            this.quill.insertText(range.index, '\n');

            // Устанавливаем курсор на новую строку
            this.quill.setSelection(range.index + 1);

            // Применяем форматирование к тексту после курсора
            if (currentFormat.font) {
                // Небольшая задержка для применения форматирования
                setTimeout(() => {
                    const sel = this.quill.getSelection();
                    if (sel) {
                        this.quill.format('font', currentFormat.font);
                    }
                }, 0);
            }

            return false;
        }
    });

    // 3. Логика отправки формы
    const form = document.getElementById('note-form');
    if (!form) {
        console.error('Note form not found');
        return;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        console.log('Submit event triggered');

        const htmlContent = quill.root.innerHTML.trim();
        contentInput.value = htmlContent;

        if (htmlContent === '' || htmlContent === '<p><br></p>') {
            contentInput.value = '<p>Пустая заметка</p>';
        }

        console.log('Content updated, submitting form');
        this.submit();
    });
});
