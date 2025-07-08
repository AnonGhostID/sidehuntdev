<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\Users;
use App\Services\TarikSaldoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayoutController extends Controller
{
    protected $xenditService;

    public function __construct(TarikSaldoService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Store a new payout request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'payment_type' => 'required|string|in:bank,ewallet',
            'bank_code' => 'required_if:payment_type,bank|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ], [
            'amount.min' => 'Minimum penarikan adalah Rp 50.000',
            'amount.required' => 'Jumlah penarikan harus diisi',
            'payment_type.required' => 'Jenis pembayaran harus dipilih',
            'payment_type.in' => 'Jenis pembayaran tidak valid',
            'bank_code.required_if' => 'Kode bank harus dipilih untuk pembayaran bank',
            'account_number.required' => 'Nomor rekening/e-wallet harus diisi',
            'account_name.required' => 'Nama pemilik rekening/e-wallet harus diisi',
        ]);

        $user = session('account');
        $userId = $user['id'];
        $amount = $request->amount;

        // Get fresh user data
        $userModel = Users::find($userId);
        
        if (!$userModel) {
            return back()->with('error', 'User tidak ditemukan');
        }

        // Validate sufficient balance
        if ($userModel->dompet < $amount) {
            return back()->with('error', 'Saldo tidak mencukupi. Saldo Anda: Rp ' . number_format($userModel->dompet, 0, ',', '.'));
        }

        try {
            return DB::transaction(function () use ($request, $userModel, $amount) {
                // Create payout record
                $payout = FinancialTransaction::create([
                    'user_id' => $userModel->id,
                    'type' => 'payout',
                    'amount' => $amount,
                    'payment_type' => $request->payment_type,
                    'bank_code' => $request->bank_code,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                    'status' => 'pending',
                    'xendit_reference_id' => 'payout_' . Str::random(32),
                ]);

                // Update payout status to processing
                $payout->update(['status' => 'processing']);

                // Process disbursement through Xendit
                $disbursementResult = $this->xenditService->createDisbursement($payout);

                if ($disbursementResult['success']) {
                    // Update payout with Xendit details
                    $payout->update([
                        'status' => 'completed',
                        'xendit_disbursement_id' => $disbursementResult['disbursement_id'],
                        'processed_at' => now(),
                    ]);

                    // Deduct from user's wallet
                    $userModel->decrement('dompet', $amount);

                    Log::info('Payout processed successfully', [
                        'payout_id' => $payout->id,
                        'user_id' => $userModel->id,
                        'amount' => $amount,
                        'xendit_id' => $disbursementResult['disbursement_id']
                    ]);

                    return redirect()->route('manajemen.tarik_saldo')->with('success', 'Penarikan berhasil diproses! Dana akan dikirim ke rekening Anda dalam 1-2 hari kerja.');

                } else {
                    // Update payout status to failed
                    $payout->update([
                        'status' => 'failed',
                        'failure_reason' => $disbursementResult['error']
                    ]);

                    Log::error('Payout failed', [
                        'payout_id' => $payout->id,
                        'error' => $disbursementResult['error']
                    ]);

                    return back()->with('error', 'Penarikan gagal: ' . $disbursementResult['error']);
                }
            });

        } catch (\Exception $e) {
            Log::error('Payout transaction failed', [
                'user_id' => $userModel->id,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi nanti.');
        }
    }

    /**
     * Get user's payout history
     */
    public function history()
    {
        $user = session('account');
        $payouts = FinancialTransaction::where('user_id', $user['id'])
            ->payouts()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'payouts' => $payouts->items(),
            'pagination' => [
                'current_page' => $payouts->currentPage(),
                'last_page' => $payouts->lastPage(),
                'total' => $payouts->total()
            ]
        ]);
    }

    /**
     * Check user's available balance
     */
    public function checkBalance()
    {
        $user = session('account');
        $userModel = Users::find($user['id']);

        return response()->json([
            'balance' => $userModel->dompet,
            'formatted_balance' => 'Rp ' . number_format($userModel->dompet, 0, ',', '.'),
            'max_withdrawal' => $userModel->dompet,
            'min_withdrawal' => 50000
        ]);
    }
}
