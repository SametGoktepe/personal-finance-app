<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense'])->default('expense');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('TRY');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_interval')->nullable(); // daily, weekly, monthly, yearly
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
