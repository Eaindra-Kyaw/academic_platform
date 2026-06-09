<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = \App\Models\Announcement::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'target_role' => $this->faker->randomElement(['admin', 'lecturer', 'student', 'all']),
            'posted_by' => User::factory(),
            'is_active' => true,
            'published_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
