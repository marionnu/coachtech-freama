<?php

namespace App\Models;

use App\Models\Item;
use App\Models\ItemComment;
use App\Models\Order;
use App\Models\UserProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;   // ★ 追加
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    /* ============ Relations ============ */

    // 自分が出品した商品
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // お気に入り
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites')->withTimestamps();
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(ItemComment::class);
    }

    // プロフィール（住所/画像など）: デフォルト空オブジェクトを返す
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id')->withDefault([
            'path'        => null,
            'postal_code' => null,
            'address'     => null,
            'building'    => null,
        ]);
    }

    // 自分の購入注文
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // 購入した「商品」へ直接アクセス（paid のみ）
    public function purchasedItems()
    {
        return $this->hasManyThrough(
            Item::class,          // 最終モデル
            Order::class,         // 中間モデル
            'buyer_id',           // 中間: User に対する外部キー
            'id',                 // 最終: Order が参照する Item の主キー
            'id',                 // User の主キー
            'item_id'             // 中間: Item への外部キー
        )->where('orders.status', 'paid');
    }

    /* ============ Helpers / Accessors ============ */

    // お気に入り済み？
    public function hasFavorited(Item $item): bool
    {
        return $this->favorites()->where('item_id', $item->id)->exists();
    }

    public function toggleFavorite(Item $item): void
    {
        $this->favorites()->toggle($item->id);
    }

    // プロフィールを必ず用意
    public function ensureProfile(): UserProfile
    {
        return $this->profile()->firstOrCreate(['user_id' => $this->id]);
    }

    // 住所系（withDefaultにより null 安全）
    public function getPostalCodeAttribute(): ?string { return $this->profile->postal_code; }
    public function getAddressAttribute(): ?string     { return $this->profile->address; }
    public function getBuildingAttribute(): ?string    { return $this->profile->building; }

    // アバターURL（storage 保存 or プレースホルダ）
    public function getAvatarUrlAttribute(): string
    {
        $path = $this->profile->path;
        if (!$path) {
            return 'https://placehold.co/150x150?text=%20';
        }
        return preg_match('#^https?://#', $path) ? $path : Storage::url($path);
    }
}
