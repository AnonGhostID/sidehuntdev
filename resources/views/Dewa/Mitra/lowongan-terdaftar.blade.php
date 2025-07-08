@extends('Dewa.Base.Basic-page')

@section('css')
    <style>
        .job-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .job-card:hover {
            transform: translateY(-2px);
        }
        .job-header {
            background: linear-gradient(135deg, #1B4841 0%, #2d6a5f 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px 10px 0 0;
        }
        .job-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .job-salary {
            font-size: 1.1rem;
            font-weight: 500;
            color: #F2C255;
        }
        .job-body {
            padding: 1.5rem;
        }
        .job-info {
            margin-bottom: 1rem;
        }
        .job-info strong {
            color: #1B4841;
        }
        .applicant-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-diterima {
            background-color: #d1edff;
            color: #0c63e4;
        }
        .status-ditolak {
            background-color: #f8d7da;
            color: #721c24;
        }
        .btn-action {
            padding: 0.3rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 5px;
        }
        .no-applicants {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 2rem;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f3ff;
            color: #1B4841;
        }
        .accordion-button {
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0" style="color: #1B4841; font-weight: 600;">
                    <i class="bi bi-briefcase-fill me-2"></i>Lowongan Terdaftar
                </h2>
                <span class="badge bg-primary fs-6">{{ $jobs->count() }} Lowongan</span>
            </div>

            @if($jobs->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-briefcase" style="font-size: 4rem; color: #dee2e6;"></i>
                    <h4 class="mt-3 text-muted">Belum ada lowongan pekerjaan</h4>
                    <p class="text-muted">Mulai buat lowongan pekerjaan pertama Anda!</p>
                    <a href="/kerja/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Buat Lowongan Baru
                    </a>
                </div>
            @else
                <div class="accordion" id="jobsAccordion">
                    @foreach($jobs as $index => $job)
                        <div class="card job-card mb-3">
                            <div class="job-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="job-title">{{ $job->nama }}</div>
                                        <div class="job-salary">
                                            Rp {{ number_format($job->min_gaji, 0, ',', '.') }} - 
                                            Rp {{ number_format($job->max_gaji, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="opacity-75">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($job->created_at)->format('d M Y') }}
                                        </small>
                                        <br>
                                        <span class="badge bg-light text-dark mt-1">
                                            {{ $job->pelamar->count() }} Pelamar
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="job-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="job-info">
                                            <strong><i class="bi bi-geo-alt me-1"></i>Lokasi:</strong> {{ $job->alamat }}
                                        </div>
                                        <div class="job-info">
                                            <strong><i class="bi bi-people me-1"></i>Max Pekerja:</strong> {{ $job->max_pekerja }} orang
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="job-info">
                                            <strong><i class="bi bi-calendar-check me-1"></i>Mulai:</strong> 
                                            {{ \Carbon\Carbon::parse($job->start_job)->format('d F Y') }}
                                        </div>
                                        <div class="job-info">
                                            <strong><i class="bi bi-calendar-x me-1"></i>Selesai:</strong> 
                                            {{ \Carbon\Carbon::parse($job->end_job)->format('d F Y') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $job->id }}" aria-expanded="false" 
                                            aria-controls="collapse{{ $job->id }}">
                                        <i class="bi bi-eye me-1"></i>Lihat Pelamar ({{ $job->pelamar->count() }})
                                    </button>
                                    <div>
                                        <a href="/kerja/{{ $job->id }}" class="btn btn-success btn-sm me-2">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </a>
                                    </div>
                                </div>

                                <div class="collapse mt-3" id="collapse{{ $job->id }}" data-bs-parent="#jobsAccordion">
                                    <div class="border-top pt-3">
                                        <h6 class="mb-3"><i class="bi bi-people-fill me-2"></i>Daftar Pelamar</h6>
                                        
                                        @if($job->pelamar->isEmpty())
                                            <div class="no-applicants">
                                                <i class="bi bi-person-x" style="font-size: 2rem; color: #dee2e6;"></i>
                                                <p class="mt-2 mb-0">Belum ada yang melamar pekerjaan ini</p>
                                            </div>
                                        @else
                                            <!-- Tab untuk filter status -->
                                            <ul class="nav nav-tabs mb-3" id="applicantTab{{ $job->id }}" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="all-tab{{ $job->id }}" data-bs-toggle="tab" 
                                                            data-bs-target="#all{{ $job->id }}" type="button" role="tab">
                                                        Semua ({{ $job->pelamar->count() }})
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pending-tab{{ $job->id }}" data-bs-toggle="tab" 
                                                            data-bs-target="#pending{{ $job->id }}" type="button" role="tab">
                                                        Pending ({{ $job->pelamar->where('status', 'pending')->count() }})
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="accepted-tab{{ $job->id }}" data-bs-toggle="tab" 
                                                            data-bs-target="#accepted{{ $job->id }}" type="button" role="tab">
                                                        Diterima ({{ $job->pelamar->where('status', 'diterima')->count() }})
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="rejected-tab{{ $job->id }}" data-bs-toggle="tab" 
                                                            data-bs-target="#rejected{{ $job->id }}" type="button" role="tab">
                                                        Ditolak ({{ $job->pelamar->where('status', 'ditolak')->count() }})
                                                    </button>
                                                </li>
                                            </ul>

                                            <div class="tab-content" id="applicantTabContent{{ $job->id }}">
                                                <!-- All Applicants -->
                                                <div class="tab-pane fade show active" id="all{{ $job->id }}" role="tabpanel">
                                                    @foreach($job->pelamar->sortByDesc('created_at') as $pelamar)
                                                        @include('Dewa.Mitra.partials.applicant-card', ['pelamar' => $pelamar])
                                                    @endforeach
                                                </div>

                                                <!-- Pending Applicants -->
                                                <div class="tab-pane fade" id="pending{{ $job->id }}" role="tabpanel">
                                                    @foreach($job->pelamar->where('status', 'pending')->sortByDesc('created_at') as $pelamar)
                                                        @include('Dewa.Mitra.partials.applicant-card', ['pelamar' => $pelamar])
                                                    @endforeach
                                                </div>

                                                <!-- Accepted Applicants -->
                                                <div class="tab-pane fade" id="accepted{{ $job->id }}" role="tabpanel">
                                                    @foreach($job->pelamar->where('status', 'diterima')->sortByDesc('created_at') as $pelamar)
                                                        @include('Dewa.Mitra.partials.applicant-card', ['pelamar' => $pelamar])
                                                    @endforeach
                                                </div>

                                                <!-- Rejected Applicants -->
                                                <div class="tab-pane fade" id="rejected{{ $job->id }}" role="tabpanel">
                                                    @foreach($job->pelamar->where('status', 'ditolak')->sortByDesc('created_at') as $pelamar)
                                                        @include('Dewa.Mitra.partials.applicant-card', ['pelamar' => $pelamar])
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function updateStatus(pelamarId, status) {
        const actionText = status === 'diterima' ? 'menerima' : 'menolak';
        const actionUrlPart = status === 'diterima' ? 'terima' : 'tolak';
        
        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${actionText} pelamar ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'diterima' ? '#198754' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dewa/mitra/pelamar/${pelamarId}/${actionUrlPart}`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method override for PATCH
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
