@extends('Dewa.Base.Basic-page')

@section('css')
<style>
    .job {
        background-color: #CDCDCD !important;
    }

    .img-container {
        overflow: hidden;
        position: relative;
    }

    .img-custom {
        object-fit: fill;
        object-position: center;
        scale: 1;
        display: block;
        width: 100%;
        height: 100%;
        min-height: 100%;
        max-height: 100%;
    }

    .img-container::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 30%;
        height: 100%;
        /* background: linear-gradient(to left, #4d4d4d, transparent); */
        /* pointer-events: none; */
    }
</style>
@endsection

@section('content')
<div class="container w-100 h-auto gap-5 d-flex my-5 flex-column justify-content-center align-items-center">
    <div class="w-100 d-flex mt-5 mb-2 flex-column justify-content-center align-items-center">
        <p class="fw-bolder" style="font-size: 30px;">Cari Pekerjaan</p>
        <div
            class="form-control w-75 ps-3 rounded-5 shadow d-flex flex-row gap-0 justify-content-center align-items-center">
            <i class="bi bi-search w-auto"></i>
            <input type="text"
                class="form-control rounded-5 flex-1 px-2 no-outline border-0  @error('max_gaji') is-invalid @enderror"
                maxlength="150" name="nama" value="{{ old('nama') }}" placeholder="">
        </div>
    </div>
    <div class="newest d-none w-100 d-flex mt-5 flex-column row">
        Pekerjaan Terbaru Nih,
        <div class="w-100 justify-content-center align-items-center d-flex ">
            <div class="row g-3 border-3 border-black shadow mt-0 rounded-4 p-3 pt-0 row d-flex flex-wrap justify-content-start justify-content-md-between align-items-start"
                style="min-height: 43vh; width: fit-content; max-width: fit-content; min-width: 100%;">
                @for($i=5;$i>1;$i--)
                <div class="col-12 col-md-6 col-lg-3">
                    <a href=""
                        class=" w-100 h-100 job text-decoration-none d-flex flex-row p-0 gap-0 rounded-2 align-items-center justify-content-between"
                        style="">
                        <div class="rounded-start-2 justify-content-start align-items-center"
                            style="width: 40%; min-width: 40%; height: 100%;">
                            <img class="rounded-start-2"
                                src="{{ auto_asset('Dewa/img/f4030acf172260f3241cad5f4527a7d8.jpg') }}"
                                style="width: 100%; height: 100%;  object-fit: cover;" alt="">
                        </div>
                        <div class="d-flex flex-column justify-content-center text-truncate align-items-start gap-0 flex-grow-1 h-100 p-2"
                            style=" max-width: 100%;">
                            <p class="clear-p fw-bolder text-truncate w-100" style="font-size: 12px; color: #2E2D2C;">
                                Potong
                                Rumput ksdaksjdkjasbdkjasbdkmasbndmanb</p>
                            <p class="clear-p  opacity-50" style="font-size: 10px; color: #2E2D2C;">5 Jam yang lalu</p>
                            <p class="clear-p opacity-50" style="font-size: 10px; color: #2E2D2C;"></p>
                        </div>
                    </a>
                </div>
                @endfor
            </div>
        </div>
    </div>
    <!-- <div class="result d-flex w-100 d-flex mt-5 flex-column row">
        Hasil Pencarian,
        <div class="w-100 justify-content-center align-items-center d-flex ">
            <div class="row g-3 border-3 border-black shadow mt-0 rounded-4 p-3 pt-0 row d-flex flex-wrap justify-content-start justify-content-md-start align-items-start"
                style="min-height: 43vh; width: fit-content; max-width: fit-content; min-width: 100%;">
                @for($i=20;$i>1;$i--)
                <div class="col-12 col-md-6 col-lg-3">
                    <a href=""
                        class=" w-100 h-100 job text-decoration-none d-flex flex-row p-0 gap-0 rounded-2 align-items-center justify-content-between"
                        style="">
                        <div class="rounded-start-2 justify-content-start align-items-center"
                            style="width: 40%; min-width: 40%; height: 100%;">
                            <img class="rounded-start-2" src="{{ auto_asset('Dewa/img/f4030acf172260f3241cad5f4527a7d8.jpg') }}"
                                style="width: 100%; height: 100%;  object-fit: cover;" alt="">
                        </div>
                        <div class="d-flex flex-column justify-content-center text-truncate align-items-start gap-0 flex-grow-1 h-100 p-2"
                            style=" max-width: 100%;">
                            <p class="clear-p fw-bolder text-truncate w-100" style="font-size: 12px; color: #2E2D2C;">
                                Potong
                                Rumput ksdaksjdkjasbdkjasbdkmasbndmanb</p>
                            <p class="clear-p  opacity-50" style="font-size: 10px; color: #2E2D2C;">5 Jam yang lalu</p>
                            <p class="clear-p opacity-50" style="font-size: 10px; color: #2E2D2C;">1,4 km</p>
                        </div>
                    </a>
                </div>
                @endfor
            </div>
        </div>
    </div> -->
    <div class="result d-flex w-100 d-flex mt-5 flex-column row">
        Pekerjaan Disarankan,
        <div class="w-100 justify-content-center align-items-center d-flex ">
            <div class="row g-3 border-3 border-black shadow mt-0 rounded-4 p-3 pt-0 row d-flex flex-wrap justify-content-start justify-content-md-start align-items-start"
                style="min-height: 43vh; width: fit-content; max-width: fit-content; min-width: 100%;">
                @foreach($match as $key => $value)
                <div class="col-12 col-md-6 col-lg-3">
                    <a href="{{{$all[$key]['id']}}}"
                        class=" w-100 h-100 job text-decoration-none d-flex flex-row p-0 gap-0 rounded-2 align-items-center justify-content-between"
                        style="">
                        <div class="rounded-start-2 justify-content-start align-items-center"
                            style="width: 40%; min-width: 40%; height: 100%;">
                            <img class="rounded-start-2"
                                src="{{ auto_asset('Dewa/img/f4030acf172260f3241cad5f4527a7d8.jpg') }}"
                                style="width: 100%; height: 100%;  object-fit: cover;" alt="">
                        </div>
                        <div class="d-flex flex-column justify-content-center text-truncate align-items-start gap-0 flex-grow-1 h-100 p-2"
                            style=" max-width: 100%;">
                            <p class="clear-p fw-bolder text-truncate w-100" style="font-size: 12px; color: #2E2D2C;">
                                {{{$all[$key]['nama']}}}</p>
                            <p class="clear-p  opacity-50" style="font-size: 10px; color: #2E2D2C;">5 Jam yang lalu</p>
                            <p class="clear-p opacity-50" style="font-size: 10px; color: #2E2D2C;">
                                {{{hitungJarak($all[$key]['longitude'], $all[$key]['langitude'],
                                session('account')['longitude'], session('account')['langitude'])}}}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>

</script>
@endsection

@php
function hitungJarak($lon1, $lat1, $lon2, $lat2)
{
    $earthRadius = 6371; // kilometer

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
    cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
    sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c;

    return number_format($distance, 2) . ' km';
}


@endphp