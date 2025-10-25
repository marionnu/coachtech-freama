<?php

namespace App\Http\Controllers;

use App\Models\Item;   // ★ 追加
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
    public function __construct(){ $this->middleware(['auth','verified']); }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->query('page', 'sell'); // 'sell' or 'buy'

        if ($tab === 'buy') {
            // 購入済み（paid）注文から商品IDを集めて一覧化
            $itemIds = Order::where('buyer_id', $user->id)
                ->where('status', 'paid')
                ->pluck('item_id');

            $items = Item::whereIn('id', $itemIds)
                ->with('images')
                ->latest()
                ->paginate(12);            // 片方のタブだけ表示するのでページ名分離は不要
        } else {
            // 自分が出品した商品
            $items = $user->items()
                ->with('images')
                ->latest()
                ->paginate(12);
        }

        // ★ ビューが期待する変数名に統一して渡す
        return view('mypage.index', compact('user', 'tab', 'items'));
    }


    // プロフィール編集 画面
    public function editProfile()
    {
        $user = Auth::user();
        $profile = $user->profile; // null可
        return view('mypage.profile', compact('user','profile'));
    }

    // プロフィール更新
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'        => ['required','string','max:100'],
            'postal_code' => ['nullable','regex:/^\d{3}-?\d{4}$/'],
            'address'     => ['nullable','string','max:255'],
            'building'    => ['nullable','string','max:255'],
            'avatar'      => ['nullable','image','max:2048'], // 2MB
        ], [], [
            'name' => 'ユーザー名',
            'postal_code' => '郵便番号',
            'address' => '住所',
            'building' => '建物名',
            'avatar' => 'プロフィール画像',
        ]);

        // users.name を更新
        $user->update(['name' => $data['name']]);

        // 画像アップロード（storage/app/public/avatars）
        $updateProfile = [
            'postal_code' => $data['postal_code'] ?? null,
            'address'     => $data['address'] ?? null,
            'building'    => $data['building'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public'); // ★ storage 保存
            $updateProfile['path'] = $path; // user_profiles.path
        }

        // user_profiles に upsert
        $user->profile()->updateOrCreate(['user_id' => $user->id], $updateProfile);

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました。');
    }
}
