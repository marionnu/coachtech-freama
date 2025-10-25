<?php

namespace App\Models;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemComment extends Model
{
    use HasFactory;

    // 一括代入で受け付けるカラム
    protected $fillable = [
        'user_id',
        'item_id',
        'body',
    ];

    // （必要なら）キャストや隠し属性を追加できます
    // protected $hidden = [];
    // protected $casts  = [];

    /* ========= Relations ========= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /* ========= Scopes / Helpers（任意） ========= */

    // 新しい順で取りたいときに使える補助スコープ
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }
}
