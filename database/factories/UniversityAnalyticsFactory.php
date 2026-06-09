<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UniversityAnalytics>
 */
class UniversityAnalyticsFactory extends Factory
{
    protected $model = \App\Models\UniversityAnalytics::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'total_students' => $this->faker->numberBetween(100, 500),
            'total_lecturers' => $this->faker->numberBetween(10, 50),
            'total_courses' => $this->faker->numberBetween(20, 60),
            'attendance_rate' => $this->faker->randomFloat(2, 60, 95),
            'students_at_risk' => $this->faker->numberBetween(10, 100),
            'eligibility_rate' => $this->faker->randomFloat(2, 70, 95),
            'avg_academic_health_score' => $this->faker->randomFloat(2, 50, 90),
            'active_sessions' => $this->faker->numberBetween(0, 10),
            'busiest_classroom' => $this->faker->bothify('Room ###'),
            'busiest_classroom_count' => $this->faker->numberBetween(10, 50),
            'weekly_engagement' => json_encode(['mon' => 85, 'tue' => 82, 'wed' => 88]),
            'analytics_date' => $this->faker->date(),
        ];
    }
}
