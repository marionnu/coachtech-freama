<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemComment;
use App\Http\Requests\StoreItemCommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        // ルート側でも auth を掛けている想定だが二重で保護
        $this->middleware('auth');
    }

    /**
     * コメント投稿
     * POST /items/{item}/comments
     */
    public function store(StoreItemCommentRequest $request, Item $item)
    {
        // ← FormRequest でバリデーション済み。配列から取り出す
        $data = $request->validated();   // ['body' => '...']
        $body = $data['body'];           // 文字列に

        $item->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $body,
        ]);

        return back(303); // or ->with('success', 'コメントを投稿しました。')
    }

    /**
     * 自分のコメントを削除（任意）
     * DELETE /comments/{comment}
     */
    public function destroy(Request $request, ItemComment $comment)
    {
        abort_if($comment->user_id !== $request->user()->id, 403);

        $comment->delete();

        return back(303);
    }
}
