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
        return view('notes.create', compact('track'));
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
            $note = $track->notes()->create(['content' => $content]);
            if (!$note) {
                Log::error('Failed to create note', ['content' => $content]);
                return back()->withErrors(['error' => 'Не удалось сохранить заметку']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while creating note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при сохранении заметки']);
        }
        return redirect()->route('tracks.show', $track);
    }

    public function edit(Track $track, Note $note)
    {
        if ($track->user_id !== Auth::id() || $note->track_id !== $track->id) {
            abort(403);
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
            $note->update(['content' => $content]);
        } catch (\Exception $e) {
            Log::error('Exception while updating note', ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Ошибка при обновлении заметки']);
        }
        return redirect()->route('tracks.show', $track);
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
        return redirect()->route('tracks.show', $track)->with('success', 'Заметка успешно удалена');
    }
}
