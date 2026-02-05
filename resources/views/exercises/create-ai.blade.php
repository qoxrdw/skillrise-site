@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-white px-8 pt-8 pb-20 font-sans">
        <div class="max-w-[1000px]">

            {{-- Навигация --}}
            <div class="mb-8">
                <a href="{{ route('tracks.show', $track) }}" class="inline-flex items-center gap-2 text-[18px] text-black/50 hover:text-black transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    {{ __('Назад к треку') }}
                </a>
            </div>

            {{-- Заголовок страницы (Стиль как синий бокс, но с AI акцентом) --}}
            <div class="mb-12 bg-black text-white border border-black rounded-[30px] p-10 relative overflow-hidden">
                {{-- Декоративный эффект нейросети на фоне --}}
                <div class="absolute top-0 right-0 opacity-20 pointer-events-none">
                    <svg width="400" height="400" viewBox="0 0 400 400" fill="none">
                        <circle cx="400" cy="0" r="300" stroke="white" stroke-width="1" stroke-dasharray="10 10"/>
                        <circle cx="400" cy="0" r="200" stroke="white" stroke-width="1" stroke-dasharray="5 5"/>
                    </svg>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-3 py-1 border border-white/30 rounded-full text-[12px] uppercase tracking-[0.2em]">AI Generation</span>
                        <span class="text-white/40 text-[12px] uppercase tracking-[0.2em]">Pro Feature</span>
                    </div>
                    <h1 class="text-[48px] font-normal leading-tight mb-4">
                        Создание упражнение <br> с помощью искуственного интеллекта
                    </h1>
                    <p class="text-[18px] text-white/60 max-w-[600px] font-light italic">
                        Выберите одну из ваших заметок. AI проанализирует контекст и составит проверочные вопросы для закрепления материала.
                    </p>
                </div>
            </div>

            {{-- Ошибки --}}
            @if ($errors->any())
                <div class="mb-8 p-6 rounded-[25px] border border-red-500 bg-red-50 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Форма выбора заметки --}}
            <form method="POST" action="{{ route('exercises.generate-ai', $track) }}" id="ai-form">
                @csrf

                <div class="mb-10">
                    <h2 class="text-[24px] font-normal mb-8">{{ __('Выберите источник:') }}</h2>

                    @if($notes->isEmpty())
                        <div class="p-12 text-center rounded-[30px] border border-black border-dashed">
                            <p class="text-[18px] text-black/40 mb-6 italic">{{ __('В этом треке пока нет заметок для анализа.') }}</p>
                            <a href="{{ route('notes.create', $track) }}" class="h-[55px] px-8 border border-black rounded-full inline-flex items-center hover:bg-black hover:text-white transition-all">
                                {{ __('Создать первую заметку') }}
                            </a>
                        </div>
                    @else
                        <div class="grid gap-4">
                            @foreach($notes as $note)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="note_id" value="{{ $note->id }}" class="peer hidden" required>

                                    <div class="flex items-center justify-between p-6 border border-black rounded-[25px] bg-white transition-all
                                                peer-checked:bg-[#F0F7FF] peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500
                                                group-hover:bg-gray-50">

                                        <div class="flex items-center gap-6">
                                            {{-- Кастомный индикатор радиокнопки --}}
                                            <div>
                                                <div class="text-[20px] text-black mb-1">
                                                    @php
                                                        $icons = ['text' => '📝', 'voice' => '🎙️', 'handwriting' => '🎨'];
                                                        echo ($icons[$note->type] ?? '📄') . ' ' . ($note->getFirstLine() ?: 'Без названия');
                                                    @endphp
                                                </div>
                                                <div class="text-[14px] text-black/40 uppercase tracking-widest">
                                                    {{ $note->type }} • {{ $note->created_at->isoFormat('LL') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Метка типа заметки --}}
                                        <div class="hidden sm:block text-[12px] font-medium text-black/30 border border-black/10 rounded-lg px-3 py-1 group-hover:border-black/30 transition-colors">
                                            SELECT SOURCE
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Кнопки действий --}}
                <div class="flex items-center justify-end gap-4 border-t border-black/5 pt-10">
                    <a href="{{ route('exercises.index', $track) }}"
                       class="h-[65px] px-10 rounded-full text-[18px] text-black/50 hover:text-black hover:bg-gray-50 flex items-center justify-center transition-all">
                        {{ __('Отмена') }}
                    </a>

                    <button type="submit" id="submit-btn"
                            class="h-[65px] px-12 bg-black text-white rounded-full text-[20px] flex items-center justify-center gap-3 hover:bg-black/90 transition-all shadow-xl group disabled:opacity-50">
                        <span id="btn-text" class="flex items-center gap-3">
                            {{ __('Сгенерировать') }}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>

                        {{-- Лоадер (скрыт по умолчанию) --}}
                        <div id="btn-loading" class="hidden items-center gap-3">
                            <svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ __('Нейросеть думает...') }}</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('ai-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');

            if (form) {
                form.addEventListener('submit', () => {
                    submitBtn.disabled = true;
                    btnText.classList.add('hidden');
                    btnLoading.classList.remove('hidden');
                    btnLoading.classList.add('flex');
                });
            }
        });
    </script>
@endsection
