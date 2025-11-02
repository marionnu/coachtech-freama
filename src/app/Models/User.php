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
use Illuminate\Support\Facades\Storage;
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

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(ItemComment::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id')->withDefault([
            'path'        => null,
            'postal_code' => null,
            'address'     => null,
            'building'    => null,
        ]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function purchasedItems()
    {
        return $this->hasManyThrough(
            Item::class,
            Order::class,
            'buyer_id',
            'id',
            'id',
            'item_id'
        )->where('orders.status', 'paid');
    }

    public function hasFavorited(Item $item): bool
    {
        return $this->favorites()->where('item_id', $item->id)->exists();
    }

    public function toggleFavorite(Item $item): void
    {
        $this->favorites()->toggle($item->id);
    }

    public function ensureProfile(): UserProfile
    {
        return $this->profile()->firstOrCreate(['user_id' => $this->id]);
    }

    public function getPostalCodeAttribute(): ?string { return $this->profile->postal_code; }
    public function getAddressAttribute(): ?string     { return $this->profile->address; }
    public function getBuildingAttribute(): ?string    { return $this->profile->building; }

    public function getAvatarUrlAttribute(): string
    {
        $path = $this->profile->path;
        if (!$path) {
            return 'https://placehold.co/150x150?text=%20';
        }
        return preg_match('#^https?://#', $path) ? $path : Storage::url($path);
    }
}
