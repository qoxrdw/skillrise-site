document.addEventListener('DOMContentLoaded', function () {
    const answerButton = document.querySelector('button[onclick="submitAnswers()"]');
    if (answerButton) {
        answerButton.removeAttribute('onclick'); // Удаляем inline-обработчик
        answerButton.addEventListener('click', function () {
            const form = document.querySelector('form');
            form.submit();
        });
    }
});
