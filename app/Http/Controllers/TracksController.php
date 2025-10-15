<?php
namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TracksController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = (string) $request->query('q', '');

        $tracks = Track::where('user_id', Auth::id())
            ->when($searchQuery !== '', function ($query) use ($searchQuery) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $searchQuery) . '%';
                $query->where('name', 'like', $like);
            })
            // !!! ДОБАВЛЕНИЕ EAGER LOADING И ОБЪЕДИНЕНИЯ !!!
            ->with(['notes', 'exercises']) // Подгружаем связанные данные
            ->get()
            ->map(function ($track) {
                // Объединяем заметки и упражнения
                $items = $track->notes
                    ->map(fn($item) => ['type' => 'note', 'data' => $item])
                    ->merge(
                        $track->exercises->map(fn($item) => ['type' => 'exercise', 'data' => $item])
                    )
                    ->sortBy(fn($item) => $item['data']->created_at)
                    ->values();

                $track->track_items = $items;
                return $track;
            });
        // !!! КОНЕЦ ДОБАВЛЕНИЯ !!!

        return view('tracks.index', compact('tracks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Track::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);
        return redirect()->route('tracks.index');
    }

    public function show(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        $notes = $track->notes;
        return view('tracks.show', compact('track', 'notes'));
    }

    public function update(Request $request, Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $track->update(['name' => $validated['name']]);

        return redirect()->route('tracks.show', $track)->with('success', 'Название трека обновлено.');
    }

    public function destroy(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        $track->delete();
        return redirect()->route('tracks.index')->with('success', 'Трек успешно удалён.');
    }

    public function cloneTrack(Track $track)
    {
        $newTrack = Auth::user()->tracks()->create([
            'name' => $track->name . ' (Копия)',
            // Другие поля, если есть, которые нужно скопировать
        ]);

        foreach ($track->notes as $note) {
            $newTrack->notes()->create([
                'title' => $note->title,
                'content' => $note->content,
                // Другие поля заметки
            ]);
        }

        return redirect()->route('tracks.show', $newTrack)->with('success', 'Трек успешно добавлен в ваши треки!');
    }

    public function share(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403, 'У вас нет прав для публикации этого трека.');
        }

        $track->is_public = true;
        $track->save();

        return redirect()->route('tracks.show', $track)->with('success', 'Трек успешно опубликован и теперь виден всем пользователям!');
    }

    public function unshare(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403, 'У вас нет прав для снятия этого трека с публикации.');
        }

        $track->is_public = false;
        $track->save();

        return redirect()->route('tracks.show', $track)->with('success', 'Трек успешно снят с публикации и больше не виден другим пользователям.');
    }
}
