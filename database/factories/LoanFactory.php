<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_name' => fake()->company(),
            'amount_requested' => fake()->numberBetween(10_000, 500_000),
            'amount_funded' => 0,
            'status' => LoanStatus::Open,
        ];
    }
}
