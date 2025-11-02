<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
    public function __construct(){ $this->middleware(['auth','verified']); }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->query('page', 'sell');

        if ($tab === 'buy') {

            $itemIds = Order::where('buyer_id', $user->id)
                ->where('status', 'paid')
                ->pluck('item_id');

            $items = Item::whereIn('id', $itemIds)
                ->with('images')
                ->latest()
                ->paginate(12);
        } else {
            $items = $user->items()
                ->with('images')
                ->latest()
                ->paginate(12);
        }

        return view('mypage.index', compact('user', 'tab', 'items'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('mypage.profile', compact('user','profile'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'        => ['required','string','max:100'],
            'postal_code' => ['nullable','regex:/^\d{3}-?\d{4}$/'],
            'address'     => ['nullable','string','max:255'],
            'building'    => ['nullable','string','max:255'],
            'avatar'      => ['nullable','image','max:2048'],
        ], [], [
            'name' => 'ユーザー名',
            'postal_code' => '郵便番号',
            'address' => '住所',
            'building' => '建物名',
            'avatar' => 'プロフィール画像',
        ]);

        $user->update(['name' => $data['name']]);

        $updateProfile = [
            'postal_code' => $data['postal_code'] ?? null,
            'address'     => $data['address'] ?? null,
            'building'    => $data['building'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $updateProfile['path'] = $path;
            $user->profile()->updateOrCreate(['user_id' => $user->id], $updateProfile);

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました。');
    }
    }
}