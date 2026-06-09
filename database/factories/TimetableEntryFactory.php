<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimetableEntry>
 */
class TimetableEntryFactory extends Factory
{
    protected $model = \App\Models\TimetableEntry::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'lecturer_id' => User::factory(),
            'day_of_week' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'room' => $this->faker->bothify('Room ###'),
            'semester' => $this->faker->numberBetween(1, 8),
            'academic_year' => $this->faker->year(),
            'is_active' => true,
        ];
    }
}
