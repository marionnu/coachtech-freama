<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * マイグレーションに合わせて必要カラムのみ許可
     * - DB が 'amount' ではなく 'price' の場合はこのままでOK
     */
    protected $fillable = [
        'item_id',
        'buyer_id',
        'price',                 // DB が 'amount' の場合は 'amount' に変更
        'payment_method',        // 1:コンビニ, 2:カード
        'status',                // pending / paid / canceled
        'stripe_session_id',     // 任意
        'stripe_payment_intent', // 任意
    ];

    protected $casts = [
        'payment_method' => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /* ===== 定数 ===== */
    // 支払い方法
    public const PM_KONBINI = 1;
    public const PM_CARD    = 2;

    // ステータス
    public const ST_PENDING  = 'pending';
    public const ST_PAID     = 'paid';
    public const ST_CANCELED = 'canceled';

    /* ===== アクセサ / ヘルパ ===== */

    // 表示用ラベル（$order->payment_label）
    public function getPaymentLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PM_KONBINI => 'コンビニ払い',
            self::PM_CARD    => 'カード支払い',
            default          => '不明',
        };
    }

    // 価格エイリアス：コード側で $order->amount でも参照できる
    public function getAmountAttribute(): int
    {
        return (int) ($this->price ?? 0);
    }

    public function isPaid(): bool
    {
        return $this->status === self::ST_PAID;
    }

    /* ===== スコープ ===== */

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

    /* ===== リレーション ===== */

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
