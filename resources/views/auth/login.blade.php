@extends('layouts.app')

@section('content')
<div class="container-fluid py-5 mt-2 justify-content-center w-50">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h1 class="text-center display-5 fw-bold mb-5">Login</h1>
        <div class="mb-3">
            <label for="email" class="mb-3">Email </label>
            <div class="col">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" autofocus>
    
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="mb-3">Password</label>
            <div class="col">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div>
            {{-- <input type="checkbox" class="" onclick="">Tampilkan Password --}}
            <div class="col mt-3">
                <p>Lupa Kata Sandi? <a href="{{(route('password.request'))}}">Klik Disini</a></p>
                    <button class="my-button">Login</button>
                <p class="mt-3">Belum Punya Akun? <a href="{{(route('register'))}}">Register</a></p>
            </div>
        </div>
    </form>
</div>
@endsection