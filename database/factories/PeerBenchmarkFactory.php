<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeerBenchmark>
 */
class PeerBenchmarkFactory extends Factory
{
    protected $model = \App\Models\PeerBenchmark::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'student_attendance' => $this->faker->numberBetween(0, 100),
            'course_avg_attendance' => $this->faker->numberBetween(50, 90),
            'department_avg_attendance' => $this->faker->numberBetween(55, 85),
            'university_avg_attendance' => $this->faker->numberBetween(60, 80),
            'attendance_rank' => $this->faker->numberBetween(1, 100),
            'total_students_in_course' => $this->faker->numberBetween(30, 150),
            'student_health_score' => $this->faker->numberBetween(0, 100),
            'course_avg_health_score' => $this->faker->numberBetween(50, 85),
            'benchmark_date' => $this->faker->date(),
        ];
    }
}
