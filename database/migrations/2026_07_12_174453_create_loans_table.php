<?php

declare(strict_types=1);

use App\Enums\LoanStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('business_name');
            $table->decimal('amount_requested', 12, 2);
            $table->decimal('amount_funded', 12, 2)->default(0);
            $table->string('status')->default(LoanStatus::Open->value)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
