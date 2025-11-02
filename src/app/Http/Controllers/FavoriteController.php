<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $user = $request->user();
        $user->favorites()->syncWithoutDetaching([$item->id]);
        if ($request->wantsJson()) {
            return response()->json([
                'favorited' => true,
                'count'     => $item->favorites()->count(),
            ]);
        }

        return back(303);
    }

    public function destroy(Request $request, Item $item)
    {
        $user = $request->user();
        $user->favorites()->detach($item->id);
        if ($request->wantsJson()) {
            return response()->json([
                'favorited' => false,
                'count'     => $item->favorites()->count(),
            ]);
        }
        return back(303);
    }
}
