<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    protected $model = \App\Models\AttendanceSession::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'lecturer_id' => User::factory(),
            'session_date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'qr_code' => $this->faker->optional()->text(),
            'qr_expiry' => $this->faker->optional()->dateTime(),
            'session_token' => $this->faker->unique()->uuid(),
            'is_active' => true,
            'is_locked' => false,
            'total_students' => $this->faker->numberBetween(20, 100),
            'present_count' => $this->faker->numberBetween(15, 95),
            'absent_count' => $this->faker->numberBetween(0, 30),
            'late_count' => $this->faker->numberBetween(0, 10),
        ];
    }
}
