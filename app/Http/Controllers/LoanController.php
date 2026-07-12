<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Loan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoanController extends Controller
{
    public function show(Request $request, Loan $loan): Response
    {
        $investor = $request->user()->investor;

        return Inertia::render('Loans/Show', [
            'loan' => fn () => [
                'id' => $loan->id,
                'business_name' => $loan->business_name,
                'amount_requested' => $loan->amount_requested,
                'amount_funded' => $loan->amount_funded,
                'is_fully_funded' => $loan->status === LoanStatus::FullyFunded,
            ],
            'investor' => fn () => $investor === null ? null : [
                'id' => $investor->id,
                'name' => $investor->name,
                'available_balance' => $investor->available_balance,
            ],
        ]);
    }
}
