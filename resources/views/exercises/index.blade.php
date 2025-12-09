@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Hero Header with Gradient --}}
            <div class="mb-8 md:mb-10">
                <div class="relative rounded-[24px] border-2 border-black bg-gradient-to-br from-purple-50 via-blue-50 to-cyan-50 overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                    {{-- Декоративные элементы --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-200/30 to-transparent rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-200/30 to-transparent rounded-full blur-2xl"></div>

                    <div class="relative px-6 md:px-10 py-8 md:py-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                {{-- Иконка с анимацией --}}
                                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                    </svg>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">{{ __('Трек') }}</span>
                                    </div>
                                    <h1 class="text-[32px] md:text-[40px] leading-tight font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-blue-900 bg-clip-text text-transparent">
                                        {{ $track->name }}
                                    </h1>
                                </div>
                            </div>

                            {{-- Статистика --}}
                            <div class="flex items-center gap-3">
                                <div class="px-4 h-12 rounded-2xl border-2 border-purple-200 bg-white/80 backdrop-blur-sm flex items-center gap-2 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ $exercises->count() }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Упражнений') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons with Enhanced Design --}}
            <div class="mb-8">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('exercises.create', $track) }}" class="group relative h-12 px-6 rounded-2xl border-2 border-gray-300 bg-white text-gray-700 hover:border-gray-900 hover:text-gray-900 text-sm font-medium flex items-center gap-2 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                        <span class="absolute inset-0 bg-gradient-to-r from-gray-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative">{{ __('Создать обычное упражнение') }}</span>
                    </a>

                    <a href="{{ route('exercises.create-ai', $track) }}" class="group relative h-12 px-6 rounded-2xl border-2 border-transparent bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 text-white text-sm font-medium flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                        {{-- Анимированный фон --}}
                        <span class="absolute inset-0 bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>

                        {{-- Блик --}}
                        <span class="absolute inset-0 translate-x-[-100%] group-hover:translate-x-[100%] bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700"></span>
                        <span class="relative font-semibold">{{ __('Создать с AI PRO') }}</span>
                        <span class="relative ml-1 px-2 py-0.5 rounded-full bg-white/20 text-xs font-bold backdrop-blur-sm">NEW</span>
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-6 p-4 border-2 border-green-200 rounded-2xl bg-gradient-to-r from-green-50 to-emerald-50 text-green-800 flex items-center gap-3 shadow-sm animate-fade-in">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Results Section --}}
            @if (session('results'))
                <div class="mb-8 p-6 md:p-8 rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 via-white to-purple-50 shadow-lg">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ __('Результаты прохождения') }}
                        </h2>
                    </div>

                    <div class="space-y-3">
                        @foreach (session('results') as $index => $result)
                            <div class="group p-5 rounded-xl border-2 {{ $result['is_correct'] ? 'border-green-200 bg-gradient-to-br from-green-50 to-emerald-50' : 'border-red-200 bg-gradient-to-br from-red-50 to-orange-50' }} hover:shadow-md transition-all duration-300" style="animation: slideIn 0.3s ease-out {{ $index * 0.1 }}s backwards;">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg {{ $result['is_correct'] ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="flex-1 text-gray-900 font-medium">{{ $result['question'] }}</p>
                                </div>

                                <div class="ml-11 space-y-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">{{ __('Ваш ответ:') }}</span>
                                        <span class="px-3 py-1 rounded-lg {{ $result['is_correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $result['user_answer'] ?? __('Нет ответа') }}
                                        </span>
                                    </div>

                                    @if (!$result['is_correct'])
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-700">{{ __('Правильный ответ:') }}</span>
                                            <span class="px-3 py-1 rounded-lg bg-green-100 text-green-800 font-medium">
                                                {{ $result['correct_answer'] }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2 pt-1">
                                        @if ($result['is_correct'])
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-green-700 font-bold">{{ __('Правильно') }}</span>
                                        @else
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-red-700 font-bold">{{ __('Неправильно') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Exercises List --}}
            @if($exercises->isEmpty())
                <div class="relative p-12 text-center rounded-2xl border-2 border-dashed border-gray-300 bg-gradient-to-br from-gray-50 to-slate-50 overflow-hidden">
                    {{-- Декоративные круги --}}
                    <div class="absolute top-0 left-1/4 w-32 h-32 bg-purple-200/20 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 right-1/4 w-40 h-40 bg-blue-200/20 rounded-full blur-3xl"></div>

                    <div class="relative">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-xl font-medium text-gray-600 mb-2">{{ __('Упражнений пока нет') }}</p>
                        <p class="text-gray-500">{{ __('Создайте первое упражнение, чтобы начать обучение') }}</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($exercises as $index => $exercise)
                        <div class="group rounded-2xl border-2 border-gray-200 bg-white hover:border-purple-300 hover:shadow-lg p-6 transition-all duration-300" style="animation: slideIn 0.3s ease-out {{ $index * 0.05 }}s backwards;">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="flex-1 flex items-center gap-4 min-w-0">
                                    {{-- Номер упражнения --}}
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center text-white font-bold shadow-md group-hover:scale-110 transition-transform duration-300">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 group-hover:text-purple-600 transition-colors truncate">
                                            {{ $exercise->title }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-sm text-gray-500">
                                                {{ count($exercise->content) }} {{ __('вопросов') }}
                                            </span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-sm text-gray-500">
                                                {{ $exercise->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </a>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="h-10 px-5 rounded-xl border-2 border-purple-200 bg-gradient-to-r from-purple-50 to-blue-50 text-purple-700 hover:border-purple-400 hover:from-purple-100 hover:to-blue-100 text-sm font-medium flex items-center gap-2 transition-all duration-300 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ __('Пройти') }}</span>
                                    </a>

                                    <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST" onsubmit="return confirm('{{ __('Вы уверены, что хотите удалить это упражнение?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-10 w-10 rounded-xl border-2 border-gray-200 bg-white hover:border-red-400 hover:bg-red-50 text-gray-600 hover:text-red-600 transition-all duration-300 flex items-center justify-center shadow-sm hover:shadow-md group/delete" title="{{ __('Удалить упражнение') }}">
                                            <svg class="w-5 h-5 group-hover/delete:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endsection
