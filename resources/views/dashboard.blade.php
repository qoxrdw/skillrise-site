@extends('layouts.app')

@section('content')
    <div class="py-16">
        <div class="max-w-5xl mx-auto">
            @auth
                <h1 class="text-5xl font-extrabold leading-tight">Добро пожаловать,<br> {{ Auth::user()->name }}!</h1>
            @else
                <h1 class="text-5xl font-extrabold leading-tight">Добро пожаловать,<br> гость!</h1>
            @endauth

            <div class="mt-6 max-w-prose text-gray-600">
                <p>SkillRise поможет организовать ваше самообучение в одном месте.</p>
                <p class="mt-2">Обучение — это ваш путь к росту, и мы поможем вам пройти его уверенно.</p>
            </div>

           

            <h2 class="mt-14 text-3xl font-extrabold">Как пользоваться платформой?</h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card-minimal">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center">📁</span>
                        <h3 class="text-xl font-semibold">1. Треки</h3>
                    </div>
                    <p class="text-gray-600">Треки сделаны для объединения заметок по темам. Если вы изучаете несколько областей, создайте отдельный трек для каждой.</p>
                </div>
                <div class="card-minimal">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center">📝</span>
                        <h3 class="text-xl font-semibold">2. Заметки</h3>
                    </div>
                    <p class="text-gray-600">В каждом треке можно вести множество заметок, добавлять их, редактировать и удалять в любое время.</p>
                </div>
                <div class="card-minimal">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center">✏️</span>
                        <h3 class="text-xl font-semibold">3. Упражнения</h3>
                    </div>
                    <p class="text-gray-600">Создавайте свои упражнения и проходите их, чтобы закреплять изученное и отслеживать прогресс.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
