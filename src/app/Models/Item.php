<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand_name',
        'price',
        'description',
        'condition',
        'status',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function categories()
{
    return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
}

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

    public function getIsSoldAttribute(): bool
    {
        return !is_null($this->sold_at);
    }

    public function isSold(): bool
    {
        return $this->is_sold;
    }

    public function scopeAvailable($q)
    {
        return $q->whereNull('sold_at');
    }

    public function scopeSold($q)
    {
        return $q->whereNotNull('sold_at');
    }

    public function scopeForIndex($q)
    {
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

public function getCategoryNamesAttribute(): string
{
    if ($this->relationLoaded('categories') && $this->categories->isNotEmpty()) {
        return $this->categories->pluck('name')->implode(' / ');
    }

    if ($this->relationLoaded('category') && $this->category) {
        return (string) $this->category->name;
    }

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
