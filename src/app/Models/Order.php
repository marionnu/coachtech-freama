<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'price',
        'payment_method',
        'status',
        'stripe_session_id',
        'stripe_payment_intent',
    ];

    protected $casts = [
        'payment_method' => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    public const PM_KONBINI = 1;
    public const PM_CARD    = 2;
    public const ST_PENDING  = 'pending';
    public const ST_PAID     = 'paid';
    public const ST_CANCELED = 'canceled';

    public function getPaymentLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PM_KONBINI => 'コンビニ払い',
            self::PM_CARD    => 'カード支払い',
            default          => '不明',
        };
    }

    public function getAmountAttribute(): int
    {
        return (int) ($this->price ?? 0);
    }

    public function isPaid(): bool
    {
        return $this->status === self::ST_PAID;
    }

    public function scopePaid($q)
    {
        return $q->where('status', self::ST_PAID);
    }

    public function scopePending($q)
    {
        return $q->where('status', self::ST_PENDING);
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('buyer_id', $userId);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
