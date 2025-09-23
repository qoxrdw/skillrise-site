@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto">

            {{-- Breadcrumbs or Back Link --}}
            <div class="mb-6">
                <a href="{{ route('tracks.index') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors duration-300">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    {{ __('К списку треков') }}
                </a>
            </div>

            {{-- Track Title with rename button and toggleable form --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h1 class="text-3xl font-extrabold">{{ $track->name }}</h1>
                    <div class="flex items-center gap-2">
                        <button id="rename-toggle" type="button" class="h-9 px-4 rounded-full border border-gray-900">
                            Переименовать
                        </button>
                        <form id="rename-form" method="POST" action="{{ route('tracks.update', $track) }}" class="flex items-center gap-2 hidden">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="name" value="{{ old('name', $track->name) }}" class="h-9 px-3 rounded-lg border border-gray-300 w-64" required>
                            <button type="submit" class="h-9 px-4 rounded-full border border-gray-900">Сохранить</button>
                            <button id="rename-cancel" type="button" class="h-9 px-4 rounded-full border border-gray-300 hidden">
                                Отмена
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            {{-- Session Messages --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Action Button: Create New Note --}}
            <div class="mb-8 flex justify-end gap-3">
                @if (!$track->is_public)
                    <form action="{{ route('tracks.share', $track) }}" method="POST" class="inline-block"
                          onsubmit="return confirm('Вы уверены, что хотите поделиться этим треком? Он станет виден всем пользователям.');">
                        @csrf
                        <button type="submit" class="h-9 px-4 rounded-full border border-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186A4.486 4.486 0 0112 20.25a4.486 4.486 0 01-4.783-9.343m0 0A10.025 10.025 0 019.337 2.685M12 12h.008v.008H12zm0 0v9.75m-4.783-2.814c-1.451.133-2.883.36-4.28.647M12 4.75h.008v.008H12zm0 0H9.25m8.517 6.602c1.451.133 2.883.36 4.28.647m0 0a8.91 8.91 0 00-5.613-8.63c-.944-.943-2.11-1.688-3.414-2.088m0 0Kh.008v.008H12z" />
                            </svg>
                            {{ __('Поделиться треком') }}
                        </button>
                    </form>
                @else
                    <form action="{{ route('tracks.unshare', $track) }}" method="POST" class="inline-block"
                          onsubmit="return confirm('Вы уверены, что хотите снять этот трек с публикации? Он больше не будет виден другим пользователям.');">
                        @csrf
                        <button type="submit" class="h-9 px-4 rounded-full border border-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            {{ __('Снять с публикации') }}
                        </button>
                    </form>
                    <span class="inline-flex items-center h-9 px-4 rounded-full border border-gray-300 text-gray-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18.75c0 .621-.504 1.125-1.125 1.125H12a2.25 2.25 0 01-2.25-2.25V12a2.25 2.25 0 00-2.25-2.25H6" />
                        </svg>
                        {{ __('Трек опубликован') }}
                    </span>
                @endif
                <a href="{{ route('notes.create', $track) }}"
                   class="h-9 px-4 rounded-full border border-gray-900 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Создать новую заметку') }}
                </a>
            </div>

            {{-- Notes Section Title --}}
            <h2 class="text-2xl font-extrabold mb-6">{{ __('Заметки в треке') }}</h2>

            {{-- List of Notes --}}
            @if($notes->isEmpty())
                <div class="text-center p-10 bg-white rounded-xl shadow-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <h3 class="mt-2 text-xl font-medium text-gray-900">{{ __('Заметок пока нет') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Добавьте свою первую заметку в этот трек.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notes as $note)
                        <div class="card-minimal overflow-hidden">
                            <div>
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3">
                                    <div class="flex items-center mb-2 sm:mb-0">
                                        {{-- Note Title (First line) --}}
                                        <h3 class="text-lg font-semibold">
                                            <a href="{{ route('notes.edit', [$track, $note]) }}" class="hover:underline focus:outline-none focus:ring-2 focus:ring-purple-500 rounded">
                                                {{ $note->getFirstLine() ?: __('(Без названия)') }}
                                            </a>
                                        </h3>
                                    </div>
                                    {{-- Action Buttons: Edit and Delete --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ route('notes.edit', [$track, $note]) }}"
                                           class="h-8 px-3 rounded-full border border-gray-900 text-sm inline-flex items-center"
                                           title="{{ __('Редактировать заметку') }}">
                                            <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ __('Редакт.') }}
                                        </a>
                                        <form action="{{ route('notes.destroy', [$track, $note]) }}" method="POST" class="inline-block delete-form"
                                              onsubmit="return confirm('Вы уверены, что хотите удалить эту заметку? Это действие необратимо.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 px-3 rounded-full border border-gray-300 text-sm inline-flex items-center"
                                                    title="{{ __('Удалить заметку') }}">
                                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ __('Удалить') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                {{-- Optional: Display a snippet of the note content or creation/update dates --}}
                                <p class="text-gray-600 text-sm mt-2">
                                    {{-- Example: Displaying a snippet. You'll need a method in your Note model like getSnippet() --}}
                                    {{-- {{ $note->getSnippet(150) }} --}}
                                    {{-- Or just creation date --}}
                                    <span class="text-xs text-gray-400">{{ __('Создана:') }} {{ $note->created_at->isoFormat('LL LTS') }}</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Exercises Section Title --}}
            <h2 class="text-2xl font-extrabold mb-6 mt-10">{{ __('Упражнения в треке') }}</h2>

            {{-- Action Button: Create New Exercise --}}
            <div class="mb-8">
                <a href="{{ route('exercises.create', $track) }}"
                   class="h-9 px-4 rounded-full border border-gray-900 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Создать новое упражнение') }}
                </a>
            </div>

            {{-- List of Exercises --}}
            @if($track->exercises->isEmpty())
                <div class="text-center p-10 bg-white rounded-xl shadow-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-xl font-medium text-gray-900">{{ __('Упражнений пока нет') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Добавьте свое первое упражнение в этот трек.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($track->exercises as $exercise)
                        <div class="card-minimal overflow-hidden">
                            <div>
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3">
                                    <div class="flex items-center mb-2 sm:mb-0">
                                        <h3 class="text-lg font-semibold">
                                            <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                                                {{ $exercise->title }}
                                            </a>
                                        </h3>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ route('exercises.take', [$track, $exercise]) }}" class="h-8 px-3 rounded-full border border-gray-900 text-sm inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                            {{ __('Начать') }}
                                        </a>
                                        <form action="{{ route('exercises.destroy', [$track, $exercise]) }}" method="POST" class="inline-block delete-form"
                                              onsubmit="return confirm('Вы уверены, что хотите удалить это упражнение? Это действие необратимо.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 px-3 rounded-full border border-gray-300 text-sm inline-flex items-center"
                                                    title="{{ __('Удалить упражнение') }}">
                                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ __('Удалить') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    {{-- If you have a global delete confirmation script, it might still be useful.
        Otherwise, the onsubmit confirm in the form is a simple alternative.
        @vite('resources/js/delete-confirm.js')
    --}}
@endsection
