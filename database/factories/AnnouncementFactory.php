<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * 對應模型
     *
     * @var class-string<\App\Models\Announcement>
     */
    protected $model = Announcement::class;

    /**
     * 定義預設資料
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => ANNOUNCEMENT_TYPE_GENERAL,
            'title' => fake()->sentence(4),
            'summary' => fake()->sentence(10),
            'content' => fake()->paragraphs(3, true),
            'is_active' => STATUS_ACTIVE,
            'start_at' => now()->subDay(),
            'end_at' => now()->addWeek(),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    /**
     * 全系統公告狀態
     */
    public function system(): static
    {
        return $this->state(fn () => [
            'type' => ANNOUNCEMENT_TYPE_SYSTEM,
        ]);
    }

    /**
     * 永久顯示狀態
     */
    public function perpetual(): static
    {
        return $this->state(fn () => [
            'end_at' => null,
        ]);
    }

    /**
     * 停用狀態
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => STATUS_INACTIVE,
        ]);
    }
}

