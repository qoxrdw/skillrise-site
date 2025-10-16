document.addEventListener('DOMContentLoaded', function () {
    console.log('Notes edit script loaded');
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Введите текст заметки...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],

                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],

                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],

                ['link'],

                ['clean']
            ]
        }
    });
    console.log('Quill initialized');

    // ⚠️ ВНИМАНИЕ: Заполнение редактора Quill HTML-контентом через innerHTML не всегда безопасно.
    // Лучше использовать quill.clipboard.dangerouslyPasteHTML.
    const contentInput = document.querySelector('#content');
    const initialContent = contentInput ? contentInput.value : '';

    if (initialContent) {
        // Используем безопасный метод для вставки HTML в Quill
        quill.clipboard.dangerouslyPasteHTML(initialContent);
        console.log('Initial content set.');
    }

    const form = document.getElementById('note-form');
    if (!form) {
        console.error('Note form not found');
        return;
    }
    console.log('Note form found');

    form.addEventListener('submit', function(event) {
        // !!! ГЛАВНОЕ ИСПРАВЛЕНИЕ 1: ОТМЕНЯЕМ СТАНДАРТНУЮ ОТПРАВКУ !!!
        event.preventDefault();

        console.log('Submit event triggered');
        const contentInput = document.querySelector('#content');
        if (!contentInput) {
            console.error('Content input not found');
            return;
        }

        const htmlContent = quill.root.innerHTML.trim();
        console.log('Quill content:', htmlContent.substring(0, 50) + '...');

        // Устанавливаем контент в скрытое поле
        contentInput.value = htmlContent;

        // Логика для случая, если пользователь ввел пустой контент (только <p><br></p>)
        if (htmlContent === '' || htmlContent === '<p><br></p>') {
            contentInput.value = '<p>Пустая заметка</p>';
        }

        console.log('Content set to:', contentInput.value.substring(0, 50) + '...');

        // !!! ГЛАВНОЕ ИСПРАВЛЕНИЕ 2: ОТПРАВЛЯЕМ ФОРМУ ВРУЧНУЮ !!!
        this.submit();
    });
});
