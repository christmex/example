<?php

declare(strict_types=1);

use App\Http\Controllers\LoanController;
use App\Http\Controllers\Loans\LoanInvestmentController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('loans/{loan}/investments', [LoanInvestmentController::class, 'store'])
        ->name('loans.investments.store');
});

require __DIR__.'/settings.php';
