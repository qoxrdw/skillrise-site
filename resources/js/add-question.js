document.addEventListener('DOMContentLoaded', function () {
    let questionCount = document.querySelectorAll('.question').length;
    const addQuestionButton = document.getElementById('add-question-button');
    if (addQuestionButton) {
        addQuestionButton.addEventListener('click', function () {
            const questionsDiv = document.getElementById('questions');
            const newQuestion = document.createElement('div');
            newQuestion.classList.add('question', 'mb-4');
            newQuestion.innerHTML = `
                <label for="question_${questionCount}" class="block text-gray-700 font-semibold mb-2">Вопрос ${questionCount + 1}</label>
                <input type="text" name="questions[${questionCount}]" id="question_${questionCount}" class="w-full p-2 border rounded" required>
                <label for="answer_${questionCount}" class="block text-gray-700 font-semibold mt-2 mb-2">Правильный ответ</label>
                <input type="text" name="answers[${questionCount}]" id="answer_${questionCount}" class="w-full p-2 border rounded" required>
            `;
            questionsDiv.appendChild(newQuestion);
            questionCount++;
        });
    } else {
        console.error('Add question button not found');
    }
});
