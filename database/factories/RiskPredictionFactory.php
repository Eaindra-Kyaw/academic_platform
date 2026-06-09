<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiskPrediction>
 */
class RiskPredictionFactory extends Factory
{
    protected $model = \App\Models\RiskPrediction::class;

    public function definition(): array
    {
        $riskScore = $this->faker->numberBetween(0, 100);
        $riskLevel = $riskScore < 40 ? 'low_risk' : ($riskScore < 70 ? 'medium_risk' : 'high_risk');

        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'consecutive_absences' => $this->faker->numberBetween(0, 5),
            'attendance_trend' => $this->faker->randomElement(['improving', 'stable', 'slight_decline', 'moderate_decline', 'severe_decline']),
            'attendance_risk_points' => $this->faker->numberBetween(0, 100),
            'roll_call_risk_points' => $this->faker->numberBetween(0, 100),
            'absence_risk_points' => $this->faker->numberBetween(0, 100),
            'trend_risk_points' => $this->faker->numberBetween(0, 100),
            'risk_explanation' => $this->faker->paragraph(),
            'prediction_date' => $this->faker->date(),
        ];
    }
}
