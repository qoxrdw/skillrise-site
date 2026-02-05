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

            {{-- Заголовок страницы --}}
            <div class="mb-12">
                <h1 class="text-[48px] font-normal text-black leading-tight">
                    {{ __('Создать упражнение') }}
                </h1>
                <p class="text-[20px] text-black/40 mt-2">Трек: {{ $track->name }}</p>
            </div>

            {{-- Ошибки валидации --}}
            @if ($errors->any())
                <div class="mb-10 p-6 border-2 border-black rounded-[30px] bg-red-50">
                    <div class="text-[20px] font-medium text-red-600 mb-2">{{ __('Пожалуйста, исправьте ошибки:') }}</div>
                    <ul class="list-disc pl-5 text-red-600/80">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Основная форма --}}
            <form method="POST" action="{{ route('exercises.store', $track) }}" class="space-y-10">
                @csrf

                {{-- Поле: Название --}}
                <div class="bg-white border border-black rounded-[30px] p-8">
                    <label for="title" class="block text-[24px] font-medium mb-4">{{ __('Название упражнения') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                           placeholder="Например: Тест по генетике"
                           class="w-full h-[60px] px-8 border border-black rounded-full text-[20px] focus:ring-0 focus:border-black placeholder-black/30 @error('title') border-red-500 @enderror"
                           required>
                </div>

                {{-- Секция вопросов --}}
                <div id="questions" class="space-y-6">
                    <h2 class="text-[24px] font-medium px-4">{{ __('Вопросы') }}</h2>

                    @php
                        $questions = old('questions', []);
                        $answers = old('answers', []);
                        $questionCount = max(count($questions), 1);
                    @endphp

                    @for ($i = 0; $i < $questionCount; $i++)
                        <div class="question bg-[#F9F9F9] border border-black rounded-[30px] p-8 relative group">
                            <div class="grid gap-6">
                                <div>
                                    <label class="block text-[18px] text-black/50 mb-2 uppercase tracking-wide">{{ __('Вопрос') }} {{ $i + 1 }}</label>
                                    <input type="text" name="questions[{{ $i }}]" value="{{ $questions[$i] ?? '' }}"
                                           class="w-full h-[55px] px-6 border border-black rounded-[20px] text-[18px] focus:ring-0 focus:border-black"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-[18px] text-black/50 mb-2 uppercase tracking-wide">{{ __('Правильный ответ') }}</label>
                                    <input type="text" name="answers[{{ $i }}]" value="{{ $answers[$i] ?? '' }}"
                                           class="w-full h-[55px] px-6 border border-black rounded-[20px] text-[18px] focus:ring-0 focus:border-black"
                                           required>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Кнопки действий --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
                    <button type="button" id="add-question-button"
                            class="w-full sm:w-auto h-[60px] px-10 border border-black rounded-full text-[20px] hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                        <span>+</span> {{ __('Добавить вопрос') }}
                    </button>

                    <button type="submit"
                            class="w-full sm:w-auto h-[60px] px-10 bg-black text-white border border-black rounded-full text-[20px] hover:bg-black/90 transition-all flex items-center justify-center">
                        {{ __('Сохранить упражнение') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @vite('resources/js/add-question.js')
@endsection
