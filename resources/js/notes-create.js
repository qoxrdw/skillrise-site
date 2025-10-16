document.addEventListener('DOMContentLoaded', function () {
    console.log('Notes create script loaded');
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

    const form = document.getElementById('note-form');
    if (!form) {
        console.error('Note form not found');
        return;
    }

    form.addEventListener('submit', function(event) {
        // !!! 1. ПРЕДОТВРАЩАЕМ СТАНДАРТНУЮ ОТПРАВКУ, ЧТОБЫ ЗАПОЛНИТЬ СКРЫТОЕ ПОЛЕ !!!
        event.preventDefault();

        console.log('Submit event triggered');
        const contentInput = document.querySelector('#content');
        if (!contentInput) {
            console.error('Content input not found');
            return;
        }

        // !!! 2. ГЛАВНОЕ ИСПРАВЛЕНИЕ: ПОЛУЧЕНИЕ И УСТАНОВКА КОНТЕНТА !!!
        // Получаем HTML-контент из Quill.root.innerHTML
        const htmlContent = quill.root.innerHTML.trim();

        // Устанавливаем контент в скрытое поле
        contentInput.value = htmlContent;

        // Логика для случая, если пользователь ввел пустой контент (только <p><br></p>)
        if (htmlContent === '' || htmlContent === '<p><br></p>') {
            // Если пусто, устанавливаем минимальное значение, чтобы пройти валидацию 'required'
            contentInput.value = '<p>Пустая заметка</p>';
        }

        console.log('Content input set:', contentInput.value.substring(0, 50) + '...');

        // !!! 3. ОТПРАВЛЯЕМ ФОРМУ ВРУЧНУЮ !!!
        this.submit();
    });
});
