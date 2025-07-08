<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class TopUpController extends Controller
{
    var $apiInstance = null;

    public function __construct()
    {
        // $this->middleware('auth');
        Configuration::setXenditKey('xnd_development_7Qgujm27QHHqpc15olW28d1yBzncI1f1KLHSGNMwGeRug2K6doSB426KYqvgEa');
        $this->apiInstance = new InvoiceApi();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominal' => 'nullable|numeric|min:20000',
            // 'custom_amount' => 'nullable|string',
            'custom_amount_raw' => 'nullable|numeric|min:20000'
        ]);

        $user = session('account');
        // dd($request);
        
        $amount = !empty($request->nominal) ? $request->nominal : (!empty($request->custom_amount) ? $request->custom_amount: null);
        // dd($amount);

        if (!$amount || $amount < 20000) {
            return back()->with('error', 'Minimum top up adalah Rp 20.000');
        }

        if ($amount > 10000000) {
            return back()->with('error', 'Maximum top up adalah Rp 10.000.000');
        }

        try {
            $external_id = 'topup_' . Str::random(32);
            
            // Set expiry date to 24 hours from now
            // $expiryDate = now()->addHours(24)->toISOString();
            // dd($user);
            
            $createInvoiceRequest = new \Xendit\Invoice\CreateInvoiceRequest([
                'external_id' => $external_id,
                'description' => "Top Up Saldo - Rp " . number_format($amount, 0, ',', '.') . " - " . $user->nama,
                'amount' => $amount,
                'payer_email' => $user->email,
                'invoice_duration' => 120, 
                'success_redirect_url' => route('manajemen.topup.payment', ['external_id' => $external_id]),
                'failure_redirect_url' => route('manajemen.topup.payment', ['external_id' => $external_id]),
                
            ]);
            
            
            $result = $this->apiInstance->createInvoice($createInvoiceRequest);
            
            // Save to database
            $payment = FinancialTransaction::create([
                'user_id' => $user->id,
                'type' => 'payment',
                'status' => $result->getStatus(),
                'checkout_link' => $result['invoice_url'],
                'external_id' => $external_id,
                'amount' => $amount,
                'description' => "Top Up Saldo - Rp " . number_format($amount, 0, ',', '.') . " - " . $user->nama,
            ]);
            
            
            return redirect()
                    ->route('manajemen.topup.payment', ['external_id' => $external_id])
                    ->with('invoice_duration', 120);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }
    
    public function payment($external_id)
    {
        $query = FinancialTransaction::where('external_id', $external_id);
        
        if (Auth::user()->isAdmin != 1) {
            $query->where('user_id', Auth::id());
        }
        
        $payment = $query->first();

        if (!$payment) {
            return redirect()->route('manajemen.topUp')
                           ->with('error', 'Invoice Tidak Ditemukan');
        }

        $this->updatePaymentStatus($payment);

        $payment->refresh();

        switch ($payment->status) {
            case 'pending':
                return view('manajemen.keuangan.topup', compact('payment'))->with(['view_type' => 'waiting']);
            
            case 'completed':
                return view('manajemen.keuangan.topup', compact('payment'))->with(['view_type' => 'success']);
            
            case 'failed':
            case 'expired':
            case 'cancelled':
                return view('manajemen.keuangan.topup', compact('payment'))->with(['view_type' => 'failed']);
            
            default:
                return redirect()->route('manajemen.topUp')
                               ->with('error', 'Status pembayaran tidak valid.');
        }
    }

    public function checkStatus(Request $request)
    {
        $external_id = $request->external_id;
        $query = FinancialTransaction::where('external_id', $external_id);
        
        if (Auth::user()->isAdmin != 1) {
            $query->where('user_id', Auth::id());
        }
        
        $payment = $query->first();

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice Tidak Ditemukan'
            ], 404);
        }

        $payment->refresh();

        // Fitur anti dobel input
        try {
            $result = $this->apiInstance->getInvoices(null, $external_id);
            
            if (!empty($result)) {
                $xenditStatus = strtolower($result[0]['status']);
                
                return DB::transaction(function () use ($payment, $xenditStatus, $result) {

                    $lockedPayment = FinancialTransaction::where('id', $payment->id)->lockForUpdate()->first();
                    $previousStatus = $lockedPayment->status;
                    
                    $paymentMethod = null;
                    
                    if (isset($result[0]['payment_method'])) {
                        $paymentMethod = $result[0]['payment_method'];
                    } elseif (isset($result[0]['payment_channel'])) {
                        $paymentMethod = $result[0]['payment_channel'];
                    } elseif (isset($result[0]['payment_destination'])) {
                        $paymentMethod = $result[0]['payment_destination'];
                    } elseif (isset($result[0]['bank_code'])) {
                        $paymentMethod = $result[0]['bank_code'];
                    } elseif (isset($result[0]['payment_details']['payment_method'])) {
                        $paymentMethod = $result[0]['payment_details']['payment_method'];
                    }
                    
                    $updateData = ['status' => $this->mapXenditStatus($xenditStatus)];
                    if ($paymentMethod) {
                        $updateData['method'] = $paymentMethod;
                    }
                    
                    $lockedPayment->update($updateData);
                    
                    if (($xenditStatus === 'paid' || $xenditStatus === 'settled') && 
                        ($previousStatus !== 'completed')) {
                        
                        $user = $lockedPayment->user;
                        $user->increment('dompet', $lockedPayment->amount);
                        
                        // Update session with fresh user data
                        $freshUser = $user->fresh();
                        session(['account' => $freshUser]);
                        
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Pembayaran berhasil! Saldo Anda telah ditambahkan.',
                            'new_balance' => $freshUser->dompet
                        ]);
                    }
                    
                    return response()->json([
                        'status' => $xenditStatus,
                        'message' => $this->getStatusMessage($xenditStatus)
                    ]);
                });
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengecek status pembayaran: ' . $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'status' => $payment->status,
            'message' => $this->getStatusMessage($payment->status)
        ]);
    }

    public function cancel(Request $request, $external_id)
    {
        $query = FinancialTransaction::where('external_id', $external_id)
                       ->where('status', 'pending');
        
        if (Auth::user()->isAdmin != 1) {
            $query->where('user_id', Auth::id());
        }
        
        $payment = $query->first();

        if (!$payment) {
            return redirect()->route('manajemen.topUp')
                           ->with('error', 'Invoice Tidak Ditemukan');
        }

        try {
            $result = $this->apiInstance->getInvoices(null, $external_id);
            
            if (!empty($result)) {
                $invoiceId = $result[0]['id'];
                $expiredInvoice = $this->apiInstance->expireInvoice($invoiceId);
                $payment->update(['status' => 'expired']);
                
                return redirect()->route('manajemen.topUp')->with('error', 'Pembayaran telah dibatalkan dan invoice telah dihapus!.');
            } else {
                $payment->update(['status' => 'cancelled']);
                return redirect()->route('manajemen.topUp')->with('error', 'Pembayaran telah dibatalkan.');
            }
        } catch (\Exception $e) {
            Log::error('Gagal hapus invoice Xendit: ' . $e->getMessage());
            $payment->update(['status' => 'cancelled']);
            return redirect()->route('manajemen.topUp')->with('error', 'Pembayaran telah dibatalkan (offline).');
        }
    }

    private function updatePaymentStatus(FinancialTransaction $payment)
    {
        try {
            $result = $this->apiInstance->getInvoices(null, $payment->external_id);
            
            if (!empty($result)) {
                $xenditStatus = strtolower($result[0]['status']);
                $previousStatus = $payment->status;
                
                // Log the full Xendit response to see available fields
                // Log::info('Xendit Invoice Response', [
                //     'external_id' => $payment->external_id,
                //     'response' => $result[0]
                // ]);
                
                $paymentMethod = null;
                
                if (isset($result[0]['payment_method'])) {
                    $paymentMethod = $result[0]['payment_method'];
                } elseif (isset($result[0]['payment_channel'])) {
                    $paymentMethod = $result[0]['payment_channel'];
                } elseif (isset($result[0]['payment_destination'])) {
                    $paymentMethod = $result[0]['payment_destination'];
                } elseif (isset($result[0]['bank_code'])) {
                    $paymentMethod = $result[0]['bank_code'];
                } elseif (isset($result[0]['payment_details']['payment_method'])) {
                    $paymentMethod = $result[0]['payment_details']['payment_method'];
                }
                
                $updateData = ['status' => $this->mapXenditStatus($xenditStatus)];
                if ($paymentMethod) {
                    $updateData['method'] = $paymentMethod;
                }
                
                $payment->update($updateData);
                
                if (($xenditStatus === 'paid' || $xenditStatus === 'settled') && 
                    ($previousStatus !== 'completed')) {
                    
                    $user = $payment->user;
                    $user->increment('dompet', $payment->amount);
                    
                    // Update session with fresh user data if this user is currently logged in
                    if (session('account') && session('account')->id == $user->id) {
                        $freshUser = $user->fresh();
                        session(['account' => $freshUser]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't throw
            Log::error('Failed to update payment status: ' . $e->getMessage());
        }
    }

    private function getStatusMessage($status)
    {
        return match($status) {
            'pending' => 'Menunggu pembayaran...',
            'paid', 'settled' => 'Pembayaran berhasil!',
            'completed' => 'Pembayaran berhasil!',
            'expired' => 'Pembayaran telah kedaluwarsa',
            'cancelled' => 'Pembayaran dibatalkan',
            default => 'Status tidak dikenal'
        };
    }

    /**
     * Map Xendit status to unified status
     */
    private function mapXenditStatus($xenditStatus): string
    {
        return match(strtolower($xenditStatus)) {
            'paid', 'settled' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
    }

    public function cleanupExpiredPayments()
    {
        $expiredPayments = FinancialTransaction::where('status', 'pending')
                                ->where('created_at', '<', now()->subHours(24))
                                ->get();

        foreach ($expiredPayments as $payment) {
            $payment->update(['status' => 'expired']);
        }

        return response()->json(['message' => 'Expired payments cleaned up']);
    }

    public function expireOnTimeout(Request $request)
    {
        $external_id = $request->external_id;
        $query = FinancialTransaction::where('external_id', $external_id)
                       ->where('status', 'pending');
        
        if (Auth::user()->isAdmin != 1) {
            $query->where('user_id', Auth::id());
        }
        
        $payment = $query->first();

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice Tidak Ditemukan'
            ], 404);
        }

        // Ambil data dr Xendit
        try {
            $result = $this->apiInstance->getInvoices(null, $external_id);
            
            if (!empty($result)) {
                $invoiceId = $result[0]['id'];
                $xenditStatus = strtolower($result[0]['status']);
                
                if ($xenditStatus === 'pending') {
                    $this->apiInstance->expireInvoice($invoiceId);
                    $payment->update(['status' => 'expired']);
                    
                    return response()->json([
                        'status' => 'expired',
                        'message' => 'Invoice telah kedaluwarsa dan dihapus dari Xendit.'
                    ]);
                } else {
                    $payment->update(['status' => $xenditStatus]);
                    
                    return response()->json([
                        'status' => $xenditStatus,
                        'message' => $this->getStatusMessage($xenditStatus)
                    ]);
                }
            } else {
                $payment->update(['status' => 'expired']);
                
                return response()->json([
                    'status' => 'expired',
                    'message' => 'Invoice tidak ditemukan di Xendit, ditandai sebagai kedaluwarsa.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Gagal expire invoice otomatis: ' . $e->getMessage());
            $payment->update(['status' => 'expired']);
            
            return response()->json([
                'status' => 'expired',
                'message' => 'Invoice telah kedaluwarsa (offline).'
            ]);
        }
    }

}
