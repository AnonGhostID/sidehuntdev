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
    <div class="container-fluid py-5 mt-2 justify-content-center w-50">
        @csrf
        <h1 class="text-center display-5 fw-bold mb-5">Tambah Pekerjaan</h1>
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('sidejob.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama :</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                            value="{{ old('nama') }}" placeholder="Masukkan Nama Pekerjaan">

                        @error('title')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Alamat Penyedia :</label>
                        <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="findbox"
                            name="alamat" value="{{ old('alamat') }}"
                            placeholder="Masukkan alamat pekerjaan yang tersedia">
                        @error('alamat')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="mt-3" id="map">
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
                            <script>
                                const apiKey =
                                    "AAPK3e52398025234807add84f416a03c213CPb7ak6zNzwQYIBhQ9PIx-oBY_1mtsbVR1klbU-RrJ6TWtK5mP28C-lfmNqfndnS";

                                const basemapEnum = "arcgis/navigation";

                                const map = L.map("map", {
                                    minZoom: 2
                                })

                                function onLocationFound(e) {
                                    L.marker(e.latlng).addTo(map);
                                    var coords = document.querySelector("[name=koordinat]")
                                    var latitude = document.querySelector("[name=latitude]")
                                    var longitude = document.querySelector("[name=longitude]")
                                    var lat = e.latlng.lat
                                    var lng = e.latlng.lng

                                    coords.value = lat + "," + lng
                                    latitude.value = lat,
                                        longitude.value = lng
                                }

                                function onLocationError(e) {
                                    alert(e.message);
                                }

                                map.on('locationerror', onLocationError);
                                map.on('locationfound', onLocationFound);
                                map.locate({
                                    setView: true,
                                    maxZoom: 16
                                });
                                var marker = L.marker([0, 0], {}).addTo(map);

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

                                function onMapClick(e) {
                                    var coords = document.querySelector("[name=koordinat]")
                                    var latitude = document.querySelector("[name=latitude]")
                                    var longitude = document.querySelector("[name=longitude]")
                                    var lat = e.latlng.lat
                                    var lng = e.latlng.lng

                                    if (!marker) {
                                        marker = L.marker(e.latlng).addTo(map)
                                    } else {
                                        marker.setLatLng(e.latlng)
                                    }

                                    coords.value = lat + "," + lng
                                    latitude.value = lat,
                                        longitude.value = lng
                                }
                                map.on('click', onMapClick)

                                const results = L.layerGroup().addTo(map);

                                searchControl.on("results", (data) => {
                                    results.clearLayers();

                                    for (let i = data.results.length - 1; i >= 0; i--) {
                                        const marker = L.marker(data.results[i].latlng);
                                        var coords = document.querySelector("[name=koordinat]")
                                        var latitude = document.querySelector("[name=latitude]")
                                        var longitude = document.querySelector("[name=longitude]")
                                        var lat = data.results[i].latlng.lat
                                        var lng = data.results[i].latlng.lng

                                        marker.bindPopup(`<b>${lat},${lng}</b><p>${data.results[i].properties.LongLabel}</p>`);

                                        results.addLayer(marker);

                                        marker.openPopup();
                                        coords.value = lat + "," + lng
                                        latitude.value = lat,
                                            longitude.value = lng
                                    }

                                });
                            </script>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" class="form-control" name="koordinat" id="coordinate">
                    </div>
                    <div class="form-group">
                        <input type="hidden" class="form-control" name="latitude" id="latitude">
                    </div>
                    <div class="form-group">
                        <input type="hidden" class="form-control" name="longitude" id="longitude">
                    </div>

                    <div class="form-group mb-3">
                        <div class="container-flex">
                            <div class="row">
                                <div class="col">
                                    <label class="font-weight-bold">Minimal Gaji :</label>
                                    <input type="number" class="form-control @error('min_gaji') is-invalid @enderror"
                                        name="min_gaji" value="{{ old('min_gaji') }}" placeholder="Minimal gaji pekerjaan">

                                    @error('min_gaji')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label class="font-weight-bold">Gaji Maksimal :</label>
                                    <input type="number" class="form-control @error('max_gaji') is-invalid @enderror"
                                        name="max_gaji" value="{{ old('max_gaji') }}" placeholder="Maksimal gaji pekerjaan">

                                    @error('max_gaji')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Pekerja diterima :</label>
                        <input type="number" class="form-control @error('max_pekerja') is-invalid @enderror"
                            name="max_pekerja" value="{{ old('max_pekerja') }}"
                            placeholder="Jumlah pekerja yang bisa diterima">
                        @error('max_pekerja')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Deskripsi Pekerjaan</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" rows="5"
                            placeholder="Masukkan Deskripsi Pekerjaan">{{ old('deskripsi') }}</textarea>

                        <!-- error message untuk description -->
                        @error('deskripsi')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="my-button me-3 align-item-center">Tambahkan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{--
<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('deskripsi');
</script> --}}
@endsection
