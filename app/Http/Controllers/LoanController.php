<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Loans/Index', [
            'loans' => Loan::query()
                ->select(['id', 'business_name', 'amount_requested', 'amount_funded', 'status'])
                ->latest()
                ->get()
                ->map(fn (Loan $loan): array => [
                    'id' => $loan->id,
                    'business_name' => $loan->business_name,
                    'amount_requested' => $loan->amount_requested,
                    'amount_funded' => $loan->amount_funded,
                    'is_fully_funded' => $loan->isFullyFunded(),
                ]),
        ]);
    }

    public function show(Request $request, Loan $loan): Response
    {
        $investor = $request->user()->investor;

        return Inertia::render('Loans/Show', [
            'loan' => fn () => [
                'id' => $loan->id,
                'business_name' => $loan->business_name,
                'amount_requested' => $loan->amount_requested,
                'amount_funded' => $loan->amount_funded,
                'is_fully_funded' => $loan->isFullyFunded(),
            ],
            'investor' => fn () => $investor === null ? null : [
                'id' => $investor->id,
                'name' => $investor->name,
                'available_balance' => $investor->available_balance,
            ],
        ]);
    }
}
