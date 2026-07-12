<?php

declare(strict_types=1);

namespace App\Actions\Loans;

use App\Enums\LoanStatus;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\LoanInvestment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvestInLoanAction
{
    /**
     * Fund a loan from an investor's balance, safely under high concurrency.
     *
     * The loan and investor rows are locked FOR UPDATE inside a single database
     * transaction (in a consistent order to avoid deadlocks) so two investors can
     * never over-fund a loan or overspend a balance in the same millisecond.
     * Money is compared and accumulated with bcmath to avoid float precision bugs.
     *
     * @throws ValidationException when the loan is fully funded, the amount exceeds
     *                             the remaining need, or the balance is insufficient.
     */
    public function handle(Loan $loan, Investor $investor, string $amount): LoanInvestment
    {
        if (! is_numeric($amount)) {
            throw ValidationException::withMessages([
                'amount' => __('The amount must be a valid number.'),
            ]);
        }

        // is_numeric() also accepts scientific notation ("1e3") and surrounding
        // whitespace, which bcmath rejects with a ValueError. Reject those here so a
        // direct caller gets a clean validation error instead of a 500.
        if (preg_match('/[^0-9.]/', $amount) === 1) {
            throw ValidationException::withMessages([
                'amount' => __('The amount must be a valid number.'),
            ]);
        }

        return DB::transaction(function () use ($loan, $investor, $amount): LoanInvestment {
            $lockedLoan = Loan::query()->whereKey($loan->getKey())->lockForUpdate()->firstOrFail();
            $lockedInvestor = Investor::query()->whereKey($investor->getKey())->lockForUpdate()->firstOrFail();

            $remaining = bcsub($lockedLoan->amount_requested, $lockedLoan->amount_funded, 2);

            if (bccomp($remaining, '0', 2) <= 0) {
                throw ValidationException::withMessages([
                    'amount' => __('This loan is already fully funded.'),
                ]);
            }

            if (bccomp($amount, $remaining, 2) > 0) {
                throw ValidationException::withMessages([
                    'amount' => __('The amount exceeds the remaining funding needed (:remaining).', ['remaining' => $remaining]),
                ]);
            }

            if (bccomp($amount, $lockedInvestor->available_balance, 2) > 0) {
                throw ValidationException::withMessages([
                    'amount' => __('Your available balance is not enough for this amount.'),
                ]);
            }

            $lockedLoan->amount_funded = bcadd($lockedLoan->amount_funded, $amount, 2);

            if (bccomp($lockedLoan->amount_funded, $lockedLoan->amount_requested, 2) >= 0) {
                $lockedLoan->status = LoanStatus::FullyFunded;
            }

            $lockedLoan->save();

            $lockedInvestor->available_balance = bcsub($lockedInvestor->available_balance, $amount, 2);
            $lockedInvestor->save();

            return $lockedLoan->investments()->create([
                'investor_id' => $lockedInvestor->getKey(),
                'amount_invested' => $amount,
            ]);
        });
    }
}
