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

            {{-- Заголовок упражнения --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">{{ $exercise->title }}</h1>

            {{-- Подзаголовок "Вопросы" --}}
            <h2 class="text-2xl font-semibold text-gray-700 mb-6">{{ __('Вопросы') }}</h2>

            {{-- Форма прохождения упражнения --}}
            <div class="bg-white">
                <form method="POST" action="{{ route('exercises.submit', [$track, $exercise]) }}">
                    @csrf
                    <div class="p-6 sm:p-8 space-y-6">
                        @foreach($exercise->content as $index => $item)
                            {{-- Блок вопроса и поля ввода ответа --}}
                            <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="block text-lg font-semibold text-gray-700 mb-2">{{ $item['question'] }}</label>
                                <input type="text" name="answers[{{ $index }}]"
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('answers.'.$index) border-red-500 @enderror" required>
                                @error('answers.'.$index)
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        {{-- Кнопка "Завершить упражнение" --}}
                        <div class="mt-8 text-center sm:text-left">
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('Завершить упражнение') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
