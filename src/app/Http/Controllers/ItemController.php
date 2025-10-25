<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;   // ★ 追加（前メッセで作ったFormRequest）
use App\Models\Item;
use App\Models\Category;                  // ★ 追加
use App\Models\ItemImage;                 // ★ 追加
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;        // ★ 追加

class ItemController extends Controller
{
    /**
     * トップ（おすすめ or マイリスト）
     * /?tab=mylist ならマイリストを表示
     * 検索: q（商品名の部分一致）
     */
    public function index(Request $request)
    {
        $tab     = $request->query('tab');
        $keyword = trim((string) $request->query('q', ''));

        if ($tab === 'mylist') {
            // ゲストはログインへ（戻り先を記憶）
            if (!auth()->check()) {
                session()->put('url.intended', url()->full());
                return redirect()->route('login');
            }

            // メール未認証なら空リスト返却
            if (method_exists($request->user(), 'hasVerifiedEmail') && ! $request->user()->hasVerifiedEmail()) {
                $items = Item::query()->whereRaw('0=1')->paginate(12)->withQueryString();
                $activeTab = 'mylist';
                $suppressEmptyMessage = true;
                return view('index', compact('items', 'activeTab', 'suppressEmptyMessage'));
            }

            // ★ リレーション名を images / categories に修正
            $items = $request->user()->favorites()       // belongsToMany(Item::class, 'favorites')
                ->with(['images','categories'])
                ->withCount('favorites')
                ->when($keyword !== '', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");      // ← item_name → name
                    // $q->orWhere('brand_name', 'like', "%{$keyword}%");
                })
                ->when(auth()->check(), function ($q) {
                    $q->where('items.user_id', '!=', auth()->id());
                })
                ->orderByDesc('favorites.created_at')
                ->paginate(12)->withQueryString();

            $activeTab = 'mylist';
        } else {
            $items = Item::with(['images','categories'])
                ->withCount('favorites')
                ->when($keyword !== '', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                    // $q->orWhere('brand_name', 'like', "%{$keyword}%");
                })
                ->when(auth()->check(), function ($q) {
                    $q->where('user_id', '!=', auth()->id());
                })
                ->latest('id')
                ->paginate(12)->withQueryString();

            $activeTab = 'recommend';
        }

        return view('index', compact('items','activeTab'));
    }

    /** 詳細 /item/{item} */
    public function show(Item $item)
    {
        $item->load(['images','categories','comments.user'])->loadCount('favorites'); // ← category→categories
        return view('show', compact('item'));
    }

    /** 互換: /mylist → /?tab=mylist */
    public function mylist(Request $request)
    {
        return redirect()->to(url('/?tab=mylist'));
    }

    /** 出品フォーム */
    public function create()
    {
        $conditions = [
            1=>'良好',
            2=>'目立った傷や汚れなし',
            3=>'やや傷や汚れあり',
            4=>'状態が悪い',
        ];
        $categoryOrder = [
        'ファッション','家電','インテリア','レディース','メンズ','コスメ',
        '本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー',
        '食べもの','ベビー・キッズ','その他',
    ];

    // DBにあるものだけをこの順で取得
    $quoted = collect($categoryOrder)->map(fn($v) => "'".$v."'")->implode(',');
    $categories = Category::whereIn('name', $categoryOrder)
        ->orderByRaw("FIELD(name, {$quoted})")
        ->get();

    return view('items.create', compact('categories','conditions'));
}

    // そのまま置き換え
public function store(StoreItemRequest $request)
{
    // 先頭のカテゴリを items.category_id にも入れる（NOT NULL対策）
    $primaryCategoryId = collect($request->input('categories', []))->filter()->first();

    DB::transaction(function () use ($request, $primaryCategoryId) {
        $item = Item::create([
            'user_id'     => $request->user()->id,
            'category_id' => $primaryCategoryId,  // ← ここで使用
            'item_name'   => $request->name,
            'name'        => $request->name,
            'brand_name'  => $request->brand_name,
            'description' => $request->description,
            'price'       => $request->price,
            'condition'   => $request->condition,
        ]);

        // プライマリカテゴリ計算の下あたりに追加
$categoryIds = collect($request->input('categories', []))
    ->filter()
    ->unique()
    ->values()
    ->all();

// …
$item->categories()->sync($categoryIds);

        // 画像（storage/app/public/items/...）
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('items','public');  // php artisan storage:link 済み前提
                ItemImage::create([
                    'item_id'    => $item->id,
                    'path'       => $path,
                    'sort_order' => $i + 1,
                ]);
            }
        }
    });

    return redirect()->route('items.index')->with('success','商品を出品しました');
}
}
