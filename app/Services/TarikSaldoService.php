<?php

namespace App\Services;

use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Payout\PayoutApi;
use Xendit\Payout\CreatePayoutRequest;

class TarikSaldoService
{
    protected $payoutApi;

    public function __construct()
    {
        // Use the same Xendit key as TopUpController
        Configuration::setXenditKey('xnd_development_7Qgujm27QHHqpc15olW28d1yBzncI1f1KLHSGNMwGeRug2K6doSB426KYqvgEa');
        $this->payoutApi = new PayoutApi();
    }

    public function createDisbursement(FinancialTransaction $payout): array
    {
        try {
            // Debug: Log all values before creating request
            Log::info('Debug: Payout data before Xendit request', [
                'payout_id' => $payout->id,
                'reference_id' => $payout->xendit_reference_id,
                'payment_type' => $payout->payment_type,
                'channel_code' => $this->getChannelCode($payout),
                'account_holder_name' => $payout->account_name,
                'account_number' => $payout->account_number,
                'amount_original' => $payout->amount,
                'currency' => 'IDR'
            ]);

            // Create the CreatePayoutRequest object
            $create_payout_request = new \Xendit\Payout\CreatePayoutRequest([
                'reference_id' => $payout->xendit_reference_id,
                'channel_code' => $this->getChannelCode($payout),
                'channel_properties' => [
                'account_holder_name' => $payout->account_name,
                'account_number' => $payout->account_number,
                ],
                'amount' => (int) $payout->amount,
                'description' => 'Penarikan Saldo - SideHunt',
                'currency' => 'IDR'
            ]);

            // Generate idempotency key to prevent duplicate requests
            $idempotency_key = 'payout_' . $payout->id . '_' . time();
            
            Log::info('Xendit request payload', [
                'idempotency_key' => $idempotency_key,
                'create_payout_request' => $create_payout_request
            ]);

            // Call createPayout with correct parameters
            $result = $this->payoutApi->createPayout($idempotency_key, null, $create_payout_request);

            Log::info('Xendit disbursement created', [
                'payout_id' => $payout->id,
                'xendit_response' => $result
            ]);

            return [
                'success' => true,
                'disbursement_id' => $result['id'],
                'status' => $result['status']
            ];

        } catch (\Exception $e) {
            Log::error('Xendit disbursement failed', [
                'payout_id' => $payout->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $this->getErrorMessage($e->getMessage())
            ];
        }
    }


    /**
     * Get channel code for Xendit (both bank and e-wallet)
     */
    private function getChannelCode(FinancialTransaction $payout): string
    {
        if ($payout->payment_type === 'ewallet') {
            return $this->getEwalletChannelCode($payout->bank_code);
        }
        
        return $this->getBankChannelCode($payout->bank_code);
    }

    /**
     * Get bank channel code for Xendit
     */
    private function getBankChannelCode(string $bankCode): string
    {
        $bankMapping = [
            'BCA' => 'ID_BCA',
            'BNI' => 'ID_BNI',
            'BRI' => 'ID_BRI',
            'MANDIRI' => 'ID_MANDIRI',
            'CIMB' => 'ID_CIMB',
            'DANAMON' => 'ID_DANAMON',
            'PERMATA' => 'ID_PERMATA',
            'MAYBANK' => 'ID_MAYBANK',
            'PANIN' => 'ID_PANIN',
            'BSI' => 'ID_BSI',
            'MUAMALAT' => 'ID_MUAMALAT',
            'BTN' => 'ID_BTN',
            'BUKOPIN' => 'ID_BUKOPIN',
            'MEGA' => 'ID_MEGA',
            'OCBC' => 'ID_OCBC',
            'DBS' => 'ID_DBS',
            'CITIBANK' => 'ID_CITIBANK',
            'HSBC' => 'ID_HSBC',
            'STANDARD_CHARTERED' => 'ID_STANDARD_CHARTERED',
            'ANZ' => 'ID_ANZ',
            'UOB' => 'ID_UOB',
            'COMMONWEALTH' => 'ID_COMMONWEALTH',
            'SINARMAS' => 'ID_SINARMAS',
            'JAGO' => 'ID_JAGO',
            'BCA_DIGITAL' => 'ID_BCA_DIGITAL',
            'SEABANK' => 'ID_SEABANK',
            'ALLO' => 'ID_ALLO',
            'OKE' => 'ID_OKE',
            'BNC' => 'ID_BNC',
            'DKI' => 'ID_DKI',
            'JAWA_BARAT' => 'ID_BJB',
            'JAWA_TENGAH' => 'ID_JAWA_TENGAH',
            'JAWA_TIMUR' => 'ID_JAWA_TIMUR',
            'SUMUT' => 'ID_SUMUT',
            'SUMSEL_DAN_BABEL' => 'ID_SUMSEL_DAN_BABEL',
            'SUMATERA_BARAT' => 'ID_SUMATERA_BARAT',
            'RIAU_DAN_KEPRI' => 'ID_RIAU_DAN_KEPRI',
            'JAMBI' => 'ID_JAMBI',
            'ACEH' => 'ID_ACEH',
            'LAMPUNG' => 'ID_LAMPUNG',
            'BENGKULU' => 'ID_BENGKULU',
            'SULSELBAR' => 'ID_SULSELBAR',
            'SULUT' => 'ID_SULUT',
            'SULAWESI' => 'ID_SULAWESI',
            'SULAWESI_TENGGARA' => 'ID_SULAWESI_TENGGARA',
            'KALIMANTAN_BARAT' => 'ID_KALIMANTAN_BARAT',
            'KALIMANTAN_SELATAN' => 'ID_KALIMANTAN_SELATAN',
            'KALIMANTAN_TENGAH' => 'ID_KALIMANTAN_TENGAH',
            'KALIMANTAN_TIMUR' => 'ID_KALIMANTAN_TIMUR',
            'BALI' => 'ID_BALI',
            'NUSA_TENGGARA_BARAT' => 'ID_NUSA_TENGGARA_BARAT',
            'NUSA_TENGGARA_TIMUR' => 'ID_NUSA_TENGGARA_TIMUR',
            'MALUKU' => 'ID_MALUKU',
            'PAPUA' => 'ID_PAPUA',
        ];

        return $bankMapping[$bankCode] ?? 'ID_BCA'; // Default to BCA if not found
    }

    /**
     * Get e-wallet channel code for Xendit
     */
    private function getEwalletChannelCode(string $ewalletCode): string
    {
        $ewalletMapping = [
            'DANA' => 'ID_DANA',
            'GOPAY' => 'ID_GOPAY',
            'OVO' => 'ID_OVO',
            'LINKAJA' => 'ID_LINKAJA',
            'SHOPEEPAY' => 'ID_SHOPEEPAY',
        ];

        return $ewalletMapping[$ewalletCode] ?? 'ID_DANA'; // Default to DANA if not found
    }

    /**
     * Get user-friendly error message
     */
    private function getErrorMessage(string $error): string
    {
        if (str_contains($error, 'insufficient_balance')) {
            return 'Saldo tidak mencukupi untuk melakukan penarikan.';
        }
        
        if (str_contains($error, 'invalid_account')) {
            return 'Nomor rekening tidak valid. Silakan periksa kembali.';
        }
        
        if (str_contains($error, 'bank_maintenance')) {
            return 'Bank sedang dalam pemeliharaan. Silakan coba lagi nanti.';
        }
        
        return 'Terjadi kesalahan pada sistem pembayaran. Silakan coba lagi nanti.';
    }

    /**
     * Get supported banks list
     */
    public static function getSupportedBanks(): array
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'BCA_DIGITAL' => 'Bank Central Asia Digital (BluBCA)',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'MANDIRI' => 'Bank Mandiri',
            'CIMB' => 'Bank CIMB Niaga',
            'PERMATA' => 'Bank Permata',
            'DANAMON' => 'Bank Danamon',
            'MAYBANK' => 'Bank Maybank',
            'PANIN' => 'Bank Panin',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'MUAMALAT' => 'Bank Muamalat Indonesia',
            'BTN' => 'Bank Tabungan Negara (BTN)',
            'BUKOPIN' => 'Bank Bukopin',
            'MEGA' => 'Bank Mega',
            'OCBC' => 'Bank OCBC NISP',
            'DBS' => 'Bank DBS Indonesia',
            'CITIBANK' => 'Citibank',
            'HSBC' => 'HSBC Indonesia',
            'STANDARD_CHARTERED' => 'Standard Chartered Bank',
            'ANZ' => 'Bank ANZ Indonesia',
            'UOB' => 'Bank UOB Indonesia',
            'COMMONWEALTH' => 'Bank Commonwealth',
            'SINARMAS' => 'Bank Sinarmas',
            'JAGO' => 'Bank Jago',
            'SEABANK' => 'Bank SeaBank Indonesia',
            'ALLO' => 'Allo Bank Indonesia',
            'OKE' => 'Bank Oke Indonesia',
            'BNC' => 'Bank Neo Commerce',
            
            // Regional Banks (BPD)
            'DKI' => 'Bank DKI',
            'JAWA_BARAT' => 'Bank BJB',
            'JAWA_TENGAH' => 'Bank Pembangunan Daerah Jawa Tengah',
            'JAWA_TIMUR' => 'Bank Pembangunan Daerah Jawa Timur',
            'SUMUT' => 'Bank Pembangunan Daerah Sumut',
            'SUMSEL_DAN_BABEL' => 'Bank Pembangunan Daerah Sumsel Dan Babel',
            'SUMATERA_BARAT' => 'Bank Pembangunan Daerah Sumatera Barat',
            'RIAU_DAN_KEPRI' => 'Bank Pembangunan Daerah Riau Dan Kepri',
            'JAMBI' => 'Bank Pembangunan Daerah Jambi',
            'ACEH' => 'Bank Pembangunan Daerah Aceh',
            'LAMPUNG' => 'Bank Pembangunan Daerah Lampung',
            'BENGKULU' => 'Bank Pembangunan Daerah Bengkulu',
            'SULSELBAR' => 'Bank Pembangunan Daerah Sulselbar',
            'SULUT' => 'Bank Pembangunan Daerah Sulut',
            'SULAWESI' => 'Bank Pembangunan Daerah Sulawesi Tengah',
            'SULAWESI_TENGGARA' => 'Bank Pembangunan Daerah Sulawesi Tenggara',
            'KALIMANTAN_BARAT' => 'Bank Pembangunan Daerah Kalimantan Barat',
            'KALIMANTAN_SELATAN' => 'Bank Pembangunan Daerah Kalimantan Selatan',
            'KALIMANTAN_TENGAH' => 'Bank Pembangunan Daerah Kalimantan Tengah',
            'KALIMANTAN_TIMUR' => 'Bank Pembangunan Daerah Kalimantan Timur',
            'BALI' => 'Bank Pembangunan Daerah Bali',
            'NUSA_TENGGARA_BARAT' => 'Bank Pembangunan Daerah Nusa Tenggara Barat',
            'NUSA_TENGGARA_TIMUR' => 'Bank Pembangunan Daerah Nusa Tenggara Timur',
            'MALUKU' => 'Bank Pembangunan Daerah Maluku',
            'PAPUA' => 'Bank Pembangunan Daerah Papua',
        ];
    }

    /**
     * Get supported e-wallets list
     */
    public static function getSupportedEwallets(): array
    {
        return [
            'DANA' => 'DANA',
            'GOPAY' => 'GoPay',
            'OVO' => 'OVO',
            'LINKAJA' => 'LinkAja',
            'SHOPEEPAY' => 'ShopeePay',
        ];
    }
}
