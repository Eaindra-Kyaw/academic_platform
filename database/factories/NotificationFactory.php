<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = \App\Models\Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'notification_type' => $this->faker->randomElement(['attendance_warning', 'class_reminder', 'qr_alert', 'announcement']),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'link' => $this->faker->optional()->url(),
            'is_read' => false,
            'sent_at' => $this->faker->dateTime(),
        ];
    }
}
