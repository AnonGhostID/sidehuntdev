@extends('layouts.management')

@section('title', 'Pekerjaan Terdaftar')
@section('page-title', 'Daftar Pekerjaan Terdaftar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pekerjaan Yang Telah Anda Daftarkan</h2>
        </div>
        
        {{-- Statistics Cards --}}
        @if($pekerjaans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-briefcase text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-600">Total Pekerjaan Anda</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $pekerjaans->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-600">Total Pekerjaan Aktif</p>
                        <p class="text-2xl font-bold text-green-900">{{ $pekerjaans->where('status', 'Open')->count() }}</p>
                    </div>
                </div>
            </div>
            
            {{-- <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-600">Berlangsung</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $pekerjaans->where('status', 'In Progress')->count() }}</p>
                    </div>
                </div>
            </div> --}}
            
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-purple-600">Total Pelamar</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $pekerjaans->sum(function($p) { return $p->pelamar->count(); }) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pekerjaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelamar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Pekerja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pekerjaans as $pekerjaan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">{{ $pekerjaan->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($pekerjaan->deskripsi, 50) }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <i class="fas fa-map-marker-alt"></i> {{ Str::limit($pekerjaan->alamat, 30) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    Rp {{ number_format($pekerjaan->min_gaji, 0, ',', '.') }}
                                    @if($pekerjaan->min_gaji != $pekerjaan->max_gaji)
                                        - Rp {{ number_format($pekerjaan->max_gaji, 0, ',', '.') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($pekerjaan->status == 'Open') bg-green-100 text-green-800
                                    @elseif($pekerjaan->status == 'Done') bg-gray-100 text-gray-800
                                    @elseif($pekerjaan->status == 'In Progress') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $pekerjaan->status }}
                                </span>
                                @if($pekerjaan->is_active == 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 ml-1">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ \Carbon\Carbon::parse($pekerjaan->start_job)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pekerjaan->start_job)->format('H:i') }}</div>
                                @if($pekerjaan->deadline_job)
                                    <div class="text-xs text-red-500 mt-1">
                                        Deadline: {{ \Carbon\Carbon::parse($pekerjaan->deadline_job)->format('d M Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="text-lg font-semibold">{{ $pekerjaan->pelamar->count() }}</span>
                                    <span class="text-gray-500 ml-1">pelamar</span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Diterima: {{ $pekerjaan->jumlah_pelamar_diterima }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $pekerjaan->max_pekerja }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="text-indigo-600 hover:text-indigo-900" onclick="viewDetails({{ $pekerjaan->id }})">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <a href="{{ route('manajemen.pekerjaan.manage', $pekerjaan->id) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-cog"></i> Kelola
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">Belum ada pekerjaan yang terdaftar</p>
                                    <p class="text-sm text-gray-400">Mulai buat lowongan pekerjaan pertama Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Detail Pekerjaan</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewDetails(jobId) {
    // Find job data from the current page
    const job = @json($pekerjaans).find(j => j.id === jobId);
    if (!job) return;
    
    document.getElementById('modalTitle').textContent = 'Detail: ' + job.nama;
    document.getElementById('modalContent').innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-700">Deskripsi Pekerjaan</h4>
                <p class="text-gray-600">${job.deskripsi}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700">Informasi Gaji</h4>
                    <p class="text-gray-600">Rp ${parseInt(job.min_gaji).toLocaleString('id-ID')} ${job.min_gaji != job.max_gaji ? '- Rp ' + parseInt(job.max_gaji).toLocaleString('id-ID') : ''}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Maksimal Pekerja</h4>
                    <p class="text-gray-600">${job.max_pekerja} orang</p>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700">Lokasi</h4>
                <p class="text-gray-600">${job.alamat}</p>
                ${job.petunjuk_alamat ? `<p class="text-sm text-gray-500 mt-1">${job.petunjuk_alamat}</p>` : ''}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700">Tanggal Mulai</h4>
                    <p class="text-gray-600">${new Date(job.start_job).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Tanggal Selesai</h4>
                    <p class="text-gray-600">${new Date(job.end_job).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</p>
                </div>
            </div>
            ${job.kriteria ? `
            <div>
                <h4 class="font-semibold text-gray-700">Kriteria</h4>
                <p class="text-gray-600">${job.kriteria}</p>
            </div>
            ` : ''}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700">Status</h4>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                        job.status == 'Open' ? 'bg-green-100 text-green-800' :
                        job.status == 'Done' ? 'bg-gray-100 text-gray-800' :
                        job.status == 'In Progress' ? 'bg-blue-100 text-blue-800' :
                        'bg-yellow-100 text-yellow-800'
                    }">${job.status}</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Jumlah Pelamar</h4>
                    <p class="text-gray-600">${job.pelamar.length} orang (${job.jumlah_pelamar_diterima} diterima)</p>
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" onclick="closeModal()">
                Tutup
            </button>
        </div>
    `;
    document.getElementById('detailModal').classList.remove('hidden');
}

function viewApplicants(jobId) {
    // Redirect to applicants page or implement modal
    window.location.href = `/management/pekerjaan/${jobId}/pelamar`;
}


function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

@endsection
