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
        // Drop the old tables after data has been migrated
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payouts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old tables structure (without data)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->string('checkout_link');
            $table->string('external_id');
            $table->string('status')->default('pending');
            $table->string('method')->nullable();
            $table->timestamps();
            
            $table->index(['created_at', 'status']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_type', ['bank', 'ewallet'])->default('bank');
            $table->string('bank_code', 50);
            $table->string('account_number', 50);
            $table->string('account_name', 100);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('xendit_disbursement_id')->nullable();
            $table->string('xendit_reference_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['created_at']);
            $table->index(['xendit_disbursement_id']);
            $table->index(['payment_type']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
