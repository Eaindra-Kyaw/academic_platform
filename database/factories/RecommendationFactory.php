<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recommendation>
 */
class RecommendationFactory extends Factory
{
    protected $model = \App\Models\Recommendation::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'recommendation_type' => $this->faker->randomElement(['warning', 'suggestion', 'critical', 'encouragement']),
            'recommendation_text' => $this->faker->sentence(),
            'priority' => $this->faker->randomElement(['high', 'medium', 'low']),
            'is_read' => false,
            'is_actioned' => false,
            'generated_at' => $this->faker->dateTime(),
            'expires_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
