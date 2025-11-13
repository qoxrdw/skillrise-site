<?php
namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// !!! ДОБАВЛЯЕМ ФАСАД STORAGE ДЛЯ РАБОТЫ С ФАЙЛАМИ !!!
use Illuminate\Support\Facades\Storage;

class NotesController extends Controller
{
    public function create(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        // Заметка по умолчанию (Quill)
        return view('notes.create', compact('track'));
    }

    // НОВЫЙ МЕТОД: Отображение формы для рукописного ввода
    public function createHandwriting(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        // Заметка для планшетов (Canvas)
        return view('notes.create_handwriting', compact('track'));
    }

    // 🎙️ НОВЫЙ МЕТОД: Отображение формы для записи голосовой заметки
    public function createVoice(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        // Заметка для записи голоса
        return view('notes.create_voice', compact('track'));
    }


    /**
     * Сохраняет голосовую заметку.
     * Ожидает файл с именем 'audio'.
     * Возвращает JSON-ответ для AJAX-запроса.
     */
    public function storeVoice(Request $request, Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Недостаточно прав для этого действия.'], 403);
        }

        // 1. Валидация: проверяем, что поле 'audio' существует и является файлом
        $request->validate([
            'audio' => 'required|file|mimes:webm,mp3,wav,ogg,m4a|max:10240', // увеличено до 10MB
        ]);

        if ($request->hasFile('audio')) {
            try {
                $file = $request->file('audio');
                // 2. Сохранение файла на диск. 'public' - это драйвер.
                $path = $file->store('notes/voice/' . $track->id, 'public');

                if (!$path) {
                    Log::error('Failed to save audio file to disk.');
                    return response()->json(['status' => 'error', 'message' => 'Не удалось сохранить аудиофайл.'], 500);
                }

                // 3. Создание записи в БД
                // !!! ИСПРАВЛЕНО: Сохраняем путь к файлу в столбце 'content' !!!
                $note = $track->notes()->create([
                    'type' => 'voice', // Устанавливаем тип
                    'content' => $path, // Путь к файлу теперь хранится в 'content'
                ]);

                if (!$note) {
                    Storage::disk('public')->delete($path); // Удаляем файл, если запись в БД не удалась
                    Log::error('Failed to create voice note record', ['path' => $path]);
                    return response()->json(['status' => 'error', 'message' => 'Не удалось сохранить голосовую заметку.'], 500);
                }

                // Возвращаем успешный JSON ответ с URL для перенаправления
                return response()->json([
                    'status' => 'success',
                    'message' => 'Голосовая заметка успешно сохранена.',
                    'redirect_url' => route('tracks.show', $track) . '?success=' . urlencode('Голосовая заметка успешно создана!'),
                ], 200);

            } catch (\Exception $e) {
                Log::error('Exception while storing voice note', ['exception' => $e->getMessage()]);
                return response()->json(['status' => 'error', 'message' => 'Ошибка при сохранении голосовой заметки: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Аудиофайл не был загружен.'], 400);
    }

    public function store(Request $request, Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        Log::info('Store note request data', $request->all());
        $content = $request->input('content', '<p>Пустая заметка</p>');
        Log::info('Content to save', ['content' => $content]);
        $request->merge(['content' => $content]);
        $request->validate(['content' => 'required|string']);
        if (trim(strip_tags($content)) === '') {
            $content = '<p>Пустая заметка</p>';
        }
        try {
            // Убедитесь, что здесь всегда 'text'
            $note = $track->notes()->create([
                'content' => $content,
                'type' => 'text'
            ]);
            if (!$note) {
                Log::error('Failed to create note', ['content' => $content]);
                return back()->withErrors(['error' => 'Не удалось сохранить заметку']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while storing note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при сохранении заметки']);
        }

        return redirect()->route('tracks.show', $track)->with('success', 'Заметка успешно сохранена.');
    }

    // НОВЫЙ МЕТОД: Сохранение рукописной заметки
    public function storeHandwriting(Request $request, Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }

        // Валидация данных
        $request->validate([
            'content_json' => 'required|json',
            'content_base64' => 'required|string',
        ]);

        $contentJson = $request->input('content_json');

        try {
            $note = $track->notes()->create([
                'content' => $contentJson,
                'type' => 'handwriting', // <-- ЭТО КЛЮЧЕВОЙ МОМЕНТ
            ]);

            if (!$note) {
                Log::error('Failed to create handwriting note', ['content' => $contentJson]);
                return back()->withErrors(['error' => 'Не удалось сохранить рукописную заметку']);
            }

        } catch (\Exception $e) {
            Log::error('Exception while storing handwriting note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при сохранении рукописной заметки']);
        }

        return redirect()->route('tracks.show', $track)->with('success', 'Рукописная заметка успешно создана!');
    }


    public function edit(Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
        }

        // 🎙️ НОВАЯ ПРОВЕРКА: Голосовую заметку редактировать нельзя
        if (($note->type ?? 'text') === 'voice') {
            return redirect()->route('tracks.show', $track)->withErrors(['error' => 'Голосовые заметки нельзя редактировать.']);
        }

        // Перенаправляем на правильный маршрут редактирования холста
        if (($note->type ?? 'text') === 'handwriting') {
            return redirect()->route('notes.edit.handwriting', [$track, $note]);
        }

        return view('notes.edit', compact('track', 'note'));
    }

    public function update(Request $request, Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
        }

        // 🎙️ НОВАЯ ПРОВЕРКА: Голосовую заметку нельзя обновить через этот метод
        if (($note->type ?? 'text') === 'voice') {
            return redirect()->route('tracks.show', $track)->withErrors(['error' => 'Голосовые заметки нельзя обновлять.']);
        }

        Log::info('Update note request data', $request->all());
        $content = $request->input('content', '<p>Пустая заметка</p>');
        Log::info('Content to update', ['content' => $content]);
        $request->merge(['content' => $content]);
        $request->validate(['content' => 'required|string']);
        if (trim(strip_tags($content)) === '') {
            $content = '<p>Пустая заметка</p>';
        }
        try {
            $note->update([
                'content' => $content,
                // 'type' => 'text' // Убедитесь, что эта строка закомментирована/удалена, если тип должен сохраняться
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while updating note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при обновлении заметки']);
        }
        return redirect()->route('tracks.show', $track)->with('success', 'Заметка успешно обновлена.');
    }

    public function editHandwriting(Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
        }

        // 🎙️ НОВАЯ ПРОВЕРКА: Голосовую заметку редактировать нельзя
        if (($note->type ?? 'text') === 'voice') {
            return redirect()->route('tracks.show', $track)->withErrors(['error' => 'Голосовые заметки нельзя редактировать.']);
        }

        // Если заметка по ошибке пришла сюда с типом 'text', перенаправляем ее обратно
        if (($note->type ?? 'text') === 'text') {
            return redirect()->route('notes.edit', [$track, $note]);
        }

        return view('notes.edit_handwriting', compact('track', 'note'));
    }

    /**
     * Обновляет рукописную заметку в базе данных.
     */
    public function updateHandwriting(Request $request, Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
        }

        // 🎙️ НОВАЯ ПРОВЕРКА: Голосовую заметку нельзя обновить через этот метод
        if (($note->type ?? 'text') === 'voice') {
            return redirect()->route('tracks.show', $track)->withErrors(['error' => 'Голосовые заметки нельзя обновлять.']);
        }


        $request->validate([
            'content_json' => 'required|string', // Fabric.js JSON
            'content_base64' => 'required|string', // Base64 Preview
        ]);

        $contentJson = $request->input('content_json');

        try {
            $note->update([
                'content' => $contentJson, // Сохраняем только JSON
                'type' => 'handwriting', // Убеждаемся, что тип установлен
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while updating handwriting note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при обновлении рукописной заметки']);
        }

        return redirect()->route('tracks.show', $track)->with('success', 'Заметка успешно обновлена.');
    }

    public function destroy(Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
        }
        try {
            // !!! ИСПРАВЛЕНИЕ: УДАЛЕНИЕ ФАЙЛА ДЛЯ ГОЛОСОВОЙ ЗАМЕТКИ !!!
            // $note->content теперь гарантированно содержит путь к файлу
            if ($note->type === 'voice' && $note->content) {
                Storage::disk('public')->delete($note->content);
            }

            $note->delete();
        } catch (\Exception $e) {
            Log::error('Exception while deleting note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при удалении заметки']);
        }
        return back()->with('success', 'Заметка удалена.');
    }
}
