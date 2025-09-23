document.addEventListener('DOMContentLoaded', function () {
    console.log('Notes create script loaded');
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Введите текст заметки...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                ['blockquote', 'code-block'],

                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent


                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                [{ 'font': [] }],
                [{ 'align': [] }],

                ['link'],

                ['clean']                                         // remove formatting button
            ]
        }
    });
    console.log('Quill initialized');

    const form = document.getElementById('note-form');
    if (!form) {
        console.error('Note form not found');
        return;
    }
    console.log('Note form found');

    form.addEventListener('submit', function(event) {
        console.log('Submit event triggered');
        const contentInput = document.querySelector('#content');
        if (!contentInput) {
            console.error('Content input not found');
            event.preventDefault();
            return;
        }
        const content = quill.root.innerHTML;
        console.log('Quill content:', content);
        contentInput.value = content;
        console.log('Content set to:', contentInput.value);
    });
});
