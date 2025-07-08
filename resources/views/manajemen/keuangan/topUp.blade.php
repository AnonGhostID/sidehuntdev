@extends('layouts.management')

@section('title')
    @if(isset($view_type))
        @if($view_type === 'waiting')
            Menunggu Pembayaran
        @elseif($view_type === 'success')
            Pembayaran Berhasil
        @elseif($view_type === 'failed')
            Pembayaran Gagal
        @endif
    @else
        Top Up Saldo
    @endif
@endsection

@section('page-title')
    @if(isset($view_type))
        @if($view_type === 'waiting')
            Menunggu Pembayaran
        @elseif($view_type === 'success')
            Pembayaran Berhasil
        @elseif($view_type === 'failed')
            Pembayaran Gagal
        @endif
    @else
        Top Up Saldo
    @endif
@endsection

@section('content')
{{-- Handle different payment states based on view_type --}}
@if(isset($view_type) && $view_type === 'waiting')
    {{-- WAITING STATE --}}
    {{-- Security: This should only be accessible for pending payments --}}
    @if($payment->status !== 'pending')
        <script>
            alert('Akses tidak diizinkan: Status pembayaran tidak sesuai');
            window.location.href = '{{ route("manajemen.topUp") }}';
        </script>
    @else
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
                <div class="text-center mb-6">
                    <div id="payment-icon" class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <h2 id="payment-title" class="text-2xl font-semibold text-gray-800 mb-2">Invoice telah dibuat!</h2>
                    <p id="payment-subtitle" class="text-gray-600">Silakan klik "Bayar Sekarang" untuk melakukan pembayaran</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Jumlah Top Up:</span>
                            <p class="font-semibold text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status:</span>
                            <p id="payment-status" class="font-semibold text-lg text-yellow-600">Menunggu Pembayaran</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-500">ID Transaksi:</span>
                        <p class="font-mono text-sm">{{ $payment->external_id }}</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-yellow-600 mr-2"></i>
                        <span class="text-sm">Pembayaran akan kedaluwarsa dalam: </span>
                        <span id="countdown" class="font-semibold text-yellow-800 ml-2">00:00</span>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <a href="{{ $payment->checkout_link }}" target="_blank" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-colors duration-300 inline-block">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Bayar Sekarang
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button onclick="checkPaymentStatus()" 
                            class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300">
                        <i class="fas fa-refresh mr-2"></i>
                        Cek Status Sekarang
                    </button>
                    <form action="{{ route('manajemen.topup.cancel', $payment->external_id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" onclick="return confirmCancel()"
                                class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i>
                            Batalkan
                        </button>
                    </form>
                </div>

                <div id="message-container" class="mt-6 hidden">
                    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span id="success-text"></span>
                    </div>
                    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="error-text"></span>
                    </div>
                </div>
            </div>
        </div>
    @endif

@elseif(isset($view_type) && $view_type === 'success')
    {{-- SUCCESS STATE --}}
    {{-- Security: This should only be accessible for paid/settled payments --}}
    @if(!in_array($payment->status, ['completed']))
        <script>
            alert('Akses tidak diizinkan: Pembayaran belum berhasil');
            window.location.href = '{{ route("manajemen.topUp") }}';
        </script>
    @else
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Pembayaran Berhasil!</h2>
                <p class="text-gray-600 mb-6">Top up saldo Anda telah berhasil diproses.</p>

                <div class="bg-gray-50 p-4 rounded-lg mb-6 text-left">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Jumlah Top Up:</span>
                            <p class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status:</span>
                            <p class="font-semibold text-green-600">{{ ucfirst($payment->status) }}</p>
                        </div>
                    </div>
                    @if($payment->method)
                    <div class="mt-4">
                        <span class="text-sm text-gray-500">Metode Pembayaran:</span>
                        <p class="font-semibold">{{ strtoupper($payment->method) }}</p>
                    </div>
                    @endif
                    <div class="mt-4">
                        <span class="text-sm text-gray-500">Tanggal:</span>
                        <p class="font-semibold">{{ $payment->updated_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6">
                    <span class="text-sm text-blue-600">Saldo Dompet Saat Ini:</span>
                    <p class="text-xl font-bold text-blue-800">Rp {{ number_format(session('account')['dompet'], 0, ',', '.') }}</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('manajemen.topUp') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-colors duration-300 text-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Top Up Lagi
                    </a>
                    
                    <a href="{{ route('manajemen.transaksi.riwayat') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-colors duration-300 text-sm">
                        <i class="fas fa-history mr-2"></i>
                        Lihat Riwayat
                    </a>
                    
                    <a href="{{ route('manajemen.dashboard') }}" 
                       class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-colors duration-300 text-sm">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif

@elseif(isset($view_type) && $view_type === 'failed')
    {{-- FAILED STATE --}}
    {{-- Security: This should only be accessible for failed/expired/cancelled payments --}}
    @if(!in_array($payment->status, ['failed', 'expired', 'cancelled']))
        <script>
            alert('Akses tidak diizinkan: Halaman ini hanya untuk pembayaran yang gagal');
            window.location.href = '{{ route("manajemen.topUp") }}';
        </script>
    @else
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                        <i class="fas fa-times text-red-600 text-2xl"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Pembayaran Gagal</h2>
                <p class="text-gray-600 mb-6">Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi.</p>

                <div class="bg-gray-50 p-4 rounded-lg mb-6 text-left">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Jumlah Top Up:</span>
                            <p class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status:</span>
                            <p class="font-semibold text-red-600">{{ ucfirst($payment->status) }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-500">ID Transaksi:</span>
                        <p class="font-mono text-sm">{{ $payment->external_id }}</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6 text-left">
                    <h4 class="font-semibold text-yellow-800 mb-2">Kemungkinan Penyebab:</h4>
                    <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                        <li>Saldo tidak mencukupi</li>
                        <li>Pembayaran dibatalkan</li>
                        <li>Koneksi internet terputus</li>
                        <li>Timeout pembayaran</li>
                    </ul>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('manajemen.topUp') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-colors duration-300 text-sm">
                        <i class="fas fa-redo mr-1"></i>
                        Coba Lagi
                    </a>
                    
                    <a href="{{ route('manajemen.transaksi.riwayat') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-colors duration-300 text-sm">
                        <i class="fas fa-history mr-2"></i>
                        Lihat Riwayat
                    </a>
                    
                    <a href="{{ route('manajemen.dashboard') }}" 
                       class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-colors duration-300 text-sm">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2">Butuh bantuan?</p>
                    <a href="{{ route('manajemen.bantuan.panel') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                        <i class="fas fa-question-circle mr-1"></i>
                        Hubungi Customer Service
                    </a>
                </div>
            </div>
        </div>
    @endif

@else
    {{-- DEFAULT TOP UP FORM STATE --}}
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Isi Saldo Dompet</h2>
            
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
                <span class="text-gray-700">Saldo Dompet Saat Ini:</span>
                <span class="font-semibold">Rp {{ number_format(session('account')['dompet'], 0, ',', '.') }}</span>
            </div>
            
            <form action="{{ route('manajemen.topup.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Pilih Nominal Top Up</label>
                <select id="nominal" name="nominal" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">-- Pilih Nominal --</option>
                    @for ($i = 50000; $i <= 300000; $i += 50000)
                        <option value="{{ $i }}">Rp {{ number_format($i, 0, ',', '.') }}</option>
                    @endfor
                    <option value="500000">Rp {{ number_format(500000, 0, ',', '.') }}</option>
                    <option value="1000000">Rp {{ number_format(1000000, 0, ',', '.') }}</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="custom_amount" class="block text-sm font-medium text-gray-700 mb-1">Atau Masukkan Jumlah Custom</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                    <input 
                        type="text" 
                        id="custom_amount" 
                        name="custom_amount" 
                        data-min="20000" 
                        data-step="5000"
                        class="mt-1 block w-full pl-9 py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                        placeholder="Minimal Rp 20.000"
                    >
                    <input type="hidden" id="custom_amount_raw" name="custom_amount_raw">
                </div>
                <p class="text-xs text-gray-500 mt-1">Minimum top up adalah Rp 20.000</p>
            </div>
            <div class="mb-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                    <img src="{{ asset('xenditblue.png') }}" alt="Xendit" class="h-12 w-auto mx-auto mb-2">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Payment Gateway</span>
                        <p class="text-xs text-gray-500">Anda akan dialihkan menuju Payment Gateway</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit" id="topup-button" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400">
                    <i class="fas fa-wallet mr-2"></i> Top Up Sekarang
                </button>
            </div>
            </form>
        </div>
    </div>
@endif
@endsection

{{-- JavaScript for waiting state --}}
@if(isset($view_type) && $view_type === 'waiting')
@push('scripts')
<script>
let countdownInterval;
let autoCheckInterval;
let paymentCompleted = false;
let isTabActive = true;
let checkingStatus = false; // Prevent multiple simultaneous requests

document.addEventListener('DOMContentLoaded', function() {
    startCountdown();
    startAutoCheck();
    
    // Listen for tab visibility changes to prevent unnecessary API calls
    document.addEventListener('visibilitychange', function() {
        isTabActive = !document.hidden;
        
        if (isTabActive && !paymentCompleted) {
            // Tab became active, check payment status immediately
            checkPaymentStatus(false);
        }
    });
});

function startCountdown() {
    const expiryTime = new Date('{{ $payment->created_at }}').getTime() + ({{ session('invoice_duration') }} * 1000);

    countdownInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = expiryTime - now;
        
        if (distance > 0) {
            // const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            clearInterval(countdownInterval);
            clearInterval(autoCheckInterval);
            paymentCompleted = true;
            
            // Change icon to exclamation triangle
            const paymentIcon = document.getElementById('payment-icon');
            paymentIcon.className = "mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4";
            paymentIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>';
            
            // Update title and subtitle
            document.getElementById('payment-title').textContent = 'Pembayaran Kedaluwarsa';
            document.getElementById('payment-subtitle').textContent = 'Waktu pembayaran telah habis. Silakan buat transaksi baru.';
            
            document.getElementById('countdown').textContent = 'Kedaluwarsa';
            document.getElementById('payment-status').innerHTML = "Kedaluwarsa";
            document.getElementById('payment-status').className = "font-semibold text-lg text-red-600";
            
            showMessage('error', 'Pembayaran telah kedaluwarsa!');
            expireInvoiceOnTimeout();
        }
    }, 1000);
}

function startAutoCheck() {
    autoCheckInterval = setInterval(function() {
        if (!paymentCompleted) {
            checkPaymentStatus(true);
        }
    }, 5000);
}

function checkPaymentStatus(isAutomatic = false, isTimeoutCheck = false) {
    if (checkingStatus || paymentCompleted) return;
    
    checkingStatus = true;
    
    if (!isAutomatic && !isTimeoutCheck) {
        showMessage('info', 'Mengecek status pembayaran...');
    }
    
    fetch('{{ route("manajemen.topup.check-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            external_id: '{{ $payment->external_id }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        checkingStatus = false; // Reset flag
        
        if (data.status === 'success') {
            paymentCompleted = true;
            clearInterval(countdownInterval);
            clearInterval(autoCheckInterval);
            
            document.getElementById('payment-status').innerHTML = "Berhasil";
            document.getElementById('payment-status').className = "font-semibold text-lg text-green-600";
            
            showMessage('success', data.message + ' Saldo baru: Rp ' + new Intl.NumberFormat('id-ID').format(data.new_balance));
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 5000);
        } else if (data.status === 'failed' || data.status === 'expired' || data.status === 'cancelled' || isTimeoutCheck) {
            paymentCompleted = true;
            clearInterval(countdownInterval);
            clearInterval(autoCheckInterval);

            const paymentIcon = document.getElementById('payment-icon');
            paymentIcon.className = "mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4";
            paymentIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>';
            
            if (data.status === 'expired' || isTimeoutCheck) {
                document.getElementById('payment-title').textContent = 'Pembayaran Kedaluwarsa';
                document.getElementById('payment-subtitle').textContent = 'Waktu pembayaran telah habis. Silakan buat transaksi baru.';
            } else if (data.status === 'failed') {
                document.getElementById('payment-title').textContent = 'Pembayaran Gagal';
                document.getElementById('payment-subtitle').textContent = 'Pembayaran tidak dapat diproses. Silakan coba lagi.';
            } else if (data.status === 'cancelled') {
                document.getElementById('payment-title').textContent = 'Pembayaran Dibatalkan';
                document.getElementById('payment-subtitle').textContent = 'Pembayaran telah dibatalkan.';
            }
            
            let statusText = 'Gagal';
            if (data.status === 'expired' || isTimeoutCheck) statusText = 'Kedaluwarsa';
            else if (data.status === 'cancelled') statusText = 'Dibatalkan';
            
            document.getElementById('payment-status').innerHTML = statusText;
            document.getElementById('payment-status').className = "font-semibold text-lg text-red-600";
            
            if (isTimeoutCheck) {
                showMessage('error', 'Pembayaran telah kedaluwarsa. Halaman akan dimuat ulang...');
            } else {
                showMessage('error', data.message);
            }
            
            setTimeout(() => {
                window.location.href = window.location.href;
            }, isTimeoutCheck ? 1000 : 3000);
        } else if (data.status === 'pending') {
            if (!isAutomatic) {
                showMessage('info', 'Transaksi sedang berlangsung! Akan dicek otomatis setiap 5 detik.');
            }
        } else {
            if (!isAutomatic && !isTimeoutCheck) {
                showMessage('error', data.message || 'Terjadi kesalahan saat mengecek status pembayaran.');
            }
            
            if (isTimeoutCheck) {
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 1000);
            }
        }
    })
    .catch(error => {
        checkingStatus = false; // Reset flag
        console.error('Error:', error);
        
        if (isTimeoutCheck) {
            showMessage('error', 'Pembayaran telah kedaluwarsa. Halaman akan dimuat ulang...');
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 1000);
        } else if (!isAutomatic) {
            showMessage('error', 'Gagal mengecek status pembayaran. Silakan coba lagi.');
        }
    });
}

function expireInvoiceOnTimeout() {
    showMessage('info', 'Invoice telah kedaluwarsa!');
    
    fetch('{{ route("manajemen.topup.expire-timeout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            external_id: '{{ $payment->external_id }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'expired') {
            showMessage('error', 'Pembayaran telah kedaluwarsa dan invoice telah dihapus. Halaman akan dimuat ulang...');
        } else {
            showMessage('error', 'Pembayaran telah berubah status: ' + data.message + '. Halaman akan dimuat ulang...');
        }
        setTimeout(() => {
            window.location.href = window.location.href;
        }, 3000);
    })
    .catch(error => {
        console.error('Error expiring invoice:', error);
        showMessage('error', 'Gagal menghapus invoice, namun pembayaran telah kedaluwarsa. Halaman akan dimuat ulang...');
        setTimeout(() => {
            window.location.href = window.location.href;
        }, 3000);
    });
}

function confirmCancel() {
    if (confirm('Apakah Anda yakin ingin membatalkan pembayaran ini?')) {

        const paymentIcon = document.getElementById('payment-icon');
        paymentIcon.className = "mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4";
        paymentIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>';
        document.getElementById('payment-title').textContent = 'Pembayaran Dibatalkan';
        document.getElementById('payment-subtitle').textContent = 'Anda telah membatalkan pembayaran ini.';
        clearInterval(countdownInterval);
        clearInterval(autoCheckInterval);
        paymentCompleted = true;
        
        return true;
    }
    return false;
}

function showMessage(type, text) {
    const messageContainer = document.getElementById('message-container');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');
    
    successMessage.classList.add('hidden');
    errorMessage.classList.add('hidden');
    
    if (type === 'success') {
        successText.textContent = text;
        successMessage.classList.remove('hidden');
    } else if (type === 'error') {
        errorText.textContent = text;
        errorMessage.classList.remove('hidden');
    } else if (type === 'info') {
        successText.textContent = text;
        successMessage.classList.remove('hidden');
    }
    
    messageContainer.classList.remove('hidden');
    
    if (type !== 'error') {
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 5000);
    }
}
</script>
@endpush
@endif

{{-- JavaScript for default form state --}}
@if(!isset($view_type))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nominalSelect = document.getElementById('nominal');
        const customAmountInput = document.getElementById('custom_amount');
        const customAmountRaw = document.getElementById('custom_amount_raw');
        const topupButton = document.getElementById('topup-button');
        const form = document.querySelector('form');
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        
        function getRawNumber(formattedNum) {
            return formattedNum.replace(/\./g, '');
        }
        
        function checkButtonState() {
            const selectedNominal = nominalSelect.value;
            const customAmount = parseInt(customAmountRaw.value) || 0;
            
            if (selectedNominal || (customAmount >= 20000)) {
                topupButton.disabled = false;
            } else {
                topupButton.disabled = true;
            }
        }
        
        nominalSelect.addEventListener('change', function() {
            if (this.value) {
                customAmountInput.value = '';
                customAmountRaw.value = '';
            }
            checkButtonState();
        });
        
        customAmountInput.addEventListener('input', function() {
            if (this.value) {
                nominalSelect.value = '';
            }
            
            // Remove all non-numeric characters
            let rawValue = this.value.replace(/[^0-9]/g, '');
            
            // Format with thousands separator
            if (rawValue) {
                this.value = formatNumber(rawValue);
                customAmountRaw.value = rawValue;
            } else {
                this.value = '';
                customAmountRaw.value = '';
            }
            
            checkButtonState();
        });
        
        // Handle keypress to only allow numbers
        customAmountInput.addEventListener('keypress', function(e) {
            // Allow backspace, delete, tab, escape, enter
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        // Cek validasi form
        form.addEventListener('submit', function(e) {
            const selectedNominal = nominalSelect.value;
            const customAmount = customAmountRaw.value;
            
            if (!selectedNominal && !customAmount) {
                e.preventDefault();
                alert('Silakan pilih nominal atau masukkan jumlah custom untuk top up.');
                return;
            }
            
            if (customAmount && parseInt(customAmount) < 20000) {
                e.preventDefault();
                alert('Jumlah minimum top up adalah Rp 20.000');
                return;
            }
            
            if (customAmount) {
                customAmountInput.name = '';
                customAmountRaw.name = 'custom_amount';
            }
        });
        
        // Initial button state check
        checkButtonState();
    });
</script>
@endpush
@endif
