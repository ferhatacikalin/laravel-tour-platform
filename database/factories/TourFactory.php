<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = fake()->dateTimeBetween($startDate->format('Y-m-d H:i:s'), $startDate->modify('+2 weeks')->format('Y-m-d H:i:s'));
        
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->city() . ', ' . fake()->country(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => fake()->randomFloat(2, 100, 5000),
            'user_id' => User::factory()
        ];
    }

    /**
     * Define a state for past tours.
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-1 year', '-2 months');
            $endDate = fake()->dateTimeBetween(
                $startDate->format('Y-m-d H:i:s'),
                $startDate->modify('+2 weeks')->format('Y-m-d H:i:s')
            );
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Define a state for upcoming tours.
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('+1 week', '+6 months');
            $endDate = fake()->dateTimeBetween(
                $startDate->format('Y-m-d H:i:s'),
                $startDate->modify('+2 weeks')->format('Y-m-d H:i:s')
            );
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }
}
