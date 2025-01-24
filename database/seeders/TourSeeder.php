<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some tour operators
        $tourOperators = User::factory(3)->create();

        foreach ($tourOperators as $operator) {
            // Create past tours
            Tour::factory(5)
                ->past()
                ->create(['user_id' => $operator->id]);

            // Create current/upcoming tours
            Tour::factory(10)
                ->upcoming()
                ->create(['user_id' => $operator->id]);

            // Create regular tours (mix of dates)
            Tour::factory(5)
                ->create(['user_id' => $operator->id]);
        }
    }
}
