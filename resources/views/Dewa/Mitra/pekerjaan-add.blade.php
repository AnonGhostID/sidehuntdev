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
@section('content')
<div class="container-fluid py-5 mt-2 justify-content-center diff-width">
    @csrf
    <h1 class="text-center display-5 fw-bold mb-5">Tambahkan Lowongan Pekerjaan</h1>
    <div class="row">
        <div class="col-md-12">
            <form action="/kerja/add" method="POST" class="w-100 font-responsive"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Nama Pekerjaan*:</label>
                    <input type="text"
                        class="nama-pekerjaan form-control shadow @error('max_gaji') is-invalid @enderror"
                        maxlength="60" name="nama" value="{{ old('nama') }}" placeholder="Masukkan Nama Pekerjaan">
                    @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="hidden" class="form-control shadow" name="koordinat" id="coordinate"
                        value="{{ old('koordinat') }}">
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control shadow" name="latitude" id="latitude"
                        value="{{ old('latitude') }}">
                </div>
                <div class="form-group">
                    <input type="hidden" class="form-control shadow" name="longitude" id="longitude"
                        value="{{ old('longitude') }}">
                </div>

                <div class="form-group mb-3">
                    <div class="container-flex">
                        <div class="row">
                            <div class="col">
                                <label class="font-weight-bold">Minimal Gaji* :</label>
                                <input type="number"
                                    class="form-control  shadow @error('min_gaji') is-invalid @enderror"
                                    name=" min_gaji" maxlength="20" value="{{ old('min_gaji') }}"
                                    placeholder="Minimal gaji pekerjaan" maxlength="20">

                                @error('min_gaji')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="font-weight-bold @error('max_gaji') is-invalid @enderror">Gaji Maksimal*
                                    :</label>
                                <input type="number" class="form-control shadow @error('max_gaji') is-invalid @enderror"
                                    name="max_gaji" maxlength="20" value="{{ old('max_gaji') }}"
                                    placeholder="Maksimal gaji pekerjaan">

                                @error('max_gaji')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="container-flex">
                        <div class="row">
                            <div class="col">
                                <label class="font-weight-bold">Tanggal Dimulai Kerja* :</label>
                                <input type="datetime-local"
                                    class="form-control  shadow @error('start_job') is-invalid @enderror"
                                    name="start_job" maxlength="20" value="{{ old('start_job') }}"
                                    placeholder="Tanggal Dimulai Kerja" maxlength="20">

                                @error('start_job')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="font-weight-bold @error('end_job') is-invalid @enderror">Tanggal Berakhir
                                    Kerja* :</label>
                                <input type="datetime-local"
                                    class="form-control shadow @error('end_job') is-invalid @enderror" name="end_job"
                                    maxlength="20" value="{{ old('end_job') }}" placeholder="Tanggal Selesai Kerja">

                                @error('end_job')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>



                <div class="form-group mb-3">
                    <label class="font-weight-bold">Pekerja diterima* :</label>
                    <input type="number" class="form-control shadow @error('max_pekerja') is-invalid @enderror"
                        name="max_pekerja" maxlength="10" value="{{ old('max_pekerja') }}"
                        placeholder="Jumlah pekerja yang bisa diterima">
                    @error('max_pekerja')
                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label class="font-weight-bold">Alamat Pekerjaan* :</label>
                    <input type="text" class="alamat form-control shadow @error('alamat') is-invalid @enderror"
                        id="findbox" name="alamat" value="{{ old('alamat') }}" maxlength="100"
                        placeholder="Masukkan alamat pekerjaan yang tersedia">
                    @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <label class="font-weight-bold mt-3">Petunjuk lokasi* : </label>
                    <p class="clear-p ps-2" style="font-size: 10px;">(misalnya: dekat masjid, dekat Alfamart, atau
                        bangunan lain yang mudah dikenali)</p>
                    <input type="text"
                        class="alamat form-control shadow mt-0 @error('petunjuk_alamat') is-invalid @enderror"
                        maxlength="100" id="findbox" name="petunjuk_alamat" value="{{ old('alamat') }}"
                        placeholder="Masukkan alamat pekerjaan yang tersedia">

                    @error('petunjuk_alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <label class="font-weight-bold mt-3">Tandai Lokasi Pekerjaan Anda* :</label>
                    <div class=" shadow" id="map" data-bs-toggle="popover">
                        <link rel="stylesheet"
                            href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
                        <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js"
                            charset="utf-8"></script>
                        <!-- Load Leaflet from CDN -->
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

                        <!-- Load Esri Leaflet from CDN -->
                        <script src="https://unpkg.com/esri-leaflet@3.0.12/dist/esri-leaflet.js"></script>
                        <script src="https://unpkg.com/esri-leaflet-vector@4.2.3/dist/esri-leaflet-vector.js"></script>

                        <!-- Load Esri Leaflet Geocoder from CDN -->
                        <link rel="stylesheet"
                            href="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.css">
                        <script
                            src="https://unpkg.com/esri-leaflet-geocoder@3.1.4/dist/esri-leaflet-geocoder.js"></script>

                        <link rel="stylesheet"
                            href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
                        <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js"
                            charset="utf-8"></script>

                        <script>
                            const apiKey = "AAPK3e52398025234807add84f416a03c213CPb7ak6zNzwQYIBhQ9PIx-oBY_1mtsbVR1klbU-RrJ6TWtK5mP28C-lfmNqfndnS";
                            const basemapEnum = "arcgis/navigation";

                            const map = L.map("map", { minZoom: 10 });

                            let marker = null;

                            var lc = L.control.locate({
                                maxZoom: 22,
                                setView: true,
                                initialZoom: 22,
                                locateOptions: { enableHighAccuracy: true }
                            }).addTo(map);

                            lc.start();

                            function onLocationFound(e) {
                                const { lat, lng } = e.latlng;
                                // updateMarker(lat, lng);
                            }

                            function onLocationError(e) {
                                alert(e.message);
                            }

                            map.on('locationerror', onLocationError);
                            map.on('locationfound', onLocationFound);

                            map.locate({ setView: true, maxZoom: 19 });

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

                            map.on('click', function (e) {
                                const { lat, lng } = e.latlng;
                                updateMarker(lat, lng);
                            });

                            function updateMarker(lat, lng, popupText = "") {
                                const coords = document.querySelector("[name=koordinat]");
                                const latitude = document.querySelector("[name=latitude]");
                                const longitude = document.querySelector("[name=longitude]");

                                if (!marker) {
                                    marker = L.marker([lat, lng]).addTo(map);
                                } else {
                                    marker.setLatLng([lat, lng]);
                                }

                                if (popupText) {
                                    marker.bindPopup(`<b>${lat},${lng}</b><p>${popupText}</p>`).openPopup();
                                }

                                coords.value = `${lat},${lng}`;
                                latitude.value = lat;
                                longitude.value = lng;
                                // setInput(lat, lng);
                            }
                        </script>

                    </div>
                    @error('koordinat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Deskripsi Pekerjaan*</label>
                    <textarea name="deskripsi" class=" shadow @error('deskripsi') is-invalid @enderror" maxlength="1000"
                        id="deskripsi" value="">{!! old('deskripsi') !!}</textarea>

                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Kriteria Pekerjaan*</label>
                    <p class="clear-p ps-2" style="font-size: 10px;">minimal 3 kriteria</p>
                    <select class="form-control shadow @error('kriteria') is-invalid @enderror" id="kriteria"
                        name="kriteria[]" multiple="multiple" aria-label="Default select example">

                        @foreach($kriteria as $k)
                        <option value="{{ $k->id }}" @selected(in_array($k->nama, (array) old('kriteria', [])))>
                            {{ $k->nama }}
                        </option>
                        @endforeach
                    </select>

                    @error('kriteria')
                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold @error('deadline_job') is-invalid @enderror">Tanggal Lowongan
                        Berakhir :</label>
                    <input type="datetime-local" class="form-control shadow @error('deadline_job') is-invalid @enderror"
                        name="deadline_job" maxlength="20" value="{{ old('deadline_job') }}"
                        placeholder="Tanggal Selesai Kerja">
                    @error('deadline_job')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="formFileSm" class="form-label">Foto Pekerjaan</label>
                    <div class="d-flex flex-row gap-1">
                        <input class="form-control shadow @error('foto_job') is-invalid @enderror" name="foto_job"
                            id="formFileSm" type="file" aria-expanded="false" aria-controls="previewFotoCollapse"
                            accept=".jpg, .jpeg, .png, .img, image/jpeg, image/png">
                        <!-- <input class="btn btn-primary form-control-sm" type="button" onclick="resetFoto()" value="Reset Foto" style="height: 75%;"> -->
                        <button class="btn btn-dark p-clear">
                            <p class="clear-p" onclick="resetFoto(event)" style="font-size:10px !important; ">Reset</p>
                        </button>
                    </div>
                    @error('foto_job')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                </div>
                <div id="previewFotoCollapse"
                    class="collapse w-100 flex-column justify-content-between align-items-start"
                    style="height: fit-content; min-height: 100px; max-height: fit-content;">
                    <label for="">Preview Foto</label>
                    <div class="w-100 d-flex form-control"
                        style="height: 100%; min-height: 100%; max-height: fit-content;">
                        <div class="w-100 h-auto d-flex flex-column gap-2">
                            <div class="w-auto h-100 rounded-2">
                                <img class="preview-foto rounded-2"
                                    src="https://i.pinimg.com/736x/54/3c/31/543c3130fba0be6cfda40c0db5fe74c1.jpg"
                                    style="width: 100%; height: 100%;  object-fit: cover;" alt="">
                            </div>
                            <div class="col-12 rounded-2 col-md-6 col-lg-3 h-auto">
                                <a href=""
                                    class=" w-100 h-100 job text-decoration-none d-flex flex-row p-0 gap-0 border-2 border-black rounded-2 align-items-center justify-content-between"
                                    style="background-color: #CDCDCD !important;">
                                    <div class="rounded-start-2 justify-content-start align-items-center"
                                        style="width: 40%; min-width: 40%; height: 100%;">
                                        <img class="preview-foto rounded-start-2"
                                            src="https://i.pinimg.com/736x/54/3c/31/543c3130fba0be6cfda40c0db5fe74c1.jpg"
                                            style="width: 100%; height: 100%;  object-fit: cover;" alt="">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center text-truncate align-items-start gap-0 flex-grow-1 h-100 p-2"
                                        style=" max-width: 100%;">
                                        <p class="judul-pekerjaan clear-p fw-bolder text-truncate w-100"
                                            style="font-size: 12px; color: #2E2D2C;">Pekerjaan</p>
                                        <p class="clear-p  opacity-50" style="font-size: 10px; color: #2E2D2C;">5 Jam
                                            yang lalu</p>
                                        <p class="clear-p opacity-50" style="font-size: 10px; color: #2E2D2C;">1,4 km
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100 d-flex justify-content-center mt-3 align-items-center">
                    <button type="submit" class="btn btn-warning">Tambahkan</button>
                </div>
                @if ($errors->any())
                <div>
                    @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#kriteria').select2({
            tags: true,
            tokenSeparators: [','],
            placeholder: 'Pilih atau ketik kriteria',
            allowClear: true
        });
    });
</script>

<script>
    // Ambil elemen
    const fileInput = document.getElementById('formFileSm');
    const previewCollapse = document.getElementById('previewFotoCollapse');
    const collapseInstance = new bootstrap.Collapse(previewCollapse, { toggle: false }); // toggle: false = manual

    // Event perubahan file
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length > 0) {
            // Set aria-expanded
            fileInput.setAttribute('aria-expanded', 'true');
            let txt = document.querySelector('.judul-pekerjaan')
            let inp_nama = document.querySelector('.nama-pekerjaan')
            console.log('tes', (inp_nama.value == ""))
            if (inp_nama.value != "") {
                txt.textContent = inp_nama.value
            }

            // Tampilkan preview
            collapseInstance.show();

            // Ganti gambar
            const imgPreview = previewCollapse.querySelectorAll('.preview-foto');
            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                imgPreview.forEach(f => {
                    f.src = e.target.result;
                });
            };

            reader.readAsDataURL(file);
        } else {
            fileInput.setAttribute('aria-expanded', 'false');
            collapseInstance.hide();
        }
    });

    // Fungsi reset
    function resetFoto(event) {
        event.preventDefault();
        fileInput.value = '';
        fileInput.setAttribute('aria-expanded', 'false');
        collapseInstance.hide();
    }


    async function reverseGeocode(lat, lon) {
        console.log(lat, lon, 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`')
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`);
        const data = await response.json();
        if (data && data.display_name) {
            return data.display_name;
        } else {
            return 'Tidak ditemukan';
        }
    }



</script>
<script src="https://cdn.tiny.cloud/1/s7v4jgdffqnfid26zu23r6rcrnqznthr24xqypaqr2i91gz5/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
<!-- <script src="https://cdn.tiny.cloud/1/s7v4jgdffqnfid26zu23r6rcrnqznthr24xqypaqr2i91gz5/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> -->


<!-- Inisialisasi TinyMCE -->
<script>
    tinymce.init({
        selector: 'textarea#deskripsi',
        height: 300,
        menubar: false,
        plugins: 'lists link preview',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link preview',
        branding: false,

        setup: function (editor) {
            const maxChars = 1000;

            editor.on('keydown', function (e) {
                const content = editor.getContent({ format: 'text' });
                if (content.length >= maxChars && ![8, 37, 38, 39, 40, 46].includes(e.keyCode)) {
                    e.preventDefault();
                }
            });

            editor.on('paste', function (e) {
                const clipboard = (e.clipboardData || window.clipboardData).getData('text');
                const content = editor.getContent({ format: 'text' });
                if (content.length + clipboard.length > maxChars) {
                    e.preventDefault();
                    editor.notificationManager.open({
                        text: 'Maksimum ' + maxChars + ' karakter.',
                        type: 'warning'
                    });
                }
            });
        }
    });
    document.addEventListener('focusin', (e) => {
        if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
            e.stopImmediatePropagation();
        }
    });
</script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => console.error(error));
</script>
@endsection