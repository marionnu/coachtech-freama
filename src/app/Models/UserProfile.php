<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'path',
        'postal_code',
        'address',
        'building',
    ];

    public function getFullAddressAttribute(): string
    {
        $zip = $this->postal_code ? "〒{$this->postal_code} " : '';
        return trim($zip . ($this->address ?? '') . ' ' . ($this->building ?? ''));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
