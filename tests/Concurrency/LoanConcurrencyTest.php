<?php

declare(strict_types=1);

use App\Actions\Loans\InvestInLoanAction;
use App\Models\Investor;
use App\Models\Loan;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;

test('parallel investments can never over-fund a loan', function () {
    $loan = Loan::factory()->create([
        'amount_requested' => 1000,
        'amount_funded' => 0,
    ]);
    $loanId = $loan->getKey();

    // 20 investors, each with a sufficient balance, each firing a 100 investment at the
    // same loan at the same time: 2000 attempted into a loan that only needs 1000.
    $investorIds = collect(range(1, 20))
        ->map(fn () => Investor::factory()->create(['available_balance' => 1000])->getKey())
        ->all();

    Concurrency::driver('fork')->run(
        collect($investorIds)
            ->map(fn (string $investorId) => function () use ($loanId, $investorId): void {
                // Each forked process needs its own database connection.
                DB::reconnect();

                try {
                    app(InvestInLoanAction::class)->handle(
                        Loan::findOrFail($loanId),
                        Investor::findOrFail($investorId),
                        '100',
                    );
                } catch (Throwable) {
                    // Rejected once the loan is fully funded — the row lock holding
                    // under genuine, simultaneous contention.
                }
            })
            ->all(),
    );

    $loan->refresh();

    // Even with 20 real processes racing, exactly 1000 is funded — never 1000.01+.
    expect($loan->amount_funded)->toBe('1000.00');
    expect($loan->investments()->count())->toBe(10);
    expect($loan->investments()->sum('amount_invested'))->toBe('1000.00');
})->skip(
    fn () => DB::connection()->getDriverName() !== 'mysql' || ! function_exists('pcntl_fork'),
    'Real parallel row-locking requires MySQL and the pcntl extension.',
);
