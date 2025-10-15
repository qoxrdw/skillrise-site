@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-6xl mx-auto">

            {{-- Back link --}}
            <div class="mb-4 md:mb-6">
                <a href="{{ route('exercises.index', $track) }}" class="inline-flex items-center text-sm text-black/70 hover:text-black">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Назад к упражнениям трека') }}
                </a>
            </div>

            {{-- Header --}}
            <div class="mb-6 md:mb-8">
                <div class="rounded-[20px] border-2 border-black bg-white">
                    <div class="px-6 md:px-10 py-6 md:py-7">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-black/50 -rotate-90" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            <h1 class="flex-1 text-[28px] md:text-[34px] leading-tight text-black/90 truncate">{{ __('Создать упражнение') }} — "{{ $track->name }}"</h1>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 p-4 border-2 border-black rounded-[14px] bg-white text-red-700">
                    <div class="font-semibold mb-2">{{ __('Обнаружены ошибки:') }}</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <div class="rounded-[20px] border-2 border-black bg-white">
                <form method="POST" action="{{ route('exercises.store', $track) }}">
                    @csrf
                    <div class="p-6 md:p-8">
                        <div class="mb-6">
                            <label for="title" class="block text-base text-black/80 mb-2">{{ __('Название упражнения') }}</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full h-11 px-3 border-2 border-black rounded-[16px] focus:outline-none focus:ring-0 @error('title') border-red-500 @enderror" required>
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="questions" class="space-y-6">
                            @php
                                $questions = old('questions', []);
                                $answers = old('answers', []);
                                $questionCount = max(count($questions), 1);
                            @endphp
                            @for ($i = 0; $i < $questionCount; $i++)
                                <div class="question p-6 bg-gray-50 rounded-[14px] border border-gray-300">
                                    <label for="question_{{ $i }}" class="block text-base text-black/80 mb-2">{{ __('Вопрос') }} {{ $i + 1 }}</label>
                                    <input type="text" name="questions[{{ $i }}]" id="question_{{ $i }}" value="{{ $questions[$i] ?? '' }}" class="w-full h-11 px-3 border-2 border-black rounded-[16px] focus:outline-none focus:ring-0 @error('questions.'.$i) border-red-500 @enderror" required>
                                    @error('questions.'.$i)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror

                                    <label for="answer_{{ $i }}" class="block text-base text-black/80 mt-4 mb-2">{{ __('Правильный ответ') }}</label>
                                    <input type="text" name="answers[{{ $i }}]" id="answer_{{ $i }}" value="{{ $answers[$i] ?? '' }}" class="w-full h-11 px-3 border-2 border-black rounded-[16px] focus:outline-none focus:ring-0 @error('answers.'.$i) border-red-500 @enderror" required>
                                    @error('answers.'.$i)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endfor
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                            <button type="button" id="add-question-button" class="h-10 px-4 rounded-[12px] border-2 border-black bg-white hover:bg-black hover:text-white text-sm">{{ __('Добавить вопрос') }}</button>
                            <button type="submit" class="h-10 px-4 rounded-[12px] border-2 border-black bg-black text-white text-sm hover:opacity-90">{{ __('Сохранить упражнение') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite('resources/js/add-question.js')
@endsection
