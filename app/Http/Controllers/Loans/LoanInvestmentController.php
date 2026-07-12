<?php

declare(strict_types=1);

namespace App\Http\Controllers\Loans;

use App\Actions\Loans\InvestInLoanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loans\StoreLoanInvestmentRequest;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class LoanInvestmentController extends Controller
{
    public function store(StoreLoanInvestmentRequest $request, Loan $loan, InvestInLoanAction $investInLoan): RedirectResponse
    {
        $investor = $request->user()->investor;

        if ($investor === null) {
            throw ValidationException::withMessages([
                'amount' => __('You do not have an investor account.'),
            ]);
        }

        $investInLoan->handle($loan, $investor, (string) $request->validated('amount'));

        return back();
    }
}
