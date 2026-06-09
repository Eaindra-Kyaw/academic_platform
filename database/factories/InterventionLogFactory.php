<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InterventionLog>
 */
class InterventionLogFactory extends Factory
{
    protected $model = \App\Models\InterventionLog::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'intervention_type' => $this->faker->randomElement(['notification', 'recommendation', 'alert']),
            'intervention_trigger' => $this->faker->sentence(),
            'intervention_action' => $this->faker->paragraph(),
            'effectiveness_score' => $this->faker->optional()->randomFloat(1, 1, 5),
        ];
    }
}
