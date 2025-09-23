document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('form.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            if (!confirm('Вы точно хотите удалить эту заметку?')) {
                event.preventDefault();
            }
        });
    });
});
