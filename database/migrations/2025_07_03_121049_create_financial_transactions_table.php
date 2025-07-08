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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['payment', 'payout']); // payment = top-up, payout = withdrawal
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'expired', 'cancelled'])->default('pending');
            
            // Payment-specific fields (for top-ups)
            $table->string('checkout_link')->nullable();
            $table->string('external_id')->nullable();
            $table->string('method')->nullable(); // payment method used
            $table->string('description')->nullable();
            
            // Payout-specific fields (for withdrawals)
            $table->enum('payment_type', ['bank', 'ewallet'])->nullable(); // bank or ewallet
            $table->string('bank_code', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->string('xendit_disbursement_id')->nullable();
            $table->string('xendit_reference_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['created_at']);
            $table->index(['external_id']);
            $table->index(['xendit_disbursement_id']);
            $table->index(['type', 'status']);
            
            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
