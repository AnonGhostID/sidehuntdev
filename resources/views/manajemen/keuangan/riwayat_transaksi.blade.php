@extends('layouts.management')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi Keuangan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Riwayat Transaksi</h2>
        <p class="text-gray-600 mb-6">
            Berikut adalah daftar semua transaksi yang pernah dilakukan, termasuk top up saldo dan penarikan dana. Anda dapat memeriksa detail status transaksi di sini.
        </p>
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
            <span class="text-gray-700">Saldo Anda Saat Ini:</span>
            <span class="font-semibold">Rp {{ number_format(session('account')->dompet, 0, ',', '.') }}</span>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="relative">
                <input id="search-input" type="text" placeholder="Cari transaksi..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <div class="flex items-center">
                <label for="per-page-select" class="mr-2 text-gray-700">View items per page:</label>
                <select id="per-page-select" class="py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm">
                        <th class="px-5 py-3 border-b-2 border-gray-200">Tanggal</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">ID Transaksi</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Deskripsi</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Jumlah</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Metode</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transaction-table-body" class="text-gray-700">
                    @include('manajemen.keuangan.partials.riwayat_transaksi_rows', ['transaksi' => $transaksi])
                </tbody>
            </table>
        </div>

        <div id="pagination-container" class="mt-6 flex justify-center">
            @include('manajemen.keuangan.partials.riwayat_transaksi_pagination', ['transaksi' => $transaksi])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const perPageSelect = document.getElementById('per-page-select');
    const tableBody = document.getElementById('transaction-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    let timer;
    function fetchData(url) {
        fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = data.table;
                paginationContainer.innerHTML = data.pagination;
            });
    }
    function updateData(page = 1) {
        const search = searchInput.value;
        const perPage = perPageSelect.value;
        const url = `{{ route('manajemen.transaksi.riwayat.data') }}?search=${encodeURIComponent(search)}&per_page=${perPage}&page=${page}`;
        fetchData(url);
    }
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => updateData(1), 300);
    });
    perPageSelect.addEventListener('change', () => updateData(1));
    paginationContainer.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link) {
            e.preventDefault();
            const params = new URL(link.href).searchParams;
            const page = params.get('page') || 1;
            updateData(page);
        }
    });
});
</script>
@endpush
