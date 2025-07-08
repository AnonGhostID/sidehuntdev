<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate payments data
        if (Schema::hasTable('payments')) {
            $payments = DB::table('payments')->get();
            
            foreach ($payments as $payment) {
                DB::table('financial_transactions')->insert([
                    'user_id' => $payment->user_id,
                    'amount' => $payment->amount,
                    'type' => 'payment',
                    'status' => $this->mapPaymentStatus($payment->status),
                    'checkout_link' => $payment->checkout_link,
                    'external_id' => $payment->external_id,
                    'method' => $payment->method,
                    'description' => $payment->description,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ]);
            }
        }
        
        // Migrate payouts data
        if (Schema::hasTable('payouts')) {
            $payouts = DB::table('payouts')->get();
            
            foreach ($payouts as $payout) {
                DB::table('financial_transactions')->insert([
                    'user_id' => $payout->user_id,
                    'amount' => $payout->amount,
                    'type' => 'payout',
                    'status' => $this->mapPayoutStatus($payout->status),
                    'payment_type' => $payout->payment_type,
                    'bank_code' => $payout->bank_code,
                    'account_number' => $payout->account_number,
                    'account_name' => $payout->account_name,
                    'xendit_disbursement_id' => $payout->xendit_disbursement_id,
                    'xendit_reference_id' => $payout->xendit_reference_id,
                    'failure_reason' => $payout->failure_reason,
                    'processed_at' => $payout->processed_at,
                    'created_at' => $payout->created_at,
                    'updated_at' => $payout->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is irreversible as we're merging data
        // In a real scenario, you might want to backup the original tables first
    }
    
    /**
     * Map payment status to unified status
     */
    private function mapPaymentStatus($status): string
    {
        return match(strtolower($status)) {
            'paid', 'settled' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
    }
    
    /**
     * Map payout status to unified status
     */
    private function mapPayoutStatus($status): string
    {
        return match(strtolower($status)) {
            'completed' => 'completed',
            'pending' => 'pending',
            'processing' => 'processing',
            'failed' => 'failed',
            default => 'pending'
        };
    }
};
