@extends('layouts.app')

@section('content')
    <div class="py-6 md:py-10">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Back link --}}
            <div class="mb-4 md:mb-6">
                <a href="{{ route('exercises.index', $track) }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1
                             0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1
                             0 011.414 0z"/>
                    </svg>
                    {{ __('Назад к упражнениям трека') }}
                </a>
            </div>


            {{-- HERO HEADER (в стиле index.blade / tracks/show) --}}
            <div class="mb-8 md:mb-10">
                <div class="relative rounded-[24px] border border-gray-200 bg-gradient-to-br
                        from-purple-50 via-blue-50 to-cyan-50 overflow-hidden shadow-lg">

                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br
                            from-purple-200/40 to-transparent rounded-full blur-3xl"></div>

                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr
                            from-blue-200/40 to-transparent rounded-full blur-2xl"></div>

                    <div class="relative px-6 md:px-10 py-8 md:py-10">
                        <div class="flex items-center gap-4">

                            {{-- AI ICON --}}
                            <div class="flex-shrink-0 w-14 h-14 rounded-2xl
                                    bg-gradient-to-br from-indigo-500 to-blue-600
                                    flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 7H7v6h6V7z"/>
                                    <path fill-rule="evenodd"
                                          d="M7 2a1 1 0 112 0v1h2V2a1 1 0 112 0v1h2a2 2
                                         0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1
                                         0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1
                                         0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2
                                         0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1
                                         0 010-2h1V5a2 2 0 012-2h2V2z
                                         M5 5h10v10H5V5z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>

                            <h1 class="text-[32px] md:text-[40px] font-bold leading-tight
                                   bg-gradient-to-r from-gray-900 via-purple-900 to-blue-900
                                   bg-clip-text text-transparent">
                                {{ __('Создать упражнение с AI') }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ERRORS --}}
            @if ($errors->any())
                <div class="mb-6 p-5 rounded-2xl border border-red-200 bg-red-50 shadow-sm">
                    <div class="font-semibold text-red-700 mb-2">{{ __('Обнаружены ошибки:') }}</div>
                    <ul class="list-disc pl-5 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- INFO BOX (обновлённый стиль) --}}
            <div class="mb-8 p-6 rounded-2xl border border-blue-100 shadow-sm
                    bg-gradient-to-br from-blue-50 to-purple-50">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                    💡 {{ __('Как это работает?') }}
                </h2>
                <p class="text-gray-700 mb-2">
                    {{ __('AI проанализирует выбранную заметку и создаст упражнение с вопросами и ответами.') }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ __('Выберите заметку ниже — и упражнение будет сгенерировано за несколько секунд!') }}
                </p>
            </div>


            {{-- FORM --}}
            <div class="rounded-[24px] border border-gray-200 bg-white shadow-md">
                <form method="POST" action="{{ route('exercises.generate-ai', $track) }}" id="ai-form">
                    @csrf

                    <div class="p-6 md:p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">
                            {{ __('Выберите заметку для создания упражнения') }}
                        </h2>

                        {{-- CASE: no notes --}}
                        @if($notes->isEmpty())
                            <div class="p-8 text-center rounded-2xl border border-dashed border-gray-300 bg-gray-50">
                                <p class="text-gray-700 mb-4">{{ __('У вас пока нет заметок в этом треке.') }}</p>
                                <a href="{{ route('notes.create', $track) }}"
                                   class="inline-flex items-center h-10 px-4 rounded-xl
                                      bg-blue-100 text-blue-700 border border-blue-200
                                      hover:bg-blue-200 transition font-medium">
                                    {{ __('Создать заметку') }}
                                </a>
                            </div>

                        @else

                            {{-- RADIO-CARD LIST (в стиле карточек A) --}}
                            <div class="space-y-3">
                                @foreach($notes as $note)
                                    <label class="block rounded-2xl border border-gray-200 bg-white p-5
                                              hover:border-blue-300 hover:shadow-lg cursor-pointer transition">

                                        <div class="flex items-start gap-4">
                                            <input type="radio" name="note_id" value="{{ $note->id }}"
                                                   class="mt-1 h-5 w-5 text-blue-600 focus:ring-blue-500" required>

                                            <div class="flex-1">
                                                <div class="text-[18px] text-gray-900 mb-1 font-medium">
                                                    @if($note->type === 'handwriting')
                                                        ✍️ {{ $note->getFirstLine() ?: __('Рукописная заметка') }}
                                                    @elseif($note->type === 'voice')
                                                        🎤 {{ __('Голосовая заметка') }}
                                                    @else
                                                        📝 {{ $note->getFirstLine() ?: __('(Без названия)') }}
                                                    @endif
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    {{ $note->created_at->isoFormat('LL') }}
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                @endforeach
                            </div>

                            {{-- BUTTONS --}}
                            <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                                <a href="{{ route('exercises.index', $track) }}"
                                   class="h-10 px-4 rounded-xl border border-gray-300 bg-white
                                      text-gray-700 hover:bg-gray-900 hover:text-white
                                      text-sm font-medium flex items-center justify-center">
                                    {{ __('Отмена') }}
                                </a>

                                <button type="submit" id="submit-btn"
                                        class="h-10 px-4 rounded-xl bg-gradient-to-r
                                           from-purple-600 via-blue-600 to-cyan-600
                                           text-white border border-transparent
                                           text-sm font-medium flex items-center
                                           justify-center shadow-md hover:shadow-lg transition">
                                    <span id="btn-text">{{ __('Сгенерировать упражнение') }}</span>

                                    {{-- spinner --}}
                                    <span id="btn-loading" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373
                                                 0 0 5.373 0 12h4zm2
                                                 5.291A7.962 7.962 0 014 12H0c0
                                                 3.042 1.135 5.824 3
                                                 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                </button>
                            </div>

                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script>
        const form = document.getElementById('ai-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');

        if (form) {
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
            });
        }
    </script>
@endsection
