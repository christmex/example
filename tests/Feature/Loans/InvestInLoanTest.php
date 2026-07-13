<?php

declare(strict_types=1);

use App\Actions\Loans\InvestInLoanAction;
use App\Enums\LoanStatus;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

test('an investor can fund a loan and their balance is debited', function () {
    $user = User::factory()->create();
    $investor = Investor::factory()->for($user)->create(['available_balance' => 10000]);
    $loan = Loan::factory()->create(['amount_requested' => 5000, 'amount_funded' => 0]);

    $this->actingAs($user)
        ->post(route('loans.investments.store', $loan), ['amount' => 2000])
        ->assertRedirect();

    expect($loan->fresh()->amount_funded)->toBe('2000.00');
    expect($investor->fresh()->available_balance)->toBe('8000.00');
    expect($loan->investments()->count())->toBe(1);
});

test('an investment greater than the remaining need is rejected', function () {
    $user = User::factory()->create();
    Investor::factory()->for($user)->create(['available_balance' => 100000]);
    $loan = Loan::factory()->create(['amount_requested' => 5000, 'amount_funded' => 4000]);

    $this->actingAs($user)
        ->post(route('loans.investments.store', $loan), ['amount' => 2000])
        ->assertSessionHasErrors('amount');

    expect($loan->fresh()->amount_funded)->toBe('4000.00');
});

test('an investment greater than the investor balance is rejected', function () {
    $user = User::factory()->create();
    Investor::factory()->for($user)->create(['available_balance' => 1000]);
    $loan = Loan::factory()->create(['amount_requested' => 50000, 'amount_funded' => 0]);

    $this->actingAs($user)
        ->post(route('loans.investments.store', $loan), ['amount' => 2000])
        ->assertSessionHasErrors('amount');

    expect($loan->fresh()->amount_funded)->toBe('0.00');
});

test('a loan is marked fully funded once the remaining need is met', function () {
    $user = User::factory()->create();
    Investor::factory()->for($user)->create(['available_balance' => 10000]);
    $loan = Loan::factory()->create(['amount_requested' => 5000, 'amount_funded' => 4000]);

    $this->actingAs($user)->post(route('loans.investments.store', $loan), ['amount' => 1000]);

    $loan->refresh();
    expect($loan->amount_funded)->toBe('5000.00');
    expect($loan->status)->toBe(LoanStatus::FullyFunded);
});

test('funding requires authentication', function () {
    $loan = Loan::factory()->create();

    $this->post(route('loans.investments.store', $loan), ['amount' => 100])
        ->assertRedirect(route('login'));
});

test('the loan funding page renders for an investor', function () {
    $user = User::factory()->create();
    Investor::factory()->for($user)->create();
    $loan = Loan::factory()->create();

    $this->actingAs($user)
        ->get(route('loans.show', $loan))
        ->assertOk();
});

test('the loans index page renders for an investor', function () {
    $user = User::factory()->create();
    Investor::factory()->for($user)->create();
    Loan::factory()->count(3)->create();

    $this->actingAs($user)
        ->get(route('loans.index'))
        ->assertOk();
});

test('a loan is never funded beyond the amount requested', function () {
    $loan = Loan::factory()->create(['amount_requested' => 1000, 'amount_funded' => 0]);
    $investInLoan = app(InvestInLoanAction::class);

    // 20 investors each try to invest 100 (2000 total) into a loan that needs only 1000.
    $succeeded = 0;

    foreach (range(1, 20) as $attempt) {
        $investor = Investor::factory()->create(['available_balance' => 1000]);

        try {
            $investInLoan->handle($loan, $investor, '100');
            $succeeded++;
        } catch (ValidationException) {
            // Rejected once the loan is fully funded — the invariant holding.
        }
    }

    $loan->refresh();

    expect($loan->amount_funded)->toBe('1000.00');
    expect($loan->status)->toBe(LoanStatus::FullyFunded);
    expect($succeeded)->toBe(10);
    expect($loan->investments()->count())->toBe(10);
});

test('the funding action reads the loan and investor rows for update', function () {
    $investor = Investor::factory()->create(['available_balance' => 5000]);
    $loan = Loan::factory()->create(['amount_requested' => 5000, 'amount_funded' => 0]);

    $lockingSelects = [];
    DB::listen(function ($query) use (&$lockingSelects) {
        if (str_contains(strtolower($query->sql), 'for update')) {
            $lockingSelects[] = $query->sql;
        }
    });

    app(InvestInLoanAction::class)->handle($loan, $investor, '1000');

    // Both the loan and the investor row must be locked FOR UPDATE; if lockForUpdate()
    // is ever removed, this count drops and the test fails.
    expect($lockingSelects)->toHaveCount(2);
});

test('funding without an investor account is rejected', function () {
    $user = User::factory()->create();
    $loan = Loan::factory()->create();

    $this->actingAs($user)
        ->post(route('loans.investments.store', $loan), ['amount' => 100])
        ->assertSessionHasErrors('amount');

    expect($loan->fresh()->amount_funded)->toBe('0.00');
});
