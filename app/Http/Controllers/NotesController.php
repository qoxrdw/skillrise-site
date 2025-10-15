<?php
namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            // !!! ГЛАВНОЕ ИЗМЕНЕНИЕ: Установка типа заметки !!!
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

        // 💡 ПОДСЛАХОВКА (Обязательно, если заметка с 'handwriting' открывается по маршруту 'notes.edit')
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
        Log::info('Update note request data', $request->all());
        $content = $request->input('content', '<p>Пустая заметка</p>');
        Log::info('Content to update', ['content' => $content]);
        $request->merge(['content' => $content]);
        $request->validate(['content' => 'required|string']);
        if (trim(strip_tags($content)) === '') {
            $content = '<p>Пустая заметка</p>';
        }
        try {
            // !!! ИСПРАВЛЕНИЕ: Удаляем принудительную установку 'type' => 'text'.
            // Обновляем ТОЛЬКО контент, сохраняя существующий тип заметки (text или handwriting).
            $note->update([
                'content' => $content,
                // 'type' => 'text' // <-- ЭТУ СТРОКУ НУЖНО УДАЛИТЬ ИЛИ ЗАКОММЕНТИРОВАТЬ
            ]);
        } catch (\Exception $e) {
            Log::error('Exception while updating note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при обновлении заметки']);
        }
        return redirect()->route('tracks.show', $track);
    }

    public function editHandwriting(Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
        }

        // 💡 ПОДСЛАХОВКА: Если заметка по ошибке пришла сюда с типом 'text', перенаправляем ее обратно
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
            $note->delete();
        } catch (\Exception $e) {
            Log::error('Exception while deleting note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при удалении заметки']);
        }
        return back()->with('success', 'Заметка удалена.');
    }
}
