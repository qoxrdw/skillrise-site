@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-6xl mx-auto">

            {{-- Header --}}
            <div class="mb-6 md:mb-8">
                <div class="rounded-[20px] border-2 border-black bg-white">
                    <div class="px-6 md:px-10 py-6 md:py-7">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-black/50 -rotate-90" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            <h1 class="flex-1 text-[28px] md:text-[34px] leading-tight text-black/90 truncate">{{ __('Упражнения') }} — "{{ $track->name }}"</h1>
                            <span class="px-3 h-8 rounded-[12px] border-2 border-black bg-white text-sm">{{ __('Всего') }}: {{ $exercises->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash messages / results --}}
            @if (session('success'))
                <div class="mb-6 p-4 border-2 border-black rounded-[14px] bg-white text-black/80">{{ session('success') }}</div>
            @endif
            @if (session('results'))
                <div class="mb-6 p-6 rounded-[14px] border-2 border-black bg-white">
                    <h2 class="text-xl md:text-2xl text-black/90 mb-4">{{ __('Результаты прохождения') }}</h2>
                    <ul class="space-y-4">
                        @foreach (session('results') as $result)
                            <li class="p-4 rounded-[12px] border border-gray-300 bg-gray-50">
                                <p class="text-black/90 mb-1"><strong>{{ __('Вопрос:') }}</strong> {{ $result['question'] }}</p>
                                <p class="text-black/80 mb-1"><strong>{{ __('Ваш ответ:') }}</strong> {{ $result['user_answer'] ?? __('Нет ответа') }}</p>
                                <p class="text-black/80"><strong>{{ __('Правильный ответ:') }}</strong> {{ $result['correct_answer'] }}</p>
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

            {{-- List --}}
            @if($exercises->isEmpty())
                <div class="p-8 text-center rounded-[14px] border-2 border-dashed border-black/50 bg-white text-black/70">
                    {{ __('Упражнений пока нет. Создайте первое!') }}
                </div>
            @else
                <ul class="space-y-3">
                    @foreach($exercises as $exercise)
                        <li class="rounded-[14px] border-2 border-black bg-white p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="text-[18px] md:text-[20px] leading-6 text-black/90 hover:underline">{{ $exercise->title }}</a>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="h-9 px-3 rounded-[12px] border-2 border-black bg-white hover:bg-black hover:text-white text-sm">{{ __('Пройти') }}</a>
                                <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST" onsubmit="return confirm('{{ __('Вы уверены, что хотите удалить это упражнение?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 px-3 rounded-[12px] border-2 border-black bg-white hover:bg-red-600 hover:border-red-700 hover:text-white text-sm" title="{{ __('Удалить упражнение') }}">{{ __('Удалить') }}</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
