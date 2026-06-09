<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AttendanceSession;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    protected $model = \App\Models\AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'session_id' => AttendanceSession::factory(),
            'student_id' => User::factory(),
            'status' => $this->faker->randomElement(['present', 'absent', 'late']),
            'scan_time' => $this->faker->optional()->dateTime(),
            'latitude' => $this->faker->optional()->latitude(),
            'longitude' => $this->faker->optional()->longitude(),
            'marked_by' => User::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
