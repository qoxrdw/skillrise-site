document.addEventListener('DOMContentLoaded', function () {
    console.log('Quill init script loaded');
    const editor = document.querySelector('#editor');
    if (!editor) {
        console.error('Editor element not found');
        return;
    }
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Введите текст заметки...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                ['link', 'blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }]
            ]
        }
    });
    console.log('Quill initialized');

    const form = document.querySelector('form');
    if (!form) {
        console.error('Form not found');
        return;
    }
    console.log('Form found');

    form.addEventListener('submit', function(event) {
        const contentInput = document.querySelector('#content');
        if (!contentInput) {
            console.error('Content input not found');
            event.preventDefault();
            return;
        }
        const content = quill.root.innerHTML;
        console.log('Quill content:', content);
        contentInput.value = content;
        console.log('Content input set to:', contentInput.value);
    });
});
