<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'notifiable_id' => Str::uuid()->toString(),
            'type' => $this->faker->randomElement(['leave_approved', 'leave_rejected', 'system_notification']),
            'notifiable_type' => 'App\\Models\\User',
            'data' => json_encode([
                'title' => $this->faker->sentence(),
                'message' => $this->faker->paragraph(),
            ]),
            'read_at' => null,
        ];
    }

    /**
     * Indicate that the notification has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
