@extends('layouts.app')

@section('peta')
    #map{
    height: 400px;
    width: 100%;
    margin: 0;
    padding: 0;
    }
@endsection

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <h1 class="display-2">{{ $sidejob->nama }}</h1>
                        <h3 class="display-4">Rp{{ $sidejob->min_gaji }} - Rp{{ $sidejob->max_gaji }}</h1>
                            <hr>
                            <h4>Deskripsi</h4>
                            <p>{{ $sidejob->deskripsi }}</p>
                            <h4>Lokasi Pekerjaan</h4>
                            <p>{{ $sidejob->alamat }}</p>
                            <div id="map">
                                <link rel="stylesheet"
                                    href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
                                <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js" charset="utf-8"></script>
                                <!-- Load Leaflet from CDN -->
                                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                                    crossorigin="" />
                                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

                                <!-- Load Esri Leaflet from CDN -->
                                <script src="https://unpkg.com/esri-leaflet@3.0.12/dist/esri-leaflet.js"></script>
                                <script src="https://unpkg.com/esri-leaflet-vector@4.2.3/dist/esri-leaflet-vector.js"></script>

                                <!-- Load Esri Leaflet Geocoder from CDN -->
                                <link rel="stylesheet"
                                    href="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.css">
                                <script src="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.js"></script>
                                <script>
                                    const apiKey =
                                        "AAPK3e52398025234807add84f416a03c213CPb7ak6zNzwQYIBhQ9PIx-oBY_1mtsbVR1klbU-RrJ6TWtK5mP28C-lfmNqfndnS";

                                    const basemapEnum = "arcgis/navigation";

                                    const map = L.map("map", {
                                        minZoom: 2,
                                        center: [{{ $sidejob->koordinat }}]
                                    })

                                    var marker = L.marker([{{ $sidejob->koordinat }}]).addTo(map);

                                    map.setView([{{ $sidejob->koordinat }}], 16);

                                    L.esri.Vector.vectorBasemapLayer(basemapEnum, {
                                        apiKey: apiKey
                                    }).addTo(map);
                                </script>
                            </div>
                            {{-- Untuk yang log in adalah pembuat --}}
                            @if (auth()->check())
                                @php
                                    $userApplied = app('App\Models\Pelamar')
                                        ->where('job_id', $sidejob->id)
                                        ->where('user_id', auth()->id())
                                        ->exists();
                                @endphp
                                @if ($sidejob->pembuat == auth()->id())
                                    <div class="btn-group">
                                        <a href="{{ route('sidejob.edit', $sidejob) }}" class="btn btn-primary">Edit</a>
                                        <form action="{{ route('sidejob.destroy', $sidejob) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                    {{-- Untuk yang login yang bukan pembuat dan belum daftar ke pekerjaan tersebut --}}
                                @elseif(!$userApplied)
                                    <form action="{{ route('sidejob.buatPermintaan', $sidejob) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Daftar Pekerjaan</button>
                                    </form>
                                @else
                                    {{-- Untuk yang membuat lamaran menunggu status --}}
                                    @php
                                        $userPelamar = $pelamar->where('user_id', auth()->id())->first();
                                    @endphp
                                    @if ($userPelamar->status == 'tunda')
                                        <p>Anda sudah melamar, tunggu pembuat membuat pilihan untuk terima atau tolak.</p>
                                    @elseif($userPelamar->status == 'diterima')
                                        <p>Anda sudah diterima oleh pembuat.</p>
                                    @elseif($userPelamar->status == 'ditolak')
                                        <p>Anda sudah ditolak oleh pembuat.</p>
                                    @endif
                                @endif
                                {{-- Guest --}}
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">Login untuk Melamar</a>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <h1>Pembuat : {{ $pembuat->nama }}</h1>
                        <h2>Telepon : {{ $pembuat->telpon }}</h2>
                        <h2>Alamat  : {{ $pembuat->alamat }}</h2>
                        <a href="{{ route('user.profile', $pembuat->id) }}">
                            <button class="btn btn-primary">Profile</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col">
                @if ($sidejob->pembuat == auth()->id())
                    <div class="card border-0 shadow-sm rounded">
                        <div class="card-body">
                            <h4>Daftar Pelamar:</h4>

                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="semua-tab" data-bs-toggle="tab" href="#semua"
                                        role="tab" aria-controls="semua" aria-selected="true">Semua Pelamar</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="disetujui-tab" data-bs-toggle="tab" href="#disetujui"
                                        role="tab" aria-controls="disetujui" aria-selected="false">Pelamar
                                        Disetujui</a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="semua" role="tabpanel"
                                    aria-labelledby="semua-tab">
                                    @if ($pelamar->isEmpty())
                                        <div class="alert alert-warning" role="alert">
                                            Belum ada pelamar untuk pekerjaan ini.
                                        </div>
                                    @else
                                        <ul>
                                            @foreach ($pelamar as $pelamaritem)
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col">
                                                            <p>{{ $pelamaritem->user->nama }}</p>
                                                        </div>
                                                        <div class="col">
                                                            <div class="btn-group justify-content-end">
                                                                @if ($pelamaritem->status == 'tunda')
                                                                    <form
                                                                        action="{{ route('pelamar.terima', $pelamaritem) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit"
                                                                            class="btn btn-success">Terima</button>
                                                                    </form>
                                                                    <form
                                                                        action="{{ route('pelamar.tolak', $pelamaritem) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Tolak</button>
                                                                    </form>
                                                                @else
                                                                    <p>Status: {{ $pelamaritem->status }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="tab-pane fade" id="disetujui" role="tabpanel" aria-labelledby="disetujui-tab">
                                    @php $pelamarDiterima = $pelamar->where('status', 'diterima'); @endphp
                                    @if ($pelamarDiterima->isEmpty())
                                        <div class="alert alert-warning" role="alert">
                                            Belum ada pelamar yang disetujui.
                                        </div>
                                    @else
                                        <ul>
                                            @foreach ($pelamarDiterima as $pelamaritem)
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col">
                                                            <p>{{ $pelamaritem->user->nama }}</p>
                                                        </div>
                                                        <div class="col">
                                                            <form
                                                                action="{{ route('transaksi.buat', $pelamaritem->user->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="input-group">
                                                                    <input type="number" name="jumlah"
                                                                        class="form-control" placeholder="Jumlah Gaji"
                                                                        required>
                                                                    <button type="submit" class="btn btn-primary">Buat
                                                                        Transaksi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
