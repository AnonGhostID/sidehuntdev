@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Alamat</th>
                                <th scope="col">Lihat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($hasil) > 0)
                            @foreach($hasil as $item)
                            <tr>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td><a href="{{ route('sidejob.show', $item->id) }}"
                                        class="btn btn-sm btn-dark">SHOW</a></td>
                            </tr>
                            @endforeach
                            @else
                            <div class="alert alert-danger">
                                <p>Tidak ada hasil</p>
                            </div>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endsection