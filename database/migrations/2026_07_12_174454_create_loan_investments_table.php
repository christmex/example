<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_investments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('investor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_invested', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_investments');
    }
};
