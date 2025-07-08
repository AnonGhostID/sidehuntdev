@extends('layouts.management')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Ringkasan Keuangan {{ $periodName }}</h2>
                <p class="text-gray-600">
                    @if($selectedMonth == 'all' && $selectedYear == 'all')
                        Berikut adalah ringkasan pemasukan dan pengeluaran untuk semua periode berdasarkan data transaksi yang sebenarnya.
                    @else
                        Berikut adalah ringkasan pemasukan dan pengeluaran selama periode ini berdasarkan data transaksi yang sebenarnya.
                    @endif
                </p>
            </div>
            
            <!-- Month/Year Selector -->
            <form method="GET" action="{{ route('manajemen.keuangan.laporan') }}" class="flex flex-col sm:flex-row gap-2 mt-4 md:mt-0">
                <select name="month" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach($monthNames as $monthNum => $monthName)
                        <option value="{{ $monthNum }}" {{ $selectedMonth == $monthNum ? 'selected' : '' }}>
                            {{ $monthName }}
                        </option>
                    @endforeach
                </select>
                
                <select name="year" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
        </div>

        @if($allTransactions->isEmpty())
            <div class="text-center py-8">
                <div class="text-gray-500 mb-4">
                    <i class="fas fa-chart-line text-4xl"></i>
                </div>
                @if($selectedMonth == 'all' && $selectedYear == 'all')
                    <p class="text-gray-600">Belum ada transaksi untuk akun ini</p>
                @else
                    <p class="text-gray-600">Belum ada transaksi untuk bulan {{ $monthName }} {{ $selectedYear }}</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm">
                            <th class="px-5 py-3 border-b-2 border-gray-200">Tanggal</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Keterangan</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Pemasukan</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach($allTransactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    {{ $transaction['date']->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    {{ $transaction['description'] }}
                                    @if($transaction['type'] === 'topup')
                                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Top Up</span>
                                    @elseif($transaction['type'] === 'job_income')
                                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Pendapatan</span>
                                    @elseif($transaction['type'] === 'job_expense')
                                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Pengeluaran</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    @if($transaction['income'] > 0)
                                        <span class="text-green-600 font-medium">Rp {{ number_format($transaction['income'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    @if($transaction['expense'] > 0)
                                        <span class="text-red-600 font-medium">Rp {{ number_format($transaction['expense'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Total Pemasukan</p>
                            <p class="text-lg font-semibold text-green-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Total Pengeluaran</p>
                            <p class="text-lg font-semibold text-red-900">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Pendapatan Bersih</p>
                            <p class="text-lg font-semibold {{ $netIncome >= 0 ? 'text-blue-900' : 'text-red-900' }}">
                                Rp {{ number_format($netIncome, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-receipt text-gray-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800">Total Transaksi</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $totalTransactions }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Top Up</p>
                            <p class="text-lg font-semibold text-blue-900">{{ $topUpCount }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-briefcase text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Pendapatan Pekerjaan</p>
                            <p class="text-lg font-semibold text-green-900">{{ $jobIncomeCount }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-transfer text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Pembayaran Pekerjaan</p>
                            <p class="text-lg font-semibold text-red-900">{{ $jobExpenseCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($totalTransactions > 0)
                <!-- Average Statistics -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calculator text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">Rata-rata Pemasukan per Transaksi</p>
                                <p class="text-lg font-semibold text-yellow-900">
                                    Rp {{ number_format($avgIncome, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calculator text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-orange-800">Rata-rata Pengeluaran per Transaksi</p>
                                <p class="text-lg font-semibold text-orange-900">
                                    Rp {{ number_format($avgExpense, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    console.log('Halaman Laporan Keuangan dimuat dengan data dinamis.');
</script>
@endpush
