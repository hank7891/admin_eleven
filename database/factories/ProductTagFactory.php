<?php

namespace Database\Factories;

use App\Models\ProductTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTagFactory extends Factory
{
    protected $model = ProductTag::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'is_active' => STATUS_ACTIVE,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
