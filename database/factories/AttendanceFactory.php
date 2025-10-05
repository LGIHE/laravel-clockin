<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inTime = fake()->dateTimeBetween('-1 month', 'now');
        $outTime = fake()->optional()->dateTimeBetween($inTime, 'now');
        $worked = $outTime ? $outTime->getTimestamp() - $inTime->getTimestamp() : 0;

        return [
            'id' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'in_time' => $inTime,
            'in_message' => fake()->optional()->sentence(),
            'out_time' => $outTime,
            'out_message' => fake()->optional()->sentence(),
            'worked' => $worked,
        ];
    }
}
