@extends('Dewa.Base.Basic-page')

@section('css')
    <link rel="stylesheet" href="{{ auto_asset('Dewa/css/login.css') }}">
         <!-- Bootstrap 5 JS (dari CDN) -->

@endsection

@section('content')

<div class="container login">
    <h1>Login</h1>

    <form action="/Login_account" method="POST">

        @csrf
        <label for="">Alamat Email*</label>
        <input type="email" name="email" maxlength="40" value="{{ old('email') }}" placeholder="Masukkan Email anda">
        <label for="">Password*</label>
        <input type="password" class="inp password" maxlength="15" name="password" value="{{ old('password') }}" placeholder="Masukkan Password"
            data-bs-toggle="popover"
            data-bs-trigger="hover focus"
            data-bs-content="Password harus minimal (8 karakter, 1 angka, 1 huruf Kapital, 1 huruf Kecil)" type="checkbox" role="switch" id="LihatKataSandi">
        <div class="setara see-password form-check form-switch">
            <input class="form-check-input appearance-none focus:outline-none" onclick="see_password()">
            <p>Lihat kata sandi</p>
        </div>
        <!-- <div class="setara see-password">
            <input type="checkbox" onclick="see_password()" name="" id="">
            <p>Lihat kata sandi</p>
        </div> -->
        <div class="setara forget-password">
            <p>Lupa kata sandi?</p><a href=""><p>Klik Disini</p></a>
        </div>
        <div class="submit-button">
            <!-- <button type="submit" class="btn btn-primary" data-bs-toggle="modal" >Login</button> -->
            <button type="submit" class="btn btn-warning" data-bs-target="#AlertModal">Login</button>
            

        </div>
        <div class="setara register">
            <p>Belum punya akun?</p><a href="/Register"><p>Register</p></a>
        </div>
    </form>
        

</div>

@endsection

@section('script')

<script>
    function see_password(){
        let inp = document.querySelector('.inp.password')
        if(inp.getAttribute('type')=='password'){
            inp.setAttribute('type','text')
        }
        else{
            inp.setAttribute('type','password')
        }
    }
    
        
</script>
@endsection