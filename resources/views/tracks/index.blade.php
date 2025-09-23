@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto">
            {{-- Заголовок страницы --}}
            <h1 class="text-3xl font-extrabold mb-6">Мои треки</h1>

            {{-- Сообщение об успехе/ошибке --}}
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

            {{-- Форма для создания нового трека --}}
            <form method="POST" action="{{ route('tracks.store') }}" class="mb-10">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="border border-gray-300 rounded-xl p-5">
                        <label for="name" class="block text-sm text-gray-600 mb-3">Создать трек</label>
                        <div class="flex gap-3">
                            <input id="name" name="name" placeholder="Название" class="flex-1 h-10 px-3 rounded-lg border border-gray-300" value="{{ old('name') }}" required>
                            <button type="submit" class="w-10 h-10 rounded-full border border-gray-900 flex items-center justify-center">+</button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Список треков --}}
            @if($tracks->isEmpty())
                <div class="text-center p-10 bg-white rounded-xl shadow-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-xl font-medium text-gray-900">{{ __('Треков пока нет') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Начните свое обучение, создав первый трек!') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tracks as $track)
                        <div class="border border-gray-300 rounded-xl p-5">
                            <div class="flex items-center mb-3">
                                <h2 class="text-lg font-semibold">
                                    <a href="{{ route('tracks.show', $track) }}" class="hover:underline focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded">
                                        {{ $track->name }}
                                    </a>
                                </h2>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <a href="{{ route('tracks.show', $track) }}" class="text-sm">Открыть трек →</a>
                                <form method="POST" action="{{ route('tracks.destroy', $track) }}" onsubmit="return confirm('Вы уверены, что хотите удалить трек «{{ $track->name }}» и все связанные с ним заметки и упражнения? Это действие необратимо.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">Удалить</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
