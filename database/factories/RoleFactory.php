<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = \App\Models\Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Admin', 'Lecturer', 'Student']),
            'slug' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(5),
        ];
    }
}
