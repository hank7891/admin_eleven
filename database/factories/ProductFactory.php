<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'tagline' => fake()->sentence(6),
            'price' => fake()->numberBetween(500, 99000),
            'description' => fake()->paragraphs(2, true),
            'category_id' => null,
            'status_key' => (string) PRODUCT_STATUS_ONLINE,
            'is_featured' => PRODUCT_FEATURED_OFF,
            'sort_order' => 0,
            'start_at' => now()->subDay(),
            'end_at' => now()->addWeek(),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    # 主打商品
    public function featured(): static
    {
        return $this->state(fn () => [
            'is_featured' => PRODUCT_FEATURED_ON,
        ]);
    }

    # 下架商品
    public function offline(): static
    {
        return $this->state(fn () => [
            'status_key' => (string) PRODUCT_STATUS_OFFLINE,
        ]);
    }

    # 尚未開賣
    public function upcoming(): static
    {
        return $this->state(fn () => [
            'start_at' => now()->addDay(),
            'end_at' => now()->addWeek(),
        ]);
    }

    # 已過期
    public function expired(): static
    {
        return $this->state(fn () => [
            'start_at' => now()->subMonth(),
            'end_at' => now()->subDay(),
        ]);
    }

    # 永久上架
    public function perpetual(): static
    {
        return $this->state(fn () => [
            'end_at' => null,
        ]);
    }
}
