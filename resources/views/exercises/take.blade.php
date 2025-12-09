@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-4xl mx-auto px-4">

            {{-- Back link with enhanced style --}}
            <div class="mb-6">
                <a href="{{ route('exercises.index', $track) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border-2 border-gray-200 text-gray-600 hover:border-purple-300 hover:text-purple-600 hover:bg-purple-50 transition-all duration-300 shadow-sm hover:shadow-md group">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ __('Назад к упражнениям трека') }}</span>
                </a>
            </div>

            {{-- Header with gradient --}}
            <div class="mb-8">
                <div class="relative rounded-[24px] border-2 border-purple-200 bg-gradient-to-br from-purple-50 via-blue-50 to-white overflow-hidden shadow-lg">
                    {{-- Декоративные элементы --}}
                    <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-purple-200/30 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-blue-200/30 to-transparent rounded-full blur-2xl"></div>

                    <div class="relative px-6 md:px-10 py-8">
                        <div class="flex items-center gap-4">
                            {{-- Иконка --}}
                            <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-1 rounded-lg uppercase tracking-wide">{{ __('Упражнение') }}</span>
                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                                        {{ count($exercise->content) }} {{ __('вопросов') }}
                                    </span>
                                </div>
                                <h1 class="text-[28px] md:text-[36px] leading-tight font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-blue-900 bg-clip-text text-transparent">
                                    {{ $exercise->title }}
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Instructions card --}}
            <div class="mb-6 p-6 rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-cyan-50 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Инструкция') }}</h3>
                        <p class="text-gray-600">{{ __('Ответьте на все вопросы и нажмите кнопку завершения. После этого вы увидите результаты с правильными ответами.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('exercises.submit', [$track, $exercise]) }}" id="exercise-form">
                @csrf

                <div class="space-y-4 mb-8">
                    @foreach($exercise->content as $index => $item)
                        <div class="group p-6 md:p-8 bg-white rounded-2xl border-2 border-gray-200 hover:border-purple-300 hover:shadow-lg transition-all duration-300" style="animation: slideIn 0.3s ease-out {{ $index * 0.1 }}s backwards;">
                            <div class="flex items-start gap-4 mb-4">
                                {{-- Question number --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center text-white font-bold shadow-md group-hover:scale-110 transition-transform duration-300">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Question text --}}
                                <div class="flex-1">
                                    <label for="answer_{{ $index }}" class="block text-lg font-semibold text-gray-900 mb-1">
                                        {{ $item['question'] }}
                                    </label>
                                    <p class="text-sm text-gray-500">{{ __('Введите ваш ответ ниже') }}</p>
                                </div>
                            </div>

                            {{-- Answer input --}}
                            <div class="ml-14">
                                <input
                                    type="text"
                                    name="answers[{{ $index }}]"
                                    id="answer_{{ $index }}"
                                    class="w-full h-12 px-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-gray-900 placeholder-gray-400 @error('answers.'.$index) border-red-500 focus:border-red-500 focus:ring-red-100 @enderror"
                                    placeholder="{{ __('Ваш ответ...') }}"
                                    required
                                >
                                @error('answers.'.$index)
                                <div class="flex items-center gap-2 mt-2 text-red-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm font-medium">{{ $message }}</p>
                                </div>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Progress indicator --}}
                <div class="mb-8 p-6 rounded-2xl border-2 border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-gray-700">{{ __('Прогресс') }}</span>
                        <span class="text-sm font-medium text-purple-600" id="progress-text">0 / {{ count($exercise->content) }}</span>
                    </div>
                    <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div id="progress-bar" class="h-full bg-gradient-to-r from-purple-500 to-blue-600 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('exercises.index', $track) }}" class="h-12 px-6 rounded-xl border-2 border-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 font-medium flex items-center justify-center transition-all duration-300 shadow-sm hover:shadow-md">
                        {{ __('Отменить') }}
                    </a>

                    <button
                        type="submit"
                        class="group relative h-12 px-8 rounded-xl border-2 border-transparent bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 text-white font-semibold flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden"
                        id="submit-btn"
                    >
                        {{-- Анимированный фон --}}
                        <span class="absolute inset-0 bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>

                        {{-- Блик --}}
                        <span class="absolute inset-0 translate-x-[-100%] group-hover:translate-x-[100%] bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700"></span>

                        <span class="relative">{{ __('Завершить упражнение') }}</span>
                        <svg class="relative w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        // Progress tracking
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

        // Form submission
        const submitBtn = document.getElementById('submit-btn');
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>${{ __('Проверка...') }}</span>
            `;
        });
    </script>
@endsection
