<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InvestorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property int $user_id
 * @property string $name
 * @property numeric-string $available_balance
 */
#[Fillable(['user_id', 'name', 'available_balance'])]
class Investor extends Model
{
    /** @use HasFactory<InvestorFactory> */
    use HasFactory, HasUlids;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_balance' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<LoanInvestment, $this>
     */
    public function investments(): HasMany
    {
        return $this->hasMany(LoanInvestment::class);
    }
}
