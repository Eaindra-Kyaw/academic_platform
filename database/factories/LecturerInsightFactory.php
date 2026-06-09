<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LecturerInsight>
 */
class LecturerInsightFactory extends Factory
{
    protected $model = \App\Models\LecturerInsight::class;

    public function definition(): array
    {
        return [
            'lecturer_id' => User::factory(),
            'course_id' => Course::factory(),
            'insight_type' => $this->faker->randomElement(['trend_alert', 'anomaly', 'recommendation']),
            'insight_text' => $this->faker->paragraph(),
            'insight_data' => json_encode(['key' => 'value']),
            'is_dismissed' => false,
        ];
    }
}
