@extends('Dewa.Base.Basic-page')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    
</style>
@endsection
@section('peta')

#map{
height: 400px;
width: 100%;
margin: 0;
padding: 0;
}

@endsection


@section('add-onn')

@endsection

@section('content')
<div class="w-100 d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
    <h1 class="text-danger">
        MAAF, AKSES DITOLAK!
    </h1>
    <h2>
        Halaman ini tidak bisa diakses oleh status anda saat ini
    </h2>
</div>
@endsection

@section('script')

@endsection