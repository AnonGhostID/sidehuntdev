@extends('layouts.app')

@section('content')
<div class="container">
    <h1>History Lamaran</h1>
    @if($lamaran->isEmpty())
    <div class="alert alert-warning" role="alert">
        Anda belum mendaftar pekerjaan dimana pun.
    </div>
    @else
    <ul>
        @foreach($lamaran as $lamaranitem)
        <li>
            @if($lamaranitem->sidejob)
            {{ $lamaranitem->sidejob->nama }}
            @else
            Side job not found
            @endif
            - Status: 
            @if($lamaranitem->status == 'tunda')
            <span class="badge bg-warning">Tunda</span>
            @elseif($lamaranitem->status == 'diterima')
            <span class="badge bg-success">Diterima</span>
            @elseif($lamaranitem->status == 'ditolak')
            <span class="badge bg-danger">Ditolak</span>
            @endif
        </li>
        @endforeach
    </ul>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
