@extends('layouts.management')

@section('title', 'Track Record Pelamar')
@section('page-title', 'Track Record Pelamar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Track Record Pelamar</h2>
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan riwayat pekerjaan dari pelamar yang sedang melamar ke pekerjaan Anda
            </div>
        </div>
        
        @if(count($applicantsWithHistory) > 0)
            {{-- Statistics Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Total Pelamar Aktif</p>
                            <p class="text-2xl font-bold text-blue-800">{{ count($applicantsWithHistory) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Rata-rata Tingkat Penyelesaian</p>
                            <p class="text-2xl font-bold text-green-800">
                                {{ count($applicantsWithHistory) > 0 ? round(collect($applicantsWithHistory)->avg('statistics.completion_rate'), 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-briefcase text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-600">Total Pekerjaan Diselesaikan</p>
                            <p class="text-2xl font-bold text-purple-800">
                                {{ collect($applicantsWithHistory)->sum('statistics.completed_jobs') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Applicants List --}}
            <div class="space-y-6">
                @foreach($applicantsWithHistory as $applicantData)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        {{-- Applicant Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-gray-600 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $applicantData['user']->nama }}</h3>
                                    <p class="text-sm text-gray-600">{{ $applicantData['user']->email }}</p>
                                    <p class="text-sm text-gray-500">
                                        Melamar ke: <span class="font-medium">{{ $applicantData['current_application']->sidejob->nama }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Menunggu Persetujuan
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    Melamar {{ $applicantData['current_application']->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Statistics Cards --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-gray-800">{{ $applicantData['statistics']['total_jobs'] }}</div>
                                <div class="text-xs text-gray-600">Total Lamaran</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $applicantData['statistics']['completed_jobs'] }}</div>
                                <div class="text-xs text-gray-600">Pekerjaan Selesai</div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $applicantData['statistics']['rejected_jobs'] }}</div>
                                <div class="text-xs text-gray-600">Ditolak</div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $applicantData['statistics']['completion_rate'] }}%</div>
                                <div class="text-xs text-gray-600">Tingkat Penyelesaian</div>
                            </div>
                        </div>

                        {{-- Job History Toggle --}}
                        <div class="border-t pt-4">
                            <button 
                                onclick="toggleHistory('history-{{ $applicantData['user']->id }}')"
                                class="flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm"
                            >
                                <i class="fas fa-history mr-2"></i>
                                Lihat Riwayat Pekerjaan ({{ $applicantData['statistics']['total_jobs'] }} pekerjaan)
                                <i class="fas fa-chevron-down ml-2 transform transition-transform" id="chevron-{{ $applicantData['user']->id }}"></i>
                            </button>
                            
                            {{-- Job History Details --}}
                            <div id="history-{{ $applicantData['user']->id }}" class="hidden mt-4">
                                @if($applicantData['job_history']->count() > 0)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-800 mb-3">Riwayat Pekerjaan</h4>
                                        <div class="space-y-3 max-h-64 overflow-y-auto">
                                            @foreach($applicantData['job_history'] as $job)
                                                <div class="flex justify-between items-center bg-white rounded-lg p-3 border">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-gray-800">{{ $job->sidejob->nama ?? 'Pekerjaan Tidak Ditemukan' }}</h5>
                                                        <p class="text-sm text-gray-600">
                                                            Mitra: {{ $job->sidejob->pembuat->nama ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $job->created_at->format('d M Y') }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        @if($job->status === 'diterima')
                                                            @php
                                                                $jobStatus = $job->sidejob ? trim(strtolower($job->sidejob->status)) : '';
                                                                $isCompleted = in_array($jobStatus, ['selesai', 'completed', 'finished', 'done']);
                                                            @endphp
                                                            @if($job->sidejob && $isCompleted)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    <i class="fas fa-check-circle mr-1"></i>
                                                                    Selesai
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    Dalam Pengerjaan
                                                                    {{-- Debug: Show actual status --}}
                                                                    @if($job->sidejob)
                                                                        <small class="ml-1 text-xs opacity-75">({{ $job->sidejob->status }})</small>
                                                                    @endif
                                                                </span>
                                                            @endif
                                                        @elseif($job->status === 'pending')
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                                Menunggu
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <i class="fas fa-times-circle mr-1"></i>
                                                                Ditolak
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-600">Belum ada riwayat pekerjaan</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pelamar</h3>
                <p class="text-gray-600 mb-6">
                    Saat ini tidak ada pelamar yang sedang melamar ke pekerjaan Anda.
                </p>
                <a href="{{ route('manajemen.pekerjaan.terdaftar') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-briefcase mr-2"></i>
                    Lihat Pekerjaan Terdaftar
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function toggleHistory(historyId) {
    const historyDiv = document.getElementById(historyId);
    const chevron = document.getElementById('chevron-' + historyId.split('-')[1]);
    
    if (historyDiv.classList.contains('hidden')) {
        historyDiv.classList.remove('hidden');
        chevron.classList.add('rotate-180');
    } else {
        historyDiv.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }
}
</script>
@endsection