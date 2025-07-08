@extends('layouts.management')

@section('title', 'Manajemen Pekerjaan Berlangsung')
@section('page-title', 'Pekerjaan Sedang Berlangsung')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Pekerjaan yang Sedang Berlangsung</h2>
        <p class="text-gray-600 mb-6">
            Berikut adalah daftar pekerjaan yang saat ini sedang dalam progres. Anda dapat memantau status dan detail masing-masing pekerjaan.
        </p>

        {{-- Filter dan Tombol Aksi --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="relative">
                <input type="text" placeholder="Cari pekerjaan..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            {{-- <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-colors duration-300">
                <i class="fas fa-plus mr-2"></i> Tambah Pekerjaan Baru (Jika Perlu)
            </button> --}}
        </div>

        {{-- Tabel Data Pekerjaan --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm">
                        <th class="px-5 py-3 border-b-2 border-gray-200">Judul Pekerjaan</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Pemberi Kerja</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Pelamar Kerja</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Deadline</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($pekerjaanBerlangsung as $pelamar)
                    <tr>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            @if($pelamar->sidejob)
                                <p class="font-semibold">{{ $pelamar->sidejob->nama }}</p>
                                <p class="text-xs text-gray-500">Kategori: {{ $pelamar->sidejob->kriteria ?? 'Umum' }}</p>
                            @else
                                <p class="font-semibold">Pekerjaan tidak ditemukan</p>
                                <p class="text-xs text-gray-500">Kategori: -</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            @if($pelamar->sidejob && isset($pelamar->sidejob->pembuatUser))
                                {{ $pelamar->sidejob->pembuatUser->nama }}
                            @else
                                Tidak diketahui
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">{{ $pelamar->user->nama ?? 'Tidak diketahui' }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            @php
                                $statusPekerjaan = $pelamar->getStatusPekerjaan();
                            @endphp
                            @if($statusPekerjaan == 'Selesai')
                            <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                                <span class="relative">{{ $statusPekerjaan }}</span>
                            </span>
                            @elseif($pelamar->status == 'diterima')
                            <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                <span class="relative">{{ $statusPekerjaan }}</span>
                            </span>
                            @else
                            <span class="relative inline-block px-3 py-1 font-semibold text-yellow-900 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span>
                                <span class="relative">{{ $statusPekerjaan }}</span>
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            @php
                                $formattedDate = 'Tidak ada deadline';
                                if ($pelamar->sidejob && !empty($pelamar->sidejob->end_job)) {
                                    $deadline = \Carbon\Carbon::parse($pelamar->sidejob->end_job);
                                    $formattedDate = $deadline->locale('id')->format('d-M-Y');
                                }
                            @endphp
                            {{ $formattedDate }}
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            @if($statusPekerjaan == 'Selesai')
                                @php
                                    $user = session('account');
                                    $jobId = $pelamar->sidejob->id ?? null;
                                    $workerId = $pelamar->user_id ?? null;
                                    $employerId = $pelamar->sidejob->pembuat ?? null;
                                    
                                    // Check if current user can rate
                                    $canRateEmployer = $user->isUser() && $jobId && $employerId && 
                                                     \App\Models\Rating::canRate($user->id, $employerId, $jobId, 'worker_to_employer');
                                    $canRateWorker = $user->isMitra() && $jobId && $workerId && 
                                                   \App\Models\Rating::canRate($user->id, $workerId, $jobId, 'employer_to_worker');
                                @endphp
                                
                                @if($canRateEmployer)
                                    <button onclick="openRatingModal({{ $jobId }}, {{ $employerId }}, '{{ $pelamar->sidejob->pembuatUser->nama ?? 'Tidak diketahui' }}', 'employer')" class="text-yellow-500 hover:text-yellow-700" title="Beri Rating Pemberi Kerja">
                                        <i class="fas fa-star"></i> Rating
                                    </button>
                                @elseif($canRateWorker)
                                    <button onclick="openRatingModal({{ $jobId }}, {{ $workerId }}, '{{ $pelamar->user->nama ?? 'Tidak diketahui' }}', 'worker')" class="text-yellow-500 hover:text-yellow-700" title="Beri Rating Pekerja">
                                        <i class="fas fa-star"></i> Rating
                                    </button>
                                @else
                                    <span class="text-gray-400">
                                        <i class="fas fa-star"></i> Rated
                                    </span>
                                @endif
                            @else
                                <a href="#" class="text-red-500 hover:text-red-700" title="Report">
                                    <i class="fas fa-flag"></i> Report
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 border-b border-gray-200 text-sm text-center text-gray-500">
                            Tidak ada pekerjaan yang sedang berlangsung.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (jika diperlukan) --}}
        <div class="mt-6 flex justify-center">
            <nav aria-label="Page navigation">
                <ul class="inline-flex items-center -space-x-px">
                    <li>
                        <a href="#" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                    </li>
                    <li>
                        <a href="#" aria-current="page" class="px-3 py-2 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">1</a>
                    </li>
                    <li>
                        <a href="#" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">2</a>
                    </li>
                    <li>
                        <a href="#" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                    </li>
                </ul>
            </nav>
        </div>

        {{-- Rating Modal --}}
        <div id="ratingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900 mb-4">Beri Rating</h3>
                    <form id="ratingForm">
                        <div class="mb-4">
                            <label id="targetLabel" class="block text-sm font-medium text-gray-700 mb-2">Target Rating</label>
                            <p id="targetName" class="text-sm text-gray-600 bg-gray-50 p-2 rounded"></p>
                        </div>
                        
                        <div class="mb-4">
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
                        
                        <div class="mb-4">
                            <label for="komentar" class="block text-sm font-medium text-gray-700 mb-2">Komentar (Opsional)</label>
                            <textarea id="komentar" name="komentar" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Berikan komentar tentang pemberi kerja..."></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeRatingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                                <i class="fas fa-star mr-2"></i> Beri Rating
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
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
        font-size: 1.5rem;
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
    let currentJobId = null;
    let currentTargetId = null;
    let currentRatingType = null;

    function openRatingModal(jobId, targetId, targetName, ratingType) {
        currentJobId = jobId;
        currentTargetId = targetId;
        currentRatingType = ratingType;
        
        // Update modal content based on rating type
        const modalTitle = document.getElementById('modalTitle');
        const targetLabel = document.getElementById('targetLabel');
        const targetNameEl = document.getElementById('targetName');
        const commentPlaceholder = document.getElementById('komentar');
        
        if (ratingType === 'employer') {
            modalTitle.textContent = 'Beri Rating Pemberi Kerja';
            targetLabel.textContent = 'Pemberi Kerja';
            commentPlaceholder.placeholder = 'Berikan komentar tentang pemberi kerja...';
        } else {
            modalTitle.textContent = 'Beri Rating Pekerja';
            targetLabel.textContent = 'Pekerja';
            commentPlaceholder.placeholder = 'Berikan komentar tentang pekerja...';
        }
        
        targetNameEl.textContent = targetName;
        document.getElementById('ratingModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Reset form
        document.getElementById('ratingForm').reset();
    }

    function closeRatingModal() {
        document.getElementById('ratingModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentJobId = null;
        currentTargetId = null;
        currentRatingType = null;
    }

    // Handle form submission
    document.getElementById('ratingForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const rating = formData.get('rating');
        const comment = formData.get('komentar');
        
        // Prepare data for submission
        const data = {
            job_id: currentJobId,
            rating: rating,
            comment: comment
        };
        
        // Add target user based on rating type
        if (currentRatingType === 'employer') {
            data.employer_id = currentTargetId;
        } else {
            data.worker_id = currentTargetId;
        }
        
        try {
            const url = currentRatingType === 'employer' 
                ? '{{ route("manajemen.rating.worker.store") }}'
                : '{{ route("manajemen.pekerjaan.rating.store", ":jobId") }}'.replace(':jobId', currentJobId);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                alert('Rating berhasil diberikan!');
                closeRatingModal();
                // Refresh the page to update the UI
                window.location.reload();
            } else {
                alert(result.message || 'Gagal memberikan rating. Silakan coba lagi.');
            }
        } catch (error) {
            console.error('Error submitting rating:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });

    // Close modal when clicking outside
    document.getElementById('ratingModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeRatingModal();
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !document.getElementById('ratingModal').classList.contains('hidden')) {
            closeRatingModal();
        }
    });

    console.log('Halaman Manajemen Pekerjaan Berlangsung dimuat.');
</script>
@endpush
