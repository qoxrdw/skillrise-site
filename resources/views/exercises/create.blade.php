@extends('layouts.app')

@section('content')
    {{-- Основной контейнер с белым фоном и вертикальным отступом --}}
    <div class="py-12 bg-white">
        {{-- Контейнер для контента с автоматическими горизонтальными отступами для центрирования (~50%) --}}
        <div class="mx-auto sm:px-24 lg:px-80">

            {{-- Ссылка "Назад" --}}
            {{-- Добавлен блок ссылки "Назад" --}}
            <div class="mb-6">
                <a href="{{ route('exercises.index', $track) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors duration-300">
                    {{-- Иконка стрелки назад --}}
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Назад к упражнениям трека') }}
                </a>
            </div>

            {{-- Заголовок страницы --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">{{ __('Создать упражнение для трека') }} "{{ $track->name }}"</h1>

            {{-- Сообщения сессии (ошибки) --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg shadow-sm">
                    <div class="font-semibold mb-2">{{ __('Обнаружены ошибки:') }}</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Форма создания упражнения --}}
            <div class="bg-white">
                <form method="POST" action="{{ route('exercises.store', $track) }}">
                    @csrf
                    <div class="p-6 sm:p-8">
                        <div class="mb-6">
                            <label for="title" class="block text-lg font-semibold text-gray-700 mb-2">{{ __('Название упражнения') }}</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror" required>
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="questions" class="space-y-6">
                            @php
                                $questions = old('questions', []);
                                $answers = old('answers', []);
                                $questionCount = max(count($questions), 1); // Минимально 1 вопрос
                            @endphp
                            @for ($i = 0; $i < $questionCount; $i++)
                                {{-- Блок вопроса и поля ввода ответа --}}
                                <div class="question p-6 bg-gray-50 rounded-xl border border-gray-200">
                                    <label for="question_{{ $i }}" class="block text-lg font-semibold text-gray-700 mb-2">{{ __('Вопрос') }} {{ $i + 1 }}</label>
                                    <input type="text" name="questions[{{ $i }}]" id="question_{{ $i }}" value="{{ $questions[$i] ?? '' }}"
                                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('questions.'.$i) border-red-500 @enderror" required>
                                    @error('questions.'.$i)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror

                                    <label for="answer_{{ $i }}" class="block text-lg font-semibold text-gray-700 mt-4 mb-2">{{ __('Правильный ответ') }}</label>
                                    <input type="text" name="answers[{{ $i }}]" id="answer_{{ $i }}" value="{{ $answers[$i] ?? '' }}"
                                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('answers.'.$i) border-red-500 @enderror" required>
                                    @error('answers.'.$i)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endfor
                        </div>

                        {{-- Кнопки действий --}}
                        <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                            {{-- Кнопка "Добавить вопрос" --}}
                            <button type="button" id="add-question-button"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{-- Иконка добавления --}}
                                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Добавить вопрос') }}
                            </button>
                            {{-- Кнопка "Сохранить упражнение" --}}
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                                {{-- Иконка сохранения --}}
                                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Сохранить упражнение') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Подключение вашего скрипта для добавления вопросов --}}
    @vite('resources/js/add-question.js')
@endsection
