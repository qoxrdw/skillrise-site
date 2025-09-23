<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;

class TrackSharingController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = (string) $request->query('q', '');

        $tracks = Track::where('is_public', true)
            ->when($searchQuery !== '', function ($query) use ($searchQuery) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $searchQuery) . '%';
                $query->where('name', 'like', $like);
            })
            ->with('user')
            ->get();

        return view('tracks.sharing', compact('tracks'));
    }
}
