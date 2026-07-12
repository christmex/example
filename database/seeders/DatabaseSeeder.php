<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Investor::factory()->for($user)->create([
            'name' => $user->name,
            'available_balance' => 100000,
        ]);

        $this->call(LoanSeeder::class);
    }
}
