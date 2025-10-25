<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function __construct(){ $this->middleware('auth'); }

    // 購入画面
    public function create(Item $item)
    {
        $user = Auth::user();
        abort_if($item->user_id === $user->id, 403, '自分の商品は購入できません。');
        abort_if($item->sold_at, 400, 'すでに購入されています。');

        return view('purchase.create', ['item' => $item, 'user' => $user]);
    }

    // 「購入する」
    public function store(Request $request, Item $item)
    {
        $user = Auth::user();
        abort_if($item->user_id === $user->id, 403);
        abort_if($item->sold_at, 400);

        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['konbini','card'])],
        ]);
        $pmValue = $data['payment_method'] === 'konbini' ? 1 : 2;

        $order = Order::create([
            'item_id'        => $item->id,
            'buyer_id'       => $user->id,
            'price'          => (int)$item->price,
            'payment_method' => $pmValue,
            'status'         => 'pending',
        ]);

        if (config('services.stripe.key') && config('services.stripe.secret')) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment',
                'payment_method_types' => $data['payment_method'] === 'konbini' ? ['konbini'] : ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount'  => (int)$item->price,
                    ],
                    'quantity' => 1,
                ]],
                'customer_email' => $user->email,
                'success_url'    => route('purchase.success', $item) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'     => route('purchase.cancel', $item),
                'expires_at'     => now()->addMinutes(30)->timestamp,
            ]);
            $order->update([
                'stripe_session_id'     => $session->id,
                'stripe_payment_intent' => $session->payment_intent ?? null,
            ]);
            return redirect($session->url);
        }

        // Stripe未設定：即時確定
        DB::transaction(function () use ($item, $order) {
            $order->update(['status' => 'paid']);
            $item->sold_at = now();   // ← 修正点
            $item->save();            // ← 修正点
        });

        return redirect()
            ->route('items.show', $item)
            ->with('success', '購入が完了しました。ありがとうございます！');
    }

    // 送付先住所変更（表示）
    public function editAddress(Item $item)
    {
        return view('purchase.address', ['item' => $item, 'user' => Auth::user()]);
    }

    // 送付先住所変更（更新）
    public function updateAddress(Request $request, Item $item)
    {
        $data = $request->validate([
            'postal_code' => ['required', 'regex:/^\d{3}-?\d{4}$/'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ], [], [
            'postal_code' => '郵便番号',
            'address'     => '住所',
            'building'    => '建物名',
        ]);

        $user = Auth::user();
        $user->profile()->updateOrCreate(['user_id' => $user->id], $data);

        return redirect()->route('purchase.create', $item)->with('success', '住所を更新しました。');
    }

    // 決済成功
    public function success(Request $request, Item $item)
    {
        if ($request->filled('session_id') && config('services.stripe.secret')) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($request->query('session_id'));

            $order = Order::where('stripe_session_id', $session->id)->first();
            if ($order && $order->status !== 'paid') {
                DB::transaction(function () use ($order, $item) {
                    $order->update(['status' => 'paid']);
                    $item->sold_at = now();   // ← 修正点
                    $item->save();            // ← 修正点
                });
            }
        }

        return redirect()->route('items.show', $item)->with('success', '購入が完了しました。ありがとうございます！');
    }

    // キャンセル
    public function cancel(Item $item)
    {
        return redirect()->route('purchase.create', $item)->with('error', '決済がキャンセルされました。');
    }
}
