<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = \App\Models\Course::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'lecturer_id' => User::factory(),
            'course_code' => $this->faker->unique()->bothify('CS###'),
            'course_name' => $this->faker->sentence(3),
            'credits' => $this->faker->numberBetween(2, 4),
            'semester' => $this->faker->numberBetween(1, 8),
            'academic_year' => $this->faker->year(),
            'schedule_day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'schedule_time' => $this->faker->time(),
            'schedule_end_time' => $this->faker->time(),
            'room' => $this->faker->bothify('Room ###'),
            'is_active' => true,
        ];
    }
}
