document.addEventListener("DOMContentLoaded",function(){let e=document.querySelectorAll(".question").length;const n=document.getElementById("add-question-button");n?n.addEventListener("click",function(){const d=document.getElementById("questions"),t=document.createElement("div");t.classList.add("question","bg-[#F9F9F9]","border","border-black","rounded-[30px]","p-8","relative"),t.innerHTML=`
                <div class="grid gap-6">
                    <div>
                        <label class="block text-[18px] text-black/50 mb-2 uppercase tracking-wide">Вопрос ${e+1}</label>
                        <input type="text" name="questions[${e}]"
                               class="w-full h-[55px] px-6 border border-black rounded-[20px] text-[18px] focus:ring-0 focus:border-black"
                               required>
                    </div>

                    <div>
                        <label class="block text-[18px] text-black/50 mb-2 uppercase tracking-wide">Правильный ответ</label>
                        <input type="text" name="answers[${e}]"
                               class="w-full h-[55px] px-6 border border-black rounded-[20px] text-[18px] focus:ring-0 focus:border-black"
                               required>
                    </div>
                </div>
            `,d.appendChild(t),e++}):console.error("Add question button not found")});
