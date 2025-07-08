<div class="applicant-card">
    <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-2">
                <div class="me-3">
                    <h6 class="mb-1 fw-bold">{{ $pelamar->user->nama }}</h6>
                    <small class="text-muted">
                        <i class="bi bi-envelope me-1"></i>{{ $pelamar->user->email }}
                    </small>
                    @if($pelamar->user->telpon)
                        <br>
                        <small class="text-muted">
                            <i class="bi bi-telephone me-1"></i>{{ $pelamar->user->telpon }}
                        </small>
                    @endif
                </div>
                <div class="ms-auto">
                    <span class="status-badge status-{{ $pelamar->status }}">
                        @if($pelamar->status == 'pending')
                            <i class="bi bi-clock me-1"></i>Pending
                        @elseif($pelamar->status == 'diterima')
                            <i class="bi bi-check-circle me-1"></i>Diterima
                        @else
                            <i class="bi bi-x-circle me-1"></i>Ditolak
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bi bi-calendar me-1"></i>Melamar pada: 
                    {{ $pelamar->created_at->format('d F Y H:i') }}
                </small>
                
                <div class="btn-group" role="group">
                    @if($pelamar->status == 'pending')
                        <button type="button" class="btn btn-success btn-action" 
                                onclick="updateStatus({{ $pelamar->id }}, 'diterima')">
                            <i class="bi bi-check me-1"></i>Terima
                        </button>
                        <button type="button" class="btn btn-danger btn-action" 
                                onclick="updateStatus({{ $pelamar->id }}, 'ditolak')">
                            <i class="bi bi-x me-1"></i>Tolak
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
