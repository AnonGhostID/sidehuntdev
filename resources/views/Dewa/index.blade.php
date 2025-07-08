@extends('Dewa.Base.Basic-page')

@section('css')
    <link rel="stylesheet" href="{{auto_asset('Dewa/css/index.css') }}">
@endsection

@section('peta')
    #map{
    height: 800px;
    }
@endsection

@section('content')
<div class="hero-section mb-5">
    <div class="container">
        <div class="row px-3 py-5">
            <div class="col-lg-6">
                <h1 class="container m-md-flex hero-heading mb-3">Temukan pekerjaan yang kamu inginkan!</h1>
                <div class="container-button d-grid gap-5  d-md-flex justify-content-md-start">
                        <button type="submit" onclick="click_Button('/cari')" class="btn btn-warning">Cari Pekerjaan</button>
                        <button type="submit" class="btn btn-warning">Lokasi Anda</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">{{ __('List') }}</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="bg-darker">
                    <tr class="">
                        <th scope="col">Nama</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Lihat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $job)
                        <tr>
                            <td>{{ $job->nama }}</td>
                            <td>{{ $job->alamat }}</td>
                            <td><a href="{{ route('pekerjaan.show', $job->id) }}"
                                    class="btn btn-sm btn-dark">SHOW</a></td>
                        </tr>
                    @empty
                        <div class="alert alert-danger">
                            Data kerja sampingan tidak ada.
                        </div>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="container mt-5 mb-5">
    <div id="map">
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js" charset="utf-8"></script>
        <!-- Load Leaflet from CDN -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

        <!-- Load Esri Leaflet from CDN -->
        <script src="https://unpkg.com/esri-leaflet@3.0.12/dist/esri-leaflet.js"></script>
        <script src="https://unpkg.com/esri-leaflet-vector@4.2.3/dist/esri-leaflet-vector.js"></script>

        <!-- Load Esri Leaflet Geocoder from CDN -->
        <link rel="stylesheet"
            href="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.css">
        <script src="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.js"></script>

        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js" charset="utf-8"></script>
        <script>
            const apiKey =
                "AAPK3e52398025234807add84f416a03c213CPb7ak6zNzwQYIBhQ9PIx-oBY_1mtsbVR1klbU-RrJ6TWtK5mP28C-lfmNqfndnS";

            const basemapEnum = "arcgis/navigation";

            const map = L.map("map", {
                minZoom: 2
            })

            // create control and add to map
            var lc = L.control.locate().addTo(map);

            // request location update and set location
            lc.start();

            map.setView([-2.526, 117.905], 5);

            L.esri.Vector.vectorBasemapLayer(basemapEnum, {
                apiKey: apiKey
            }).addTo(map);


            const searchControl = L.esri.Geocoding.geosearch({
                position: "topright",
                placeholder: "Cari alamat anda",
                useMapBounds: false,

                providers: [
                    L.esri.Geocoding.arcgisOnlineProvider({
                        apikey: apiKey,
                    })
                ]

            }).addTo(map);
        </script>
    </div>
</div>
@endsection

@section('script')
<script>
    function click_Button(link){
        window.location.href = link;   
    }

    @if(session('alert'))
        let split = "{{session('alert')}}".split('|');
        NotifyAlert(split[0], split[1],0);
    @endif

    function NotifyAlert(titled, message, type=null){
        let img = null;
        if(type==null){
            type==0?img="{{ asset('img/success.svg')}}":img="{{ asset('img/failed.svg')}}";
        }
        Swal.fire({
            title: titled,
            text: msg,
            imageUrl: img,
            imageWidth: 100,
            imageHeight: 100,
            imageAlt: "Custom image",
            width: 300,
            height: 400,
        });
    }
</script>
@endsection