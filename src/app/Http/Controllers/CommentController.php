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
        $this->middleware('auth');
    }

    public function store(StoreItemCommentRequest $request, Item $item)
    {
        $data = $request->validated();
        $body = $data['body'];
        $item->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $body,
        ]);

        return back(303);
    }

    public function destroy(Request $request, ItemComment $comment)
    {
        abort_if($comment->user_id !== $request->user()->id, 403);

        $comment->delete();

        return back(303);
    }
}
