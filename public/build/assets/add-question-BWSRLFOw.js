document.addEventListener("DOMContentLoaded",function(){let e=document.querySelectorAll(".question").length;const n=document.getElementById("add-question-button");n?n.addEventListener("click",function(){const o=document.getElementById("questions"),t=document.createElement("div");t.classList.add("question","mb-4"),t.innerHTML=`
                <label for="question_${e}" class="block text-gray-700 font-semibold mb-2">Вопрос ${e+1}</label>
                <input type="text" name="questions[${e}]" id="question_${e}" class="w-full p-2 border rounded" required>
                <label for="answer_${e}" class="block text-gray-700 font-semibold mt-2 mb-2">Правильный ответ</label>
                <input type="text" name="answers[${e}]" id="answer_${e}" class="w-full p-2 border rounded" required>
            `,o.appendChild(t),e++}):console.error("Add question button not found")});
