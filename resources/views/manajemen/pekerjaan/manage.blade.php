@extends('layouts.management')

@section('title', 'Kelola Pekerjaan')
@section('page-title', 'Kelola Pekerjaan')

@section('content')
<main class="container mx-auto px-4 py-8">
    <section class="bg-white p-6 rounded-lg shadow-lg">
        {{-- Header Section --}}
        <header class="border-b pb-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{ $pekerjaan->nama }}</h2>
            
            <div class="flex flex-wrap items-center gap-4">
                <p class="text-gray-600">
                    Status: 
                    <span class="font-medium px-3 py-1 rounded-full text-sm
                        @if($pekerjaan->status == 'Selesai') 
                            bg-green-100 text-green-800
                        @elseif($pekerjaan->status == 'Berlangsung') 
                            bg-blue-100 text-blue-800
                        @elseif($pekerjaan->status == 'Ditolak') 
                            bg-red-100 text-red-800
                        @else 
                            bg-gray-100 text-gray-800
                        @endif">
                        {{ $pekerjaan->status }}
                    </span>
                </p>
                @if($pekerjaan->status == 'Open')
                    <form action="{{ route('manajemen.pekerjaan.updateStatus', $pekerjaan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Set On Progress
                        </button>
                    </form>
                    <!-- Hapus dan Batalkan Pekerjaan Button -->
                    <button type="button" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded flex items-center" onclick="openDeleteModal()">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus dan Batalkan Pekerjaan
                    </button>
                @endif
                @if($laporans->count() > 0 && $pekerjaan->status == 'Berlangsung')
                    <form id="terimaHasilForm" action="{{ route('manajemen.pekerjaan.terimaHasil', $pekerjaan->id) }}" method="POST">
                        @csrf
                        <button type="button" onclick="openConfirmationModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Terima Hasil Pekerjaan
                        </button>
                    </form>
                @endif
            </div>
        </header>

        {{-- Laporan Section --}}
        <div>
            <h3 class="text-lg font-medium text-gray-700 mb-4">Laporan Pekerjaan</h3>
            
            @if($laporans->count() > 0)
                <div class="space-y-6">
                    @foreach($laporans as $laporan)
                        @include('manajemen.pekerjaan.partials.laporan-item', ['laporan' => $laporan])
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <p class="text-gray-500">Belum ada laporan yang diunggah.</p>
                    <p class="text-sm text-gray-400 mt-2">Laporan akan muncul setelah pekerja mengunggahnya.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Rating Section - Only show when job is completed --}}
    @if($pekerjaan->status == 'Selesai')
        <section class="bg-white p-6 rounded-lg shadow-lg mt-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Beri Rating Pekerja</h3>
            
            @if(session('rating_success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('rating_success') }}</span>
                </div>
            @endif

            @if(session('rating_error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('rating_error') }}</span>
                </div>
            @endif

            <div class="bg-gray-50 rounded-lg p-6">
                @php
                    $acceptedWorker = $pekerjaan->pelamar()->where('status', 'diterima')->with('user')->first();
                @endphp
                
                @if($acceptedWorker && $acceptedWorker->user)
                    <form action="{{ route('manajemen.pekerjaan.rating.store', $pekerjaan->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="pekerja_id" value="{{ $acceptedWorker->user->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pekerja</label>
                                <div class="flex items-center p-3 bg-white rounded-md border">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium">{{ substr($acceptedWorker->user->nama, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $acceptedWorker->user->nama }}</p>
                                        <p class="text-sm text-gray-500">{{ $acceptedWorker->user->email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="rating-stars">
                                    <input type="radio" name="rating" value="5" id="star5" required>
                                    <label for="star5" title="5 stars">&#9733;</label>
                                    <input type="radio" name="rating" value="4" id="star4" required>
                                    <label for="star4" title="4 stars">&#9733;</label>
                                    <input type="radio" name="rating" value="3" id="star3" required>
                                    <label for="star3" title="3 stars">&#9733;</label>
                                    <input type="radio" name="rating" value="2" id="star2" required>
                                    <label for="star2" title="2 stars">&#9733;</label>
                                    <input type="radio" name="rating" value="1" id="star1" required>
                                    <label for="star1" title="1 star">&#9733;</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar (Opsional)</label>
                            <textarea id="komentar" name="komentar" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Berikan komentar tentang kinerja pekerja..."></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-star mr-2"></i> Berikan Rating
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">Tidak ada pekerja yang dapat diberi rating untuk pekerjaan ini.</p>
                    </div>
                @endif
            </div>
        </section>
    @endif
    
    {{-- Include the image modal --}}
    @include('manajemen.pekerjaan.partials.image-modal')
    
    {{-- Confirmation Modal for Terima Hasil Pekerjaan --}}
    <div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-75 flex items-center justify-center p-4">
        <div class="relative max-w-md w-full bg-white rounded-lg shadow-xl">
            {{-- Modal Header --}}
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Terima Hasil Pekerjaan</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeConfirmationModal()">
                    <span class="sr-only">Tutup</span>
                    <i class="fa fa-times text-xl"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-700">
                            Apakah anda yakin akan menerima hasil pekerjaan ini? Dana yang anda buat untuk membuat pekerjaan akan langsung dikirimkan ke Pekerja!
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="flex justify-end space-x-3 p-6 border-t bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closeConfirmationModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Batal
                </button>
                <button type="button" onclick="confirmTerimaHasil()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Ya, Terima Hasil
                </button>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal for Delete & Cancel Job --}}
    <div id="deleteJobModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-75 flex items-center justify-center p-4">
        <div class="relative max-w-md w-full bg-white rounded-lg shadow-xl">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus & Batalkan Pekerjaan</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeDeleteModal()">
                    <span class="sr-only">Tutup</span>
                    <i class="fa fa-times text-xl"></i>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                    <p class="text-sm text-gray-700">
                        <span class="font-bold text-red-600">Peringatan:</span> Menghapus pekerjaan ini akan membatalkan lowongan dan mengembalikan dana anda sebesar Rp {{ number_format($pekerjaan->min_gaji, 0, ',', '.') }} 
                        (Belum termasuk biaya admin) ke saldo Dompet Anda. 
                        <br><br>
                        <div class="text-center mt-4">
                            <span class="font-bold text-l">Tindakan ini <span class="text-red-600">tidak dapat dibatalkan</span>.</span>
                        </div>
                    </p>
                </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="flex justify-end space-x-3 p-6 border-t bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Batal
                </button>
                <form id="deleteJobForm" action="{{ route('manajemen.pekerjaan.delete', $pekerjaan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Ya, Hapus & Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .rating-stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-stars label:hover,
        .rating-stars label:hover ~ label,
        .rating-stars input[type="radio"]:checked ~ label {
            color: #ffc107;
        }

        .rating-stars input[type="radio"]:checked ~ label {
            color: #ffc107;
        }
    </style>

    <script>
        // Function to open the confirmation modal
        function openConfirmationModal() {
            document.getElementById('confirmationModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        
        // Function to close the confirmation modal
        function closeConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
        // Function to confirm and submit the form
        function confirmTerimaHasil() {
            document.getElementById('terimaHasilForm').submit();
        }
        
        // Close modal when clicking outside the content
        document.getElementById('confirmationModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeConfirmationModal();
            }
        });
        
        // Add keyboard navigation for ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !document.getElementById('confirmationModal').classList.contains('hidden')) {
                closeConfirmationModal();
            }
        });

        function openDeleteModal() {
            document.getElementById('deleteJobModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteJobModal').classList.add('hidden');
        }
    </script>
</main>
@endsection