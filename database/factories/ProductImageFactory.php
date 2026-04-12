<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => null,
            'image_path' => 'uploads/image/' . date('Y/m') . '/' . fake()->uuid() . '.jpg',
            'image_alt' => fake()->sentence(4),
            'is_primary' => 0,
            'sort_order' => 0,
        ];
    }

    # 主圖
    public function primary(): static
    {
        return $this->state(fn () => [
            'is_primary' => 1,
        ]);
    }
}
