<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicHealthScore>
 */
class AcademicHealthScoreFactory extends Factory
{
    protected $model = \App\Models\AcademicHealthScore::class;

    public function definition(): array
    {
        $ahs = $this->faker->numberBetween(0, 100);
        $category = $ahs >= 90 ? 'excellent' : ($ahs >= 75 ? 'stable' : ($ahs >= 50 ? 'at_risk' : 'critical'));

        return [
            'student_id' => User::factory(),
            'attendance_percentage_score' => $this->faker->numberBetween(0, 100),
            'roll_call_score' => $this->faker->numberBetween(0, 100),
            'attendance_streak_score' => $this->faker->numberBetween(0, 100),
            'engagement_trend_score' => $this->faker->numberBetween(0, 100),
            'academic_health_score' => $ahs,
            'health_category' => $category,
            'current_streak' => $this->faker->numberBetween(0, 30),
            'longest_streak' => $this->faker->numberBetween(0, 50),
            'recovery_status' => $this->faker->randomElement(['recovering', 'stable', 'declining', 'critical']),
            'calculation_week' => $this->faker->numberBetween(1, 16),
        ];
    }
}
