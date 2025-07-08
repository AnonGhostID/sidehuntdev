@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <h1 class="text-center display-5 fw-bold mb-5">Pekerjaan yang Terbuat</h1>
                    {{-- <hr> --}}
                </div>
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <a href="{{ route('sidejob.create') }}" class="btn btn-md btn-success mb-3">Daftarkan Pekerjaan</a>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col">Gaji</th>
                                    <th scope="col" style="width: 20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sidejob as $sidejob)
                                    <tr>
                                        <td>{{ $sidejob->nama }}</td>
                                        <td>{{ $sidejob->alamat }}</td>
                                        <td>Rp{{ $sidejob->min_gaji }} - Rp{{ $sidejob->max_gaji}}</td>
                                        @auth
                                        <td class="text-center">
                                            <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('sidejob.destroy', $sidejob->id) }}" method="POST">
                                                <a href="{{ route('sidejob.detail', $sidejob->id) }}" class="btn btn-sm btn-dark">SHOW</a>
                                                <a href="{{ route('sidejob.edit', $sidejob->id) }}" class="btn btn-sm btn-primary">EDIT</a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">HAPUS</button>
                                            </form>
                                        </td>
                                        @endauth
                                    </tr>
                                @empty
                                    <div class="alert alert-danger">
                                        Tidak ada pekerjaan sampingan yang anda buat.
                                    </div>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- {{ $sidejob->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <script>
        //message with sweetalert
        @if(session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif(session('error'))
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