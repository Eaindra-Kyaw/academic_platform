<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = \App\Models\Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'course_id' => Course::factory(),
            'enrollment_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'dropped']),
            'attendance_percentage' => $this->faker->numberBetween(0, 100),
            'roll_call_mark' => $this->faker->randomFloat(1, 0, 10),
            'eligibility_status' => $this->faker->randomElement(['eligible', 'warning', 'not_eligible']),
            'approved_at' => $this->faker->optional()->dateTime(),
            'dropped_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
