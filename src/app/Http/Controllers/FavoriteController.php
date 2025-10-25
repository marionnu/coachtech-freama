<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * お気に入り登録（♡ ON）
     * POST /items/{item}/favorite
     */
    public function store(Request $request, Item $item)
    {
        $user = $request->user();

        // 既に登録済みでも例外にならないように
        $user->favorites()->syncWithoutDetaching([$item->id]);

        // Ajax ならJSON、通常遷移なら直前ページへ
        if ($request->wantsJson()) {
            return response()->json([
                'favorited' => true,
                'count'     => $item->favorites()->count(),
            ]);
        }

        // 303 See Other でリロード時の再POSTを防止
        return back(303);
    }

    /**
     * お気に入り解除（♡ OFF）
     * DELETE /items/{item}/favorite
     */
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
