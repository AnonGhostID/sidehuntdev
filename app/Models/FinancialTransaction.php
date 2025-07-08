<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type', // 'payment' or 'payout'
        'status',
        // Payment-specific
        'checkout_link',
        'external_id',
        'method',
        'description',
        // Payout-specific
        'payment_type',
        'bank_code',
        'account_number',
        'account_name',
        'xendit_disbursement_id',
        'xendit_reference_id',
        'failure_reason',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class);
    }

    /**
     * Check if transaction is a payment (top-up)
     */
    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    /**
     * Check if transaction is a payout (withdrawal)
     */
    public function isPayout(): bool
    {
        return $this->type === 'payout';
    }

    /**
     * Check if transaction is completed/successful
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if transaction failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if transaction is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if payment is paid (legacy method for compatibility)
     */
    public function isPaid(): bool
    {
        return $this->isPayment() && $this->isCompleted();
    }

    /**
     * Check if payment is expired (legacy method for compatibility)
     */
    public function isExpiredPayment(): bool
    {
        // Check if payment is older than 24 hours and still pending
        return $this->isPayment() && $this->isPending() && $this->created_at->diffInHours(now()) > 24;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'expired' => 'gray',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'completed' => 'Berhasil',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'payment' => 'Top Up',
            'payout' => 'Penarikan',
            default => 'Transaksi'
        };
    }

    /**
     * Scope for payments only
     */
    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    /**
     * Scope for payouts only
     */
    public function scopePayouts($query)
    {
        return $query->where('type', 'payout');
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
