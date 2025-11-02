<?php

namespace App\Models;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }
}
