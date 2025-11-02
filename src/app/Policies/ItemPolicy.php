<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function update(User $user, Item $item): bool
    {
        return $item->user_id === $user->id;
    }
}
