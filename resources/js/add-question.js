document.addEventListener('DOMContentLoaded', function () {
    let questionCount = document.querySelectorAll('.question').length;
    const addQuestionButton = document.getElementById('add-question-button');

    if (addQuestionButton) {
        addQuestionButton.addEventListener('click', function () {
            const questionsDiv = document.getElementById('questions');
            const newQuestion = document.createElement('div');

            // Добавляем те же классы, что и в основном шаблоне
            newQuestion.classList.add('question', 'bg-[#F9F9F9]', 'border', 'border-black', 'rounded-[30px]', 'p-8', 'relative');

            newQuestion.innerHTML = `
                <div class="grid gap-6">
                    <div>
                        <label class="block text-[18px] text-black/50 mb-2 uppercase tracking-wide">Вопрос ${questionCount + 1}</label>
                        <input type="text" name="questions[${questionCount}]"
                               class="w-full h-[55px] px-6 border border-black rounded-[20px] text-[18px] focus:ring-0 focus:border-black"
                               required>
                    </div>

                    <div>
                        <label class="block text-[18px] text-black/50 mb-2 uppercase tracking-wide">Правильный ответ</label>
                        <input type="text" name="answers[${questionCount}]"
                               class="w-full h-[55px] px-6 border border-black rounded-[20px] text-[18px] focus:ring-0 focus:border-black"
                               required>
                    </div>
                </div>
            `;

            questionsDiv.appendChild(newQuestion);
            questionCount++;
        });
    } else {
        console.error('Add question button not found');
    }
});
