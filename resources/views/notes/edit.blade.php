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
                <h1 class="text-[14px] font-bold uppercase tracking-widest text-black/40">{{ $track->name }} / {{ __('Редактирование') }}</h1>
            </div>

            <div class="flex items-center gap-6">
                {{-- Счётчик страниц --}}
                <span id="page-counter" class="text-[13px] text-gray-400 font-medium tabular-nums">1 / 1</span>

                <div class="h-5 w-[1px] bg-gray-200"></div>

                {{-- Кнопка экспорта в PDF --}}
                <button id="export-pdf-btn" type="button"
                        class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    <span id="export-pdf-label">PDF</span>
                </button>

                <div class="h-5 w-[1px] bg-gray-200"></div>

                <a href="{{ route('tracks.show', $track) }}" class="text-sm font-medium text-gray-400 hover:text-black transition-colors">
                    {{ __('Отмена') }}
                </a>
                <button type="submit" form="note-form" class="bg-black text-white px-8 py-2.5 rounded-full text-sm font-medium hover:bg-gray-800 transition-all shadow-md">
                    {{ __('Обновить') }}
                </button>
            </div>
        </div>

        {{-- Body: sidebar + editor --}}
        <div class="flex flex-1 overflow-hidden">

            {{-- Боковая панель миниатюр --}}
            <aside class="w-[180px] bg-[#EBEBEB] border-r border-gray-200 flex flex-col overflow-hidden shrink-0">
                <div class="px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-200">
                    Страницы
                </div>

                <div id="page-thumbnails" class="flex-1 overflow-y-auto p-3 flex flex-col gap-3">
                    {{-- Рендерится через JS --}}
                </div>

                <div class="p-3 border-t border-gray-200">
                    <button id="add-page-btn" type="button"
                            class="w-full py-2.5 rounded-lg border-2 border-dashed border-gray-300 text-gray-400 text-[13px] font-medium hover:border-black hover:text-black transition-all flex items-center justify-center gap-1.5">
                        <span class="text-lg leading-none">+</span>
                        Страница
                    </button>
                </div>
            </aside>

            {{-- Область редактора --}}
            <div class="flex-1 overflow-y-auto">
                <main class="max-w-5xl mx-auto px-6 pt-10 pb-20">
                    <form id="note-form" method="POST" action="{{ route('notes.update', [$track, $note]) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <input type="text" name="title" value="{{ old('title', $note->title) }}" placeholder="Без названия"
                                   class="w-full text-3xl md:text-4xl font-bold border-none p-0 focus:ring-0 placeholder:text-gray-300 text-gray-900 bg-transparent tracking-tight">
                        </div>

                        <div class="bg-white shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] rounded-lg border border-gray-200 min-h-[80vh] flex flex-col overflow-hidden">
                            {{-- JS читает innerHTML этого div для определения формата (JSON или HTML) --}}
                            <div id="editor" class="flex-1">{!! old('content', $note->content) !!}</div>
                        </div>

                        <input type="hidden" name="content" id="content">
                    </form>
                </main>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    @vite('resources/js/notes-edit.js')

    <style>
        .ql-toolbar.ql-snow, .ql-container.ql-snow { border: none !important; }

        .ql-toolbar.ql-snow {
            position: sticky; top: 0; z-index: 40; background: white;
            border-bottom: 1px solid #f0f0f0 !important; padding: 15px 30px !important;
        }

        .ql-editor {
            padding: 40px 50px !important;
            font-size: 1.15rem !important;
            line-height: 1.75 !important;
            min-height: 70vh;
        }

        .ql-editor.ql-blank::before {
            left: 50px !important;
            right: 50px !important;
            top: 40px !important;
            font-style: normal;
            color: #d1d5db;
        }

        .ql-font-roboto { font-family: 'Roboto', sans-serif; }
        .ql-font-montserrat { font-family: 'Montserrat', sans-serif; }
        .ql-font-playfair { font-family: 'Playfair Display', serif; }
    </style>
@endsection
