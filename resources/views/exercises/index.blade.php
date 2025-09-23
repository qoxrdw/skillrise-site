@extends('layouts.app')

@section('content')
    {{-- Основной контейнер с белым фоном и вертикальным отступом --}}
    {{-- Применены стили из страниц заметок --}}
    <div class="py-12 bg-white">
        {{-- Контейнер для контента с автоматическими горизонтальными отступами для центрирования --}}
        {{-- Применены стили из страниц заметок для контроля ширины (~50%) --}}
        <div class="mx-auto sm:px-24 lg:px-80">

            {{-- Заголовок страницы --}}
            {{-- Применены стили заголовка из страниц заметок --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">{{ __('Упражнения для трека') }} "{{ $track->name }}"</h1>

            {{-- Сообщения сессии (успех/ошибки/результаты) --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('results'))
                <div class="mb-6 p-6 bg-blue-100 border border-blue-300 text-blue-700 rounded-lg shadow-sm">
                    <h2 class="text-xl font-semibold text-blue-800 mb-4">{{ __('Результаты прохождения') }}</h2>
                    <ul class="space-y-4">
                        @foreach (session('results') as $result)
                            <li class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-blue-900 mb-1"><strong>{{ __('Вопрос:') }}</strong> {{ $result['question'] }}</p>
                                <p class="text-blue-900 mb-1"><strong>{{ __('Ваш ответ:') }}</strong> {{ $result['user_answer'] ?? __('Нет ответа') }}</p>
                                <p class="text-blue-900"><strong>{{ __('Правильный ответ:') }}</strong> {{ $result['correct_answer'] }}</p>
                                <p class="mt-2"><strong>{{ __('Статус:') }}</strong>
                                    @if ($result['is_correct'])
                                        <span class="text-green-700 font-semibold">{{ __('Правильно') }}</span>
                                    @else
                                        <span class="text-red-700 font-semibold">{{ __('Неправильно') }}</span>
                                    @endif
                                </p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Список упражнений --}}
            @if($exercises->isEmpty())
                <div class="p-6 bg-white rounded-xl shadow-lg text-gray-600">
                    <p>{{ __('Упражнений пока нет. Создайте первое!') }}</p>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach($exercises as $exercise)
                        {{-- Стили карточки упражнения, похожие на карточки в dashboard --}}
                        <li class="p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div class="mb-4 sm:mb-0">
                                {{-- Ссылка на прохождение упражнения --}}
                                <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="text-xl font-semibold text-indigo-600 hover:text-indigo-800 transition-colors duration-300">{{ $exercise->title }}</a>
                            </div>
                            <div class="flex items-center space-x-3">
                                {{-- Кнопка "Пройти" --}}
                                <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    {{ __('Пройти') }}
                                </a>
                                {{-- Кнопка удаления --}}
                                <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Вы уверены, что хотите удалить это упражнение?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors duration-300 p-1 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1" title="{{ __('Удалить упражнение') }}">
                                        {{-- Иконка корзины/удаления --}}
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Кнопка "Создать новое упражнение" --}}
            {{-- Удалена кнопка "Создать новое упражнение", так как она теперь находится на странице трека --}}
            {{-- <div class="mt-8 text-center sm:text-left">
                <a href="{{ route('exercises.create') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Создать новое упражнение') }}
                </a>
            </div> --}}
        </div>
    </div>
@endsection
