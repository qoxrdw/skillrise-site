@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white px-8 pt-8 pb-20 font-sans">
        <div class="max-w-[1000px]">

            {{-- Навигация --}}
            <div class="mb-8">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center gap-2 text-[18px] text-black/50 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Назад к треку') }}
                </a>
            </div>

            {{-- Заголовок --}}
            <div class="mb-12 bg-black text-white border border-black rounded-[30px] p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-20 pointer-events-none">
                    <svg width="400" height="400" viewBox="0 0 400 400" fill="none">
                        <circle cx="400" cy="0" r="300" stroke="white" stroke-width="1" stroke-dasharray="10 10"/>
                        <circle cx="400" cy="0" r="200" stroke="white" stroke-width="1" stroke-dasharray="5 5"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-3 py-1 border border-white/30 rounded-full text-[12px] uppercase tracking-[0.2em]">AI Generation</span>
                        <span class="text-white/40 text-[12px] uppercase tracking-[0.2em]">Pro Feature</span>
                    </div>
                    <h1 class="text-[48px] font-normal leading-tight mb-4">
                        Создание упражнения <br> с помощью искусственного интеллекта
                    </h1>
                    <p class="text-[18px] text-white/60 max-w-[600px] font-light italic">
                        Выберите заметку и формат: тест с вопросами или одна глубокая задача с проверкой AI.
                    </p>
                </div>
            </div>

            {{-- Ошибки --}}
            @if ($errors->any())
                <div class="mb-8 p-6 rounded-[25px] border border-red-500 bg-red-50 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Выбор режима генерации --}}
            <div class="mb-10">
                <h2 class="text-[24px] font-normal mb-6">{{ __('Выберите формат:') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Карточка: Q&A --}}
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="generation_mode" value="qa" class="peer hidden" checked>
                        <div class="h-full flex flex-col p-7 border border-black rounded-[25px] bg-white transition-all
                                    peer-checked:bg-black peer-checked:text-white
                                    group-hover:bg-gray-50 peer-checked:group-hover:bg-black/90">
                            <div class="text-[32px] mb-3">📋</div>
                            <div class="text-[20px] font-medium mb-2 transition-colors">Вопросы и ответы</div>
                            <div class="text-[14px] opacity-60 font-light leading-relaxed">
                                5–8 вопросов с точными ответами для самопроверки. Быстро и удобно.
                            </div>
                        </div>
                    </label>

                    {{-- Карточка: Task --}}
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="generation_mode" value="task" class="peer hidden">
                        <div class="h-full flex flex-col p-7 border border-black rounded-[25px] bg-white transition-all
                                    peer-checked:bg-black peer-checked:text-white
                                    group-hover:bg-gray-50 peer-checked:group-hover:bg-black/90">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-[32px]">🧠</span>
                                <span class="px-2 py-0.5 text-[11px] font-medium uppercase tracking-widest bg-white/20 border border-current rounded-full opacity-80">NEW</span>
                            </div>
                            <div class="text-[20px] font-medium mb-2">Глубокая задача</div>
                            <div class="text-[14px] opacity-60 font-light leading-relaxed">
                                Одна развёрнутая задача. Пишите решение своими словами — AI проверит и даст фидбек с оценкой.
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Выбор заметки --}}
            <div class="mb-10">
                <h2 class="text-[24px] font-normal mb-8">{{ __('Выберите источник:') }}</h2>

                @if($notes->isEmpty())
                    <div class="p-12 text-center rounded-[30px] border border-black border-dashed">
                        <p class="text-[18px] text-black/40 mb-6 italic">{{ __('В этом треке пока нет заметок для анализа.') }}</p>
                        <a href="{{ route('notes.create', $track) }}" class="h-[55px] px-8 border border-black rounded-full inline-flex items-center hover:bg-black hover:text-white transition-all">
                            {{ __('Создать первую заметку') }}
                        </a>
                    </div>
                @else
                    <div class="grid gap-4" id="notes-list">
                        @foreach($notes as $note)
                            <label class="relative group cursor-pointer">
                                <input type="radio" name="note_id_selector" value="{{ $note->id }}" class="peer hidden note-radio" required>
                                <div class="flex items-center justify-between p-6 border border-black rounded-[25px] bg-white transition-all
                                            peer-checked:bg-[#F0F7FF] peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500
                                            group-hover:bg-gray-50">
                                    <div class="flex items-center gap-6">
                                        <div>
                                            <div class="text-[20px] text-black mb-1">
                                                @php
                                                    $icons = ['text' => '📝', 'voice' => '🎙️', 'handwriting' => '🎨'];
                                                    echo ($icons[$note->type] ?? '📄') . ' ' . ($note->title ?: 'Без названия');
                                                @endphp
                                            </div>
                                            <div class="text-[14px] text-black/40 uppercase tracking-widest">
                                                {{ $note->type }} • {{ $note->created_at->isoFormat('LL') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden sm:block text-[12px] font-medium text-black/30 border border-black/10 rounded-lg px-3 py-1 group-hover:border-black/30 transition-colors">
                                        SELECT SOURCE
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Скрытые формы — одна для QA, одна для Task --}}
            <form method="POST" action="{{ route('exercises.generate-ai', $track) }}" id="form-qa">
                @csrf
                <input type="hidden" name="note_id" id="note_id_qa">
            </form>

            <form method="POST" action="{{ route('exercises.generate-task-ai', $track) }}" id="form-task">
                @csrf
                <input type="hidden" name="note_id" id="note_id_task">
            </form>

            {{-- Кнопки действий --}}
            <div class="flex items-center justify-end gap-4 border-t border-black/5 pt-10">
                <a href="{{ route('exercises.index', $track) }}"
                   class="h-[65px] px-10 rounded-full text-[18px] text-black/50 hover:text-black hover:bg-gray-50 flex items-center justify-center transition-all">
                    {{ __('Отмена') }}
                </a>

                <button type="button" id="submit-btn"
                        onclick="submitSelectedForm()"
                        class="h-[65px] px-12 bg-black text-white rounded-full text-[20px] flex items-center justify-center gap-3 hover:bg-black/90 transition-all shadow-xl disabled:opacity-50">
                    <span id="btn-text" class="flex items-center gap-3">
                        <span id="btn-label">Сгенерировать</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                    <div id="btn-loading" class="hidden items-center gap-3">
                        <svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('Нейросеть думает...') }}</span>
                    </div>
                </button>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Обновляем лейбл кнопки при смене режима
        document.querySelectorAll('input[name="generation_mode"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const label = document.getElementById('btn-label');
                label.textContent = radio.value === 'task'
                    ? 'Сгенерировать задачу'
                    : 'Сгенерировать';
            });
        });

        function submitSelectedForm() {
            // Получаем выбранную заметку
            const noteRadio = document.querySelector('input.note-radio:checked');
            if (!noteRadio) {
                alert('Пожалуйста, выберите заметку.');
                return;
            }

            const mode = document.querySelector('input[name="generation_mode"]:checked')?.value ?? 'qa';
            const noteId = noteRadio.value;

            // Показываем лоадер
            const btn = document.getElementById('submit-btn');
            document.getElementById('btn-text').classList.add('hidden');
            const loading = document.getElementById('btn-loading');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
            btn.disabled = true;

            // Подставляем note_id и сабмитим нужную форму
            if (mode === 'task') {
                document.getElementById('note_id_task').value = noteId;
                document.getElementById('form-task').submit();
            } else {
                document.getElementById('note_id_qa').value = noteId;
                document.getElementById('form-qa').submit();
            }
        }
    </script>
@endsection
