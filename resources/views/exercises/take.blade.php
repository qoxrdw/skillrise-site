@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white px-8 pt-8 pb-20 font-sans">
        <div class="max-w-[1000px]">

            {{-- Навигация (Back link) --}}
            <div class="mb-8">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center gap-2 text-[18px] text-black/50 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Назад к треку') }}
                </a>
            </div>

            {{-- Заголовок страницы (Стиль как в синем боксе, но белый/серый) --}}
            <div class="mb-12 bg-[#F9F9F9] border border-black rounded-[30px] p-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-[16px] font-medium text-black/40 uppercase tracking-widest">{{ __('Упражнение') }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-black/20"></span>
                    <span class="text-[16px] font-medium text-black/40 uppercase tracking-widest">
                        {{ count($exercise->content) }} {{ __('вопросов') }}
                    </span>
                </div>
                <h1 class="text-[48px] font-normal text-black leading-tight">
                    {{ $exercise->title }}
                </h1>
            </div>

            {{-- Форма --}}
            <form method="POST" action="{{ route('exercises.submit', [$track, $exercise]) }}" id="exercise-form" class="space-y-6">
                @csrf

                <div class="space-y-6 mb-10">
                    @foreach($exercise->content as $index => $item)
                        <div class="p-8 bg-white border border-black rounded-[30px] transition-all">
                            <div class="flex items-start gap-6 mb-6">
                                {{-- Номер вопроса --}}
                                <div class="flex-shrink-0 w-12 h-12 rounded-full border border-black flex items-center justify-center text-[20px] font-medium">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Текст вопроса --}}
                                <div class="flex-1 pt-2">
                                    <label for="answer_{{ $index }}" class="block text-[24px] font-normal text-black leading-tight">
                                        {{ $item['question'] }}
                                    </label>
                                </div>
                            </div>

                            {{-- Поле ответа --}}
                            <div class="ml-18">
                                <input
                                    type="text"
                                    name="answers[{{ $index }}]"
                                    id="answer_{{ $index }}"
                                    class="w-full h-[60px] px-8 border border-black rounded-full text-[20px] focus:ring-0 focus:border-black placeholder-black/20 transition-all @error('answers.'.$index) border-red-500 @enderror"
                                    placeholder="{{ __('Ваш ответ здесь...') }}"
                                    required
                                >
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Панель прогресса и действий --}}
                <div class="sticky bottom-8 bg-white/80 backdrop-blur-md border border-black rounded-[30px] p-6 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
                    <div class="flex-1 w-full sm:w-auto">
                        <div class="flex items-center justify-between mb-2 px-2">
                            <span class="text-[16px] font-medium text-black/50 uppercase tracking-wide">{{ __('Ваш прогресс') }}</span>
                            <span class="text-[18px] font-medium" id="progress-text">0 / {{ count($exercise->content) }}</span>
                        </div>
                        <div class="w-full h-3 bg-black/5 rounded-full overflow-hidden border border-black/10">
                            <div id="progress-bar" class="h-full bg-black rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <a href="{{ route('tracks.show', $track) }}" class="h-[60px] px-8 border border-black rounded-full text-[20px] hover:bg-gray-50 flex items-center justify-center transition-colors">
                            {{ __('Отмена') }}
                        </a>

                        <button
                            type="submit"
                            id="submit-btn"
                            class="h-[60px] px-10 bg-black text-white border border-black rounded-full text-[20px] hover:bg-black/90 transition-all flex items-center justify-center gap-3 shadow-lg"
                        >
                            <span>{{ __('Завершить') }}</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('exercise-form');
            const inputs = form.querySelectorAll('input[type="text"]');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const totalQuestions = {{ count($exercise->content) }};

            function updateProgress() {
                let filled = 0;
                inputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        filled++;
                    }
                });

                const percentage = (filled / totalQuestions) * 100;
                progressBar.style.width = percentage + '%';
                progressText.textContent = filled + ' / ' + totalQuestions;
            }

            inputs.forEach(input => {
                input.addEventListener('input', updateProgress);
            });

            // Первоначальный вызов для учета автозаполнения
            updateProgress();

            // Логика кнопки при отправке
            const submitBtn = document.getElementById('submit-btn');
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.innerHTML = `<span>{{ __('Проверка...') }}</span>`;
            });
        });
    </script>
@endsection
