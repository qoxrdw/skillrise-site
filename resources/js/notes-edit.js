document.addEventListener('DOMContentLoaded', function () {
    // Настройка шрифтов
    const Font = Quill.import('formats/font');
    const FONT_WHITELIST = [false, 'roboto', 'montserrat', 'playfair', 'monospace'];
    Font.whitelist = FONT_WHITELIST;
    Quill.register(Font, true);

    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Начните писать свою заметку...',
        modules: {
            toolbar: [
                [{ 'font': FONT_WHITELIST }],
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Обработка формы
    const form = document.getElementById('note-form');
    if (form) {
        form.addEventListener('submit', function() {
            const contentInput = document.getElementById('content');
            // Получаем HTML содержимое
            const html = quill.root.innerHTML;

            // Проверка на пустоту (Quill вставляет <p><br></p> в пустой редактор)
            if (html === '<p><br></p>') {
                contentInput.value = '';
            } else {
                contentInput.value = html;
            }
        });
    }
});
