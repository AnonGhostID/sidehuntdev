@extends('layouts.admin')

@section('content')
    <div class="tab-content">
        <!-- Overview Tab -->
        <div class="tab-pane active" id="overview">
            <div class="container">
                <h1 class="judul">Admin</h1>
                <div class="row">
                    <div class="col">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h1 class="card-title">Pengguna</h1>
                                <p class="card-text">{{ $user_count }}</p>
                                <a href="#pengguna" class="btn btn-primary">Pengguna</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h1 class="card-title">Pekerjaan</h1>
                                <p class="card-text">{{ $job_count }}</p>
                                <a href="#pekerjaan" class="btn btn-primary">Pekerjaan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h1 class="card-title">Pelamar</h1>
                                <p class="card-text">{{ $pelamar_count }}</p>
                                <a href="#pelamars" class="btn btn-primary">Pelamar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h1 class="card-title">Transaksi</h1>
                                <p class="card-text">{{ $transaksi_count }}</p>
                                <a href="#transaksi" class="btn btn-primary">Transaksi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengguna Tab -->
        <div class="tab-pane" id="pengguna">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-body">
                                @if($user->isEmpty())
                                    <div class="alert alert-warning" role="alert">
                                        Data pengguna belum tersedia.
                                    </div>
                                @else
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Alamat</th>
                                                <th scope="col">Telpon</th>
                                                <th scope="col">Dompet</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user as $u)
                                                <tr>
                                                    <td>{{ $u->id }}</td>
                                                    <td>{{ $u->nama }}</td>
                                                    <td>{{ $u->email }}</td>
                                                    <td>{{ $u->alamat }}</td>
                                                    <td>{{ $u->telpon }}</td>
                                                    <td>Rp{{ $u->dompet }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.show.profile', $u->id) }}" class="btn btn-sm btn-dark">Detail</a>
                                                        <a href="{{ route('admin.edit.profile', $u->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        @csrf
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $user->links('pagination.custom') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelamars Tab -->
        <div class="tab-pane" id="pelamars">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-body">
                                @if($pelamar->isEmpty())
                                    <div class="alert alert-warning" role="alert">
                                        Data pelamar belum tersedia.
                                    </div>
                                @else
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id</th>
                                                <th scope="col">User</th>
                                                <th scope="col">Job</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pelamar as $t)
                                                <tr>
                                                    <td>{{ $t->id }}</td>
                                                    <td>{{ $t->user ? $t->user->nama : 'N/A' }}</td>
                                                    <td>{{ $t->sidejob ? $t->sidejob->nama : 'N/A' }}</td>
                                                    <td>{{ $t->status }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $pelamar->links('pagination.custom') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Tab -->
        <div class="tab-pane" id="transaksi">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-body">
                                @if($transaksi->isEmpty())
                                    <div class="alert alert-warning" role="alert">
                                        Tidak ada transaksi.
                                    </div>
                                @else
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Pembuat</th>
                                                <th scope="col">Pekerja</th>
                                                <th scope="col">Jumlah</th>
                                                <th scope="col">Dibuat</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transaksi as $t)
                                                <tr>
                                                    <td>{{ $t->kode }}</td>
                                                    <td>{{ $t->pembuat ? $t->pembuat->nama : 'N/A' }}</td>
                                                    <td>{{ $t->pekerja ? $t->pekerja->nama : 'N/A' }}</td>
                                                    <td>{{ $t->jumlah }}</td>
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
                                    {{ $transaksi->links('pagination.custom') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pekerjaan Tab -->
        <div class="tab-pane" id="pekerjaan">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-body">
                                @if($sidejob->isEmpty())
                                    <div class="alert alert-warning" role="alert">
                                        Tidak ada pekerjaan.
                                    </div>
                                @else
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Pekerjaan</th>
                                                <th scope="col">Alamat</th>
                                                <th scope="col">Gaji</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sidejob as $s)
                                                <tr>
                                                    <td>{{ $s->id }}</td>
                                                    <td>{{ $s->nama }}</td>
                                                    <td>{{ $s->alamat }}</td>
                                                    <td>Rp{{ $s->min_gaji }} - Rp{{ $s->max_gaji }}</td>
                                                    <td class="text-center">
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('sidejob.destroy', $s->id) }}" method="POST">
                                                            <a href="{{ route('admin.sidejob.show', $s->id) }}" class="btn btn-sm btn-dark">Detail</a>
                                                            <a href="{{ route('admin.sidejob.edit', $s->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $sidejob->links('pagination.custom') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Message with SweetAlert
        @if (session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif (session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
@endsection
