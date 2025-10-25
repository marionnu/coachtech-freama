<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'path',
    ];

    // リレーション：画像は1つの商品に属する
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
