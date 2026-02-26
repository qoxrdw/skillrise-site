@extends('layouts.app')

@section('content')
    <div class="fixed inset-0 bg-[#F0F0F0] z-50 flex flex-col font-sans overflow-hidden">

        {{-- Header --}}
        <div class="h-[70px] bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0 z-30 shadow-sm">
            <div class="flex items-center gap-6">
                <a href="{{ route('tracks.show', $track) }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-500">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                </a>
                <div class="h-6 w-[1px] bg-gray-200"></div>
                <h1 class="text-[14px] font-bold uppercase tracking-widest text-black/40">{{ $track->name }} / {{ __('Новая заметка') }}</h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('tracks.show', $track) }}" class="text-sm font-medium text-gray-400 hover:text-black transition-colors">
                    {{ __('Отмена') }}
                </a>
                <button type="submit" form="note-form" class="bg-black text-white px-8 py-2.5 rounded-full text-sm font-medium hover:bg-gray-800 transition-all shadow-md">
                    {{ __('Сохранить') }}
                </button>
            </div>
        </div>

        {{-- Scrollable Area --}}
        <div class="flex-1 overflow-y-auto pt-10 pb-20">
            {{-- Увеличено до max-w-7xl (примерно 1280px - 1400px), что в ~1.5 раза шире предыдущего --}}
            <main class="max-w-7xl mx-auto px-6">
                <form id="note-form" method="POST" action="{{ route('notes.store', $track) }}">
                    @csrf

                    <div class="mb-6">
                        {{-- Уменьшен размер шрифта до text-4xl (как было раньше) --}}
                        <input type="text" name="title" placeholder="Заголовок заметки" autofocus
                               class="w-full text-3xl md:text-4xl font-bold border-none p-0 focus:ring-0 placeholder:text-gray-300 text-gray-900 bg-transparent tracking-tight">
                    </div>

                    {{-- Широкий контейнер-лист --}}
                    <div class="bg-white shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] rounded-lg border border-gray-200 min-h-[80vh] flex flex-col overflow-hidden">
                        <div id="editor" class="flex-1"></div>
                    </div>

                    <input type="hidden" name="content" id="content">
                </form>
            </main>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @vite('resources/js/notes-create.js')

    <style>
        /* Убираем дефолтные рамки */
        .ql-toolbar.ql-snow, .ql-container.ql-snow { border: none !important; }

        .ql-toolbar.ql-snow {
            position: sticky; top: 0; z-index: 40; background: white;
            border-bottom: 1px solid #f0f0f0 !important; padding: 15px 50px !important;
        }

        /* Настройка области текста */
        .ql-editor {
            padding: 50px 60px !important; /* Наш основной отступ */
            font-size: 1.15rem !important;
            line-height: 1.75 !important;
            min-height: 70vh;
        }

        /* !!! ИСПРАВЛЕНИЕ ПЛЕЙСХОЛДЕРА !!! */
        .ql-editor.ql-blank::before {
            left: 60px !important;      /* Должно быть равно padding-left в .ql-editor */
            right: 60px !important;     /* Для корректного переноса, если плейсхолдер длинный */
            top: 50px !important;       /* Должно быть равно padding-top в .ql-editor */
            font-style: normal;         /* Убираем курсив, чтобы выглядело современнее */
            color: #d1d5db;             /* Приятный светло-серый цвет */
        }

        /* Шрифты */
        .ql-font-roboto { font-family: 'Roboto', sans-serif; }
        .ql-font-montserrat { font-family: 'Montserrat', sans-serif; }
        .ql-font-playfair { font-family: 'Playfair Display', serif; }
    </style>
@endsection
