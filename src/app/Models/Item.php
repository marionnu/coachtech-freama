<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    /** 
     * items テーブルの前提
     * - name（← item_name から統一）
     * - category は多対多（中間: category_item）
     * - 画像は item_images に複数保存
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'name',          // ← item_name をやめて name に統一
        'brand_name',
        'price',
        'description',
        'condition',
        'status',
        // sold_at は購入処理で更新するので fillable には入れない
    ];

    protected $casts = [
        'sold_at'   => 'datetime',
        'price'     => 'integer',
        'condition' => 'integer',
        'status'    => 'integer',
    ];

    protected $appends = [
        'condition_label',
        'status_label',
        'is_sold',
        'category_names',
    ];

    /* ========= Relations ========= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ★ ここを belongsTo → belongsToMany に変更（多対多）
    // 既存：多対多
public function categories()
{
    return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
}

// ★ 追加：旧データ互換（items.category_id を使う単体リレーション）
public function category()
{
    return $this->belongsTo(Category::class);
}

    public function images()
    {
        return $this->hasMany(ItemImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function favorites()
    {
        // users との多対多（中間: favorites）
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(ItemComment::class)->latest();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /* ========= Accessors / Helpers ========= */

    // 商品状態のラベル
    public function getConditionLabelAttribute()
    {
        $map = [
            1 => '新品・未使用',
            2 => '未使用に近い',
            3 => '目立った傷や汚れなし',
            4 => 'やや傷や汚れあり',
            5 => '傷や汚れあり',
        ];
        return $map[$this->condition] ?? '-';
    }

    // ステータスのラベル（必要なら値はプロジェクトに合わせて）
    public function getStatusLabelAttribute()
    {
        $map = [
            0 => '下書き',
            1 => '公開',
            2 => '取引中',
            3 => '売却済',
        ];
        return $map[$this->status] ?? '-';
    }

    public function getIsFavoritedAttribute(): bool
    {
        return auth()->check()
            && $this->favorites()->where('user_id', auth()->id())->exists();
    }

    public function getThumbnailUrlAttribute(): string
    {
        $path = optional($this->images()->orderBy('sort_order')->first())->path;
        if (!$path) {
            return 'https://placehold.co/600x600?text=%E5%95%86%E5%93%81%E7%94%BB%E5%83%8F';
        }
        return preg_match('#^https?://#', $path) ? $path : Storage::url($path);
    }

    // 売却判定
    public function getIsSoldAttribute(): bool
    {
        return !is_null($this->sold_at);
    }

    public function isSold(): bool
    {
        return $this->is_sold;
    }

    // 絞り込み用スコープ
    public function scopeAvailable($q)
    {
        return $q->whereNull('sold_at');
    }

    public function scopeSold($q)
    {
        return $q->whereNotNull('sold_at');
    }

    /* ========= Scopes（読み込み最適化） ========= */
    public function scopeForIndex($q)
    {
        // ★ category → categories に変更
        return $q->with(['images','categories'])->withCount('favorites');
    }

    public function scopeForShow($q)
    {
        return $q->with(['images','categories','comments.user'])->withCount('favorites');
    }

    public function scopeWithFavoriteCount($q)
    {
        return $q->withCount('favorites');
    }

    // ★ 追加：カテゴリ名を「 / 」区切りで返す（新旧どちらでもOK）
public function getCategoryNamesAttribute(): string
{
    // 多対多がロード済みであれば優先
    if ($this->relationLoaded('categories') && $this->categories->isNotEmpty()) {
        return $this->categories->pluck('name')->implode(' / ');
    }
    // 単体リレーションがロード済みならそれを使う
    if ($this->relationLoaded('category') && $this->category) {
        return (string) $this->category->name;
    }
    // 未ロード時の保険
    if ($this->categories()->exists()) {
        return $this->categories()->pluck('name')->implode(' / ');
    }
    return $this->category()->value('name') ?? '';
}

public function getDisplayNameAttribute(): string
{
    return $this->name ?? $this->item_name ?? '';
}

}
