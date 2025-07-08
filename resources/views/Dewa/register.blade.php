@extends('Dewa.Base.Basic-page')

@section('css')
<!-- <link rel="stylesheet" href="{{asset('css/login.css')}}"> -->
<link rel="stylesheet" href="{{ auto_asset('Dewa/css/login.css') }}">
@endsection

@section('peta')
@endsection

@section('content')
<div class="container login">
    <h1>Buat Akun</h1>

    <form action="/Register_account" method="POST" class="w-auto">

        @csrf
        <div class="d-flex flex-column h-auto gap-3 w-100 justify-content-between">
            <label for="">Mendaftar sebagai siapa?*</label>
            <select class="form-select selectform shadow-none @error('role') is-invalid @enderror" aria-label="Default select example">
                <option value="1" {{{ old('role') == 'mitra' ? 'selected' : '' }}}>Mitra (Pemberi Kerja)</option>
                <option value="2"{ {{ old('role') == 'mitra' ? 'selected' : '' }}} selected>User (Pencari Kerja)</option>
            </select>
        </div>
        <div class="d-flex flex-row h-auto gap-3 w-100 justify-content-between">
            <div class="d-flex flex-column w-50">
                <label for="">Nama Depan*</label>
                <input type="text" name="nama-depan" maxlength="10" placeholder="john"
                    class="w-100 @error('nama-depan') is-invalid @enderror" value="{{ old('nama-depan') }}">
                @error('nama-depan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex flex-column w-50">
                <label for="">Nama Belakang*</label>
                <input type="text" name="nama-belakang" maxlength="30" placeholder="doe"
                    class="w-100 @error('nama-belakang') is-invalid @enderror" value="{{ old('nama-belakang') }}">
                @error('nama-belakang')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <label for="">Alamat Email*</label>
        <input type="email" name="email" maxlength="40" placeholder="Masukkan Email anda"
            class="w-100 @error('email') is-invalid @enderror" value="{{ old('email') }}">
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="">Password*</label>
        <input type="password" class="password w-100 @error('password') is-invalid @enderror" name="password"
            maxlength="15" placeholder="Masukkan Password" data-bs-toggle="popover" data-bs-trigger="hover focus"
            data-bs-content="Password harus minimal (8 karakter, 1 angka, 1 huruf Kapital, 1 huruf Kecil)">
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="">Verifikasi Password*</label>
        <input type="password" maxlength="15" class="password w-100 @error('password-retype') is-invalid @enderror"
            name="password-retype" placeholder="Masukkan Password">
        @error('password-retype')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="setara see-password form-check form-switch">
            <input class="form-check-input appearance-none focus:outline-none" onclick="see_password()" type="checkbox"
                role="switch" id="LihatKataSandi">
            <p>Lihat kata sandi</p>
        </div>

        <div class="submit-button">
            <button type="submit" class="btn btn-warning">Register</button>
        </div>

    </form>
</div>
@endsection

@section('script')


<script>
    // function submit_form(elemen,event){
    //     event.preventDefault();
    //     let form = elemen.closest('form')
    //     if(form.submit){
    //         console.log('masuk')
    //     }
    // }

    function see_password() {
        let form = document.querySelector('form')
        let inp = form.querySelectorAll('.password');

        inp.forEach(element => {
            if (element.getAttribute('type') == 'password') {
                element.setAttribute('type', 'text')
            }
            else {
                element.setAttribute('type', 'password')
            }

        });
    }


</script>
@endsection