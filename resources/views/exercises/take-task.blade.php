@extends('layouts.app')

@section('content')
    @php
        $feedback = session('feedback');
        $userSolution = session('user_solution');
    @endphp

    <div class="min-h-screen bg-white px-8 pt-8 pb-24 font-sans">
        <div class="max-w-[800px]">

            {{-- Навигация --}}
            <div class="mb-8">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center gap-2 text-[18px] text-black/50 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Назад к треку') }}
                </a>
            </div>

            {{-- Шапка задачи --}}
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-black text-white text-[11px] uppercase tracking-[0.2em] rounded-full">🧠 AI Задача</span>
                </div>
                <h1 class="text-[38px] font-normal leading-tight text-black">
                    {{ $exercise->title }}
                </h1>
            </div>

            {{-- Условие задачи --}}
            <div class="mb-8 p-8 rounded-[28px] border border-black bg-white">
                <div class="text-[12px] uppercase tracking-[0.2em] text-black/40 mb-4">Условие</div>
                <p class="text-[18px] leading-relaxed text-black whitespace-pre-line">{{ $exercise->content['task'] ?? '' }}</p>
            </div>

            {{-- Подсказки (скрытые) --}}
            @if(!empty($exercise->content['hints']))
                <div class="mb-8" x-data="{ open: false }">
                    <button
                        type="button"
                        onclick="toggleHints()"
                        class="flex items-center gap-2 text-[15px] text-black/50 hover:text-black transition-colors mb-4"
                    >
                        <svg id="hints-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                        Показать подсказки ({{ count($exercise->content['hints']) }})
                    </button>

                    <div id="hints-block" class="hidden">
                        <div class="p-6 rounded-[20px] border border-black/20 bg-amber-50">
                            <div class="text-[12px] uppercase tracking-[0.2em] text-black/40 mb-4">💡 Подсказки</div>
                            <ul class="space-y-3">
                                @foreach($exercise->content['hints'] as $hint)
                                    <li class="flex items-start gap-3 text-[16px] text-black/70">
                                        <span class="mt-1 w-5 h-5 flex-shrink-0 rounded-full border border-black/20 flex items-center justify-center text-[11px] font-medium">{{ $loop->iteration }}</span>
                                        {{ $hint }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ошибки --}}
            @if ($errors->any())
                <div class="mb-6 p-5 rounded-[20px] border border-red-400 bg-red-50 text-red-700 text-[15px]">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Фидбек от AI (показывается после проверки) --}}
            @if($feedback)
                <div class="mb-10 rounded-[28px] border-2 overflow-hidden
                {{ $feedback['score'] >= 8 ? 'border-green-500' : ($feedback['score'] >= 5 ? 'border-blue-500' : 'border-red-400') }}">

                    {{-- Шапка с оценкой --}}
                    <div class="px-8 py-6 flex items-center justify-between
                    {{ $feedback['score'] >= 8 ? 'bg-green-50' : ($feedback['score'] >= 5 ? 'bg-blue-50' : 'bg-red-50') }}">
                        <div>
                            <div class="text-[12px] uppercase tracking-[0.2em] text-black/40 mb-1">Результат проверки AI</div>
                            <div class="text-[28px] font-medium text-black">{{ $feedback['verdict'] ?? '' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[56px] font-light leading-none text-black">{{ $feedback['score'] }}</div>
                            <div class="text-[14px] text-black/40">из 10</div>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-white space-y-6">

                        {{-- Плюсы --}}
                        @if(!empty($feedback['strengths']))
                            <div>
                                <div class="text-[12px] uppercase tracking-[0.2em] text-green-600 mb-3">✓ Что хорошо</div>
                                <ul class="space-y-2">
                                    @foreach($feedback['strengths'] as $strength)
                                        <li class="flex items-start gap-3 text-[16px] text-black/80">
                                            <span class="mt-1 text-green-500">•</span>
                                            {{ $strength }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Ошибки --}}
                        @if(!empty($feedback['mistakes']))
                            <div>
                                <div class="text-[12px] uppercase tracking-[0.2em] text-red-500 mb-3">✗ Замечания</div>
                                <ul class="space-y-2">
                                    @foreach($feedback['mistakes'] as $mistake)
                                        <li class="flex items-start gap-3 text-[16px] text-black/80">
                                            <span class="mt-1 text-red-400">•</span>
                                            {{ $mistake }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Совет --}}
                        @if(!empty($feedback['advice']))
                            <div class="pt-4 border-t border-black/10">
                                <div class="text-[12px] uppercase tracking-[0.2em] text-black/40 mb-2">Совет</div>
                                <p class="text-[16px] text-black/70 italic">{{ $feedback['advice'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Показываем решение пользователя после фидбека --}}
                <div class="mb-10 p-6 rounded-[20px] border border-black/10 bg-gray-50">
                    <div class="text-[12px] uppercase tracking-[0.2em] text-black/30 mb-3">Ваше решение</div>
                    <p class="text-[16px] text-black/60 whitespace-pre-line leading-relaxed">{{ $userSolution }}</p>
                </div>
            @endif

            {{-- Форма решения --}}
            <form method="POST" action="{{ route('exercises.submit-task', [$track, $exercise]) }}" id="task-form">
                @csrf

                <div class="mb-6">
                    <label class="block text-[18px] font-normal text-black mb-3">
                        {{ $feedback ? 'Попробовать ещё раз' : 'Ваше решение' }}
                    </label>
                    <p class="text-[14px] text-black/40 mb-4">
                        Опишите ход рассуждений, шаги и выводы. AI оценит глубину понимания темы.
                    </p>
                    <textarea
                        name="solution"
                        id="solution"
                        rows="12"
                        placeholder="Начните писать своё решение здесь..."
                        class="w-full px-6 py-5 text-[17px] leading-relaxed border border-black rounded-[20px] bg-white resize-y
                           focus:outline-none focus:ring-2 focus:ring-black/20 focus:border-black
                           placeholder:text-black/25 transition-all"
                        required
                        minlength="10"
                    >{{ old('solution') }}</textarea>

                    {{-- Счётчик символов --}}
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-[13px] text-black/30" id="char-hint">Минимум ~10 символов</div>
                        <div class="text-[13px] text-black/30"><span id="char-count">0</span> символов</div>
                    </div>
                </div>

                {{-- Кнопки --}}
                <div class="flex items-center justify-between pt-6 border-t border-black/5">
                    <a href="{{ route('exercises.index', $track) }}"
                       class="h-[55px] px-8 rounded-full text-[16px] text-black/50 hover:text-black hover:bg-gray-50 flex items-center justify-center transition-all">
                        {{ __('К упражнениям') }}
                    </a>

                    <button type="submit" id="check-btn"
                            class="h-[65px] px-12 bg-black text-white rounded-full text-[18px] flex items-center justify-center gap-3 hover:bg-black/90 transition-all shadow-xl disabled:opacity-50">
                    <span id="check-btn-text" class="flex items-center gap-3">
                        🔍 {{ $feedback ? 'Проверить ещё раз' : 'Проверить решение' }}
                    </span>
                        <div id="check-btn-loading" class="hidden items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>AI проверяет...</span>
                        </div>
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Счётчик символов
        const textarea = document.getElementById('solution');
        const charCount = document.getElementById('char-count');
        const charHint = document.getElementById('char-hint');

        textarea.addEventListener('input', () => {
            const len = textarea.value.length;
            charCount.textContent = len;
            charHint.textContent = len < 10 ? 'Минимум ~10 символов' : 'Отлично, продолжайте';
        });

        // Лоадер при сабмите
        document.getElementById('task-form').addEventListener('submit', () => {
            const btn = document.getElementById('check-btn');
            document.getElementById('check-btn-text').classList.add('hidden');
            const loading = document.getElementById('check-btn-loading');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
            btn.disabled = true;
        });

        // Тогл подсказок
        function toggleHints() {
            const block = document.getElementById('hints-block');
            const chevron = document.getElementById('hints-chevron');
            const isHidden = block.classList.contains('hidden');
            block.classList.toggle('hidden', !isHidden);
            chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        }

        // Если есть фидбек — скроллим к нему
        @if($feedback)
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('[class*="border-2"]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        @endif
    </script>
@endsection
