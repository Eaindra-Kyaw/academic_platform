<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatbotLog>
 */
class ChatbotLogFactory extends Factory
{
    protected $model = \App\Models\ChatbotLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_message' => $this->faker->sentence(),
            'detected_intent' => $this->faker->randomElement(['attendance', 'eligibility', 'risk', 'timetable']),
            'bot_response' => $this->faker->paragraph(),
            'response_time_ms' => $this->faker->numberBetween(50, 500),
            'was_helpful' => $this->faker->optional()->boolean(),
        ];
    }
}
