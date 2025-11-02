<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'user_id'     => 1,
            'category_id' => null,
            'name'        => $this->faker->words(3, true),
            'brand_name'  => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->numberBetween(500, 50000),
            'condition'   => $this->faker->numberBetween(1,4),
            'sold_at'     => null,
        ];
    }
}
