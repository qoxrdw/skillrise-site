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
                    {{ __('Назад к треку') }} «{{ $track->name }}»
                </a>
            </div>

            {{-- Заголовок страницы (Стиль как синий бокс, но серый) --}}
            <div class="mb-12 bg-[#F9F9F9] border border-black rounded-[30px] p-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <div class="text-[16px] font-medium text-black/40 uppercase tracking-widest mb-2">{{ __('Упражнения трека') }}</div>
                        <h1 class="text-[48px] font-normal text-black leading-tight">{{ $track->name }}</h1>
                    </div>

                    <div class="flex items-center gap-3 bg-white border border-black rounded-[20px] px-6 py-3">
                        <span class="text-[24px] font-medium">{{ $exercises->count() }}</span>
                        <span class="text-[16px] text-black/50 uppercase tracking-wide">{{ __('Всего') }}</span>
                    </div>
                </div>
            </div>

            {{-- Блок действий (Кнопки создания) --}}
            <div class="flex flex-wrap gap-4 mb-12">
                <a href="{{ route('exercises.create', $track) }}"
                   class="h-[60px] px-8 border border-black rounded-full text-[18px] hover:bg-black hover:text-white transition-all flex items-center gap-2">
                    <span class="text-2xl">+</span> {{ __('Обычное упражнение') }}
                </a>

                <a href="{{ route('exercises.create-ai', $track) }}"
                   class="h-[60px] px-8 bg-black text-white border border-black rounded-full text-[18px] hover:bg-black/90 transition-all flex items-center gap-3">
                    <span class="text-xl">✨</span> {{ __('Создать с AI PRO') }}
                </a>
            </div>

            {{-- Результаты последнего прохождения (если есть в сессии) --}}
            @if (session('results'))
                <div class="mb-12 p-8 border border-black rounded-[30px] bg-white">
                    <h2 class="text-[28px] font-normal mb-8 flex items-center gap-3">
                        <span class="text-3xl">📊</span> {{ __('Результаты прохождения') }}
                    </h2>

                    <div class="space-y-4">
                        @foreach (session('results') as $index => $result)
                            <div class="p-6 border border-black rounded-[20px] {{ $result['is_correct'] ? 'bg-green-50/50' : 'bg-red-50/50' }}">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 shrink-0 border border-black rounded-full flex items-center justify-center font-medium bg-white">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[20px] text-black mb-3">{{ $result['question'] }}</p>

                                        <div class="flex flex-wrap gap-4 text-[16px]">
                                            <div class="flex items-center gap-2">
                                                <span class="text-black/40">{{ __('Ваш ответ:') }}</span>
                                                <span class="font-medium {{ $result['is_correct'] ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ $result['user_answer'] ?? __('Нет ответа') }}
                                                </span>
                                            </div>

                                            @if (!$result['is_correct'])
                                                <div class="flex items-center gap-2">
                                                    <span class="text-black/40">{{ __('Правильный:') }}</span>
                                                    <span class="font-medium text-green-700">{{ $result['correct_answer'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Список упражнений --}}
            @if($exercises->isEmpty())
                <div class="h-[200px] border border-black border-dashed rounded-[30px] flex flex-col items-center justify-center text-black/30">
                    <span class="text-4xl mb-2">📁</span>
                    <p class="text-[20px] italic">{{ __('В этом треке пока нет упражнений') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($exercises as $index => $exercise)
                        <div class="group flex items-center gap-4">
                            {{-- Карточка упражнения --}}
                            @php
                                $exRoute = $exercise->type === 'task'
                                    ? route('exercises.take-task', [$track, $exercise])
                                    : route('exercises.take', [$track, $exercise]);
                                $exMeta = $exercise->type === 'task'
                                    ? '🧠 AI Задача'
                                    : count($exercise->content) . ' ' . __('вопросов');
                            @endphp
                            <a href="{{ $exRoute }}"
                               class="flex-1 h-[90px] border border-black rounded-[25px] flex items-center justify-between px-8 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-6">
                                    <span class="text-[20px] text-black/30 font-light w-6">0{{ $index + 1 }}</span>
                                    <div>
                                        <div class="text-[22px] text-black group-hover:underline">{{ $exercise->title }}</div>
                                        <div class="text-[14px] text-black/40 uppercase tracking-widest mt-1">
                                            {{ $exMeta }} • {{ $exercise->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-black/20 group-hover:text-black transition-colors">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>

                            {{-- Кнопка удаления --}}
                            <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST"
                                  onsubmit="return confirm('{{ __('Удалить это упражнение?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-[60px] h-[90px] border border-black rounded-[25px] flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-colors group">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 6L6 18M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
