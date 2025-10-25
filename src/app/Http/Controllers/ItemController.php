<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab     = $request->query('tab');
        $keyword = trim((string) $request->query('q', ''));

        if ($tab === 'mylist') {
            if (!auth()->check()) {
                session()->put('url.intended', url()->full());
                return redirect()->route('login');
            }
            if (method_exists($request->user(), 'hasVerifiedEmail') && ! $request->user()->hasVerifiedEmail()) {
                $items = Item::query()->whereRaw('0=1')->paginate(12)->withQueryString();
                $activeTab = 'mylist';
                $suppressEmptyMessage = true;
                return view('index', compact('items', 'activeTab', 'suppressEmptyMessage'));
            }

            $items = $request->user()->favorites()
                ->with(['images','categories'])
                ->withCount('favorites')
                ->when($keyword !== '', function ($q) use ($keyword) {
                    $q->where(function($q) use($keyword){
                        $q->where('name', 'like', "%{$keyword}%");
                        // $q->orWhere('brand_name', 'like', "%{$keyword}%");
                    });
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

    public function show(Item $item)
    {
        $item->load(['images','categories','comments.user'])->loadCount('favorites');
        return view('show', compact('item'));
    }

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

        $quoted = collect($categoryOrder)->map(fn($v) => "'".$v."'")->implode(',');
        $categories = Category::whereIn('name', $categoryOrder)
            ->orderByRaw("FIELD(name, {$quoted})")
            ->get();

        return view('items.create', compact('categories','conditions'));
    }

    public function store(StoreItemRequest $request)
    {
        // categories[] の先頭を items.category_id にも入れる（単一列を保持）
        $primaryCategoryId = collect($request->input('categories', []))->filter()->first();

        DB::transaction(function () use ($request, $primaryCategoryId) {
            $item = Item::create([
                'user_id'     => $request->user()->id,
                'category_id' => $primaryCategoryId,     // ← 単一列
                'name'        => $request->name,         // ← item_name は使わない
                'brand_name'  => $request->brand_name,
                'description' => $request->description,
                'price'       => $request->price,
                'condition'   => $request->condition,
            ]);

            // 多対多カテゴリ（中間テーブル）も保持
            $categoryIds = collect($request->input('categories', []))
                ->filter()
                ->unique()
                ->values()
                ->all();
            $item->categories()->sync($categoryIds);

            // 画像保存
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $i => $file) {
                    $path = $file->store('items','public');
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
