<?php

use App\Http\Controllers\ExercisesController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TracksController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackSharingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/tracks/sharing', [TrackSharingController::class, 'index'])->name('tracks.sharing');

Route::middleware(['auth'])->group(function () {
    Route::get('/tracks', [TracksController::class, 'index'])->name('tracks.index');
    Route::post('/tracks', [TracksController::class, 'store'])->name('tracks.store');
    Route::get('/tracks/{track}', [TracksController::class, 'show'])->name('tracks.show');
    Route::patch('/tracks/{track}', [TracksController::class, 'update'])->name('tracks.update');
    Route::post('/tracks/{track}/clone', [TracksController::class, 'cloneTrack'])->name('tracks.clone');
    Route::post('/tracks/{track}/share', [TracksController::class, 'share'])->name('tracks.share');
    Route::post('/tracks/{track}/unshare', [TracksController::class, 'unshare'])->name('tracks.unshare');
    Route::delete('/tracks/{track}', [TracksController::class, 'destroy'])->name('tracks.destroy'); // Новый маршрут для удаления


    // !!! ДОБАВЛЕНЫ МАРШРУТЫ ДЛЯ РУКОПИСНЫХ ЗАМЕТОК (Canvas) !!!

    // 1. Создание формы
    Route::get('/tracks/{track}/notes/create/handwriting', [NotesController::class, 'createHandwriting'])->name('notes.create.handwriting');

    // 2. Сохранение
    Route::post('/tracks/{track}/notes/store/handwriting', [NotesController::class, 'storeHandwriting'])->name('notes.store.handwriting');

    // 3. Редактирование формы
    Route::get('/tracks/{track}/notes/{note}/edit/handwriting', [NotesController::class, 'editHandwriting'])->name('notes.edit.handwriting');

    // 4. Обновление
    Route::patch('/tracks/{track}/notes/{note}/update/handwriting', [NotesController::class, 'updateHandwriting'])->name('notes.update.handwriting');

    // Маршруты для текстовых заметок (Quill)
    Route::get('/tracks/{track}/notes/create', [NotesController::class, 'create'])->name('notes.create');
    Route::post('/tracks/{track}/notes', [NotesController::class, 'store'])->name('notes.store');
    Route::get('/tracks/{track}/notes/{note}/edit', [NotesController::class, 'edit'])->name('notes.edit');
    Route::patch('/tracks/{track}/notes/{note}', [NotesController::class, 'update'])->name('notes.update');
    Route::delete('/tracks/{track}/notes/{note}', [NotesController::class, 'destroy'])->name('notes.destroy');




    // Маршруты для упражнений, вложенные в треки
    Route::get('/tracks/{track}/exercises', [ExercisesController::class, 'index'])->name('exercises.index');
    Route::get('/tracks/{track}/exercises/create', [ExercisesController::class, 'create'])->name('exercises.create');
    Route::post('/tracks/{track}/exercises', [ExercisesController::class, 'store'])->name('exercises.store');
    Route::get('/tracks/{track}/exercises/{exercise}/take', [ExercisesController::class, 'take'])->name('exercises.take');
    Route::post('/tracks/{track}/exercises/{exercise}/submit', [ExercisesController::class, 'submit'])->name('exercises.submit');
    Route::delete('/tracks/{track}/exercises/{exercise}', [ExercisesController::class, 'destroy'])->name('exercises.destroy');


    // Удалены старые маршруты для упражнений, которые больше не используются
    // Route::get('/exercises', [ExercisesController::class, 'index'])->name('exercises.index');
    // Route::get('/exercises/create', [ExercisesController::class, 'create'])->name('exercises.create');
    // Route::post('/exercises', [ExercisesController::class, 'store'])->name('exercises.store');
    // Route::get('/exercises/{exercise}/take', [ExercisesController::class, 'take'])->name('exercises.take');
    // Route::post('/exercises/{exercise}/submit', [ExercisesController::class, 'submit'])->name('exercises.submit');
    // Route::delete('/exercises/{exercise}', [ExercisesController::class, 'destroy'])->name('exercises.destroy');

});


require __DIR__.'/auth.php';
