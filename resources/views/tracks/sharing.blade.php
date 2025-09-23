@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6 text-center">Общедоступные треки</h1>
                <p class="text-lg text-gray-700 mb-6 text-center">Здесь вы можете найти образовательные треки, которыми поделились пользователи.</p>

                <div class="flex justify-center mb-10">
                    <form method="GET" action="{{ route('tracks.sharing') }}" class="w-full max-w-xl relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск общедоступных треков" class="w-full h-11 pl-11 pr-4 rounded-full border border-gray-300 outline-none focus:ring-0">
                        <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                </div>

                @if ($tracks->isEmpty())
                    <div class="bg-gray-50 p-4 rounded-md text-center">
                        <p class="text-gray-600">Пока нет доступных треков для шеринга.</p>
                        <p class="text-gray-500 text-sm mt-2">Возможно, вы станете первым, кто поделится своим треком!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($tracks as $track)
                            <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                                <h3 class="text-xl font-semibold mb-2 text-gray-800">{{ $track->name }}</h3>
                                <p class="text-gray-600 text-sm mb-4">Автор: <span class="font-medium">{{ $track->user->name ?? 'Неизвестный' }}</span></p>
                                {{-- Дополнительная информация о треке, если есть --}}
                                <div class="mt-4">
                                    <form action="{{ route('tracks.clone', $track) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            Добавить трек
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
