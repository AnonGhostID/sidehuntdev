@extends('layouts.polos')

@section('content')
    <div class="container-fluid py-5 mt-2 justify-content-center w-50">
        <h1 class="text-center display-5 fw-bold mb-5">Edit</h1>
        <div class="row">

            <form action="{{ route('admin.update.profile', $user->id) }}" method="PUT" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Nama</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                        value="{{ old('nama', $user->nama) }}" placeholder="Masukkan Nama Pekerjaan Sampingan">

                    <!-- error message untuk title -->
                    @error('title')
                        <div class="alert alert-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email', $user->email) }}" placeholder="Masukkan email">

                    @error('email')
                        <div class="alert alert-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Alamat</label>
                    <input type="text" class="form-control @error('alamat') is-invalid @enderror" name="alamat"
                        value="{{ old('alamat', $user->alamat) }}" placeholder="Masukkan alamat user">
                    @error('alamat')
                        <div class="alert alert-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Nomor Telpon</label>
                    <input type="number" class="form-control @error('telpon') is-invalid @enderror" name="telpon"
                        value="{{ old('telpon', $user->telpon) }}" placeholder="Masukkan nomor telpon">
                    @error('telpon')
                        <div class="alert alert-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Dompet</label>
                    <input type="number" class="form-control @error('dompet') is-invalid @enderror" name="dompet"
                        value="{{ old('dompet', $user->dompet) }}" placeholder="">
                    @error('dompet')
                        <div class="alert alert-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-md btn-primary me-3">Simpan</button>
                <button type="reset" class="btn btn-md btn-warning">RESET</button>
            </form>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
