<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoanStatus;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $business_name
 * @property numeric-string $amount_requested
 * @property numeric-string $amount_funded
 * @property LoanStatus $status
 */
#[Fillable(['business_name', 'amount_requested', 'amount_funded', 'status'])]
class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory, HasUlids;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'amount_funded' => 0,
        'status' => LoanStatus::Open->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_requested' => 'decimal:2',
            'amount_funded' => 'decimal:2',
            'status' => LoanStatus::class,
        ];
    }

    /**
     * @return HasMany<LoanInvestment, $this>
     */
    public function investments(): HasMany
    {
        return $this->hasMany(LoanInvestment::class);
    }

    public function isFullyFunded(): bool
    {
        return $this->status === LoanStatus::FullyFunded;
    }
}
