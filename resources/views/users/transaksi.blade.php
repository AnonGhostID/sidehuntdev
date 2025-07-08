@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <h1>Dompet anda</h1>
                        <h2>Rp{{$user->dompet}}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col">
                    <div class="card border-0 shadow-sm rounded">
                        <div class="card-body">
                            <h1>Transaksi</h1>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Pembuat</th>
                                        <th>Pekerja</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi as $t)
                                        <tr>
                                            <td>{{ $t->kode }}</td>
                                            <td>{{ $t->pembuat ? $t->pembuat->nama : 'N/A' }}</td>
                                            <td>{{ $t->pekerja ? $t->pekerja->nama : 'N/A' }}</td>
                                            <td>{{ $t->jumlah }}</td>
                                            <td>{{ $t->status }}</td>
                                            <td>{{ $t->dibuat }}</td>
                                            <td class="text-center">
                                                @if($t->status === 'tunda')
                                                    <a href="{{ route('admin.transaksi.setuju', $t->kode) }}" class="btn btn-sm btn-primary">Verifikasi</a>
                                                    <a href="{{ route('admin.transaksi.tolak', $t->kode) }}" class="btn btn-sm btn-danger">Batalkan</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        @endsection
