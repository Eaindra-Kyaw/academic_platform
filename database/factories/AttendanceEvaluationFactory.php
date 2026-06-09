<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceEvaluation>
 */
class AttendanceEvaluationFactory extends Factory
{
    protected $model = \App\Models\AttendanceEvaluation::class;

    public function definition(): array
    {
        $attended = $this->faker->numberBetween(0, 20);
        $total = $this->faker->numberBetween($attended, 20);
        $percentage = $total > 0 ? ($attended / $total) * 100 : 0;

        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'total_sessions' => $total,
            'attended_sessions' => $attended,
            'attendance_percentage' => $percentage,
            'roll_call_mark' => $this->faker->randomFloat(1, 0, 10),
            'eligibility_status' => $percentage >= 75 ? 'eligible' : ($percentage >= 60 ? 'warning' : 'not_eligible'),
            'consecutive_absences' => $this->faker->numberBetween(0, 5),
            'longest_absence_streak' => $this->faker->numberBetween(0, 8),
            'attendance_trend' => $this->faker->randomElement(['improving', 'stable', 'slight_decline', 'moderate_decline', 'severe_decline']),
            'sessions_needed' => max(0, ceil((75 - $percentage) / 100 * $total)),
            'evaluation_week' => $this->faker->numberBetween(1, 16),
        ];
    }
}
