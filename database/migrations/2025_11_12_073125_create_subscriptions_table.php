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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('TRY');
            $table->enum('interval', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->integer('interval_count')->default(1);
            $table->date('start_date');
            $table->date('next_billing_date');
            $table->date('end_date')->nullable();
            $table->string('color')->default('#8b5cf6');
            $table->string('icon')->nullable();
            $table->boolean('auto_pay')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
