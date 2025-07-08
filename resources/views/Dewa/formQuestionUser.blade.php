@extends('Dewa.Base.Basic-page')

@section('css')

<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&amp;display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .dropdown-container {
        overflow-y: hidden;
        position: relative;
        width: 100%;
        max-width: 500px;
    }

    .dropdown-list {
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    .dropdown-list::-webkit-scrollbar {
        width: 8px;
    }

    .dropdown-list::-webkit-scrollbar-thumb {
        background-color: #ced4da;
        border-radius: 4px;
    }

    .dropdown-list::-webkit-scrollbar-thumb:hover {
        background-color: #adb5bd;
    }


    .form-container {
        max-width: 720px;
        margin: auto;
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-select {
        border-radius: 0.5rem;
    }

    .form-section {
        margin-bottom: 1.75rem;
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: bold;
        color: #0d6efd;
    }

    .dropdown-label {
        width: 100%;
        min-width: 100%;
        max-width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="form-container bg-white p-5 rounded shadow-sm">
        <h2 class="text-center form-title mb-4">📝 Formulir Kemampuan dan Preferensi Kerja</h2>

        {{-- Flash Message jika ada --}}
        @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="/user/preferensi/save">

            @csrf
            @php
            $questions = [
            'job_distance' => ['label' => 'Jarak kerja yang Anda bersedia tempuh', 'options' => [
            '0' => 'hanya remote',
            '1' => 'kurang dari atau sama dengan 1 km',
            '2' => '1–5 km',
            '3' => '6–10 km',
            '4' => '11–20 km',
            '5' => 'lebih dari 20 km'
            ]],
            'expected_rate' => ['label' => 'Minimal upah yang Anda harapkan', 'options' => [
            '0' => 'kurang dari atau sama dengan 20rb',
            '1' => 'kurang dari atau sama dengan 30rb',
            '2' => 'kurang dari atau sama dengan 40rb',
            '3' => 'kurang dari atau sama dengan 50rb',
            '4' => 'kurang dari atau sama dengan 75rb',
            '5' => 'lebih dari 75rb'
            ]],
            'daily_hours' => ['label' => 'Waktu kerja per hari yang bisa diluangkan', 'options' => [
            '0' => 'kurang dari 1 jam',
            '1' => '1–2 jam',
            '2' => '3–4 jam',
            '3' => '5–6 jam',
            '4' => '7–8 jam',
            '5' => 'lebih dari 8 jam'
            ]],
            'project_duration' => ['label' => 'Durasi proyek yang Anda inginkan', 'options' => [
            '0' => '1 hari',
            '1' => 'kurang dari atau sama dengan 3 hari',
            '2' => 'kurang dari atau sama dengan 1 minggu',
            '3' => 'kurang dari atau sama dengan 2 minggu',
            '4' => 'kurang dari atau sama dengan 1 bulan',
            '5' => 'lebih dari 1 bulan'
            ]],
            'work_method' => ['label' => 'Preferensi metode kerja', 'options' => [
            '0' => 'hanya on-site',
            '1' => 'lebih suka on-site',
            '2' => 'fleksibel cenderung on-site',
            '3' => 'fleksibel netral',
            '4' => 'fleksibel cenderung remote',
            '5' => 'hanya remote'
            ]],
            'experience_length' => ['label' => 'Lama pengalaman kerja Anda', 'options' => [
            '0' => 'tidak ada',
            '1' => 'kurang dari 3 bulan',
            '2' => '3–6 bulan',
            '3' => '6–12 bulan',
            '4' => '1–2 tahun',
            '5' => 'lebih dari 2 tahun'
            ]],
            'available_start' => ['label' => 'Kapan Anda bisa mulai bekerja', 'options' => [
            '0' => 'lebih dari 2 minggu',
            '1' => 'dalam 2 minggu',
            '2' => 'dalam 1 minggu',
            '3' => 'dalam 3 hari',
            '4' => 'besok',
            '5' => 'hari ini'
            ]],

            ];
            @endphp

            <div class="form-group d-flex flex-column">


                <label for="" class="form-label"> Deskripsikan Dirimu</label>
                <textarea name="deskripsi" id="" class="shadow-sm form-control mb-3" placeholder="Beri gambaran kepada kami anda orang yang seperti apa, bisa kebiasaan, pekerjaan, dan hal yang disukai"></textarea>
            </div>

            @foreach ($questions as $name => $q)
            <div class="form-section">
                <label for="{{ $name }}" class="form-label">{{ $q['label'] }}</label>
                <select id="{{ $name }}" name="{{ $name }}"
                    class="form-select shadow-sm @error($name) is-invalid @enderror" data-style="btn-outline-primary"
                    data-width="100%">
                    <option value="-" selected>Pilih jawaban...</option>
                    @foreach ($q['options'] as $key => $desc)
                    <option value="{{ $key.' '.$desc }}" {{old($name)==(string)$key?'selected':''}}>
                        {{ $desc }}
                    </option>
                    @endforeach
                </select>
                @error($name)
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach

            <div class="form-group">
                <div class="row justify-content-center w-100">
                    <div class="w-100 d-flex justify-content-center p-0 m-0 align-items-center">
                        <div class="dropdown-container w-100">
                            <div class="dropdown-button noselect w-100 mb-2">
                                <label class="form-label fw-semibold text-dark">Kriteria Pekerjaan (pilih minimal
                                    3)</label>
                            </div>
                            <div class="dropdown-list p-3 overflow-y-hidden">
                                <input type="text" class="form-control mb-2"
                                    placeholder="Tulis disini jika tidak ada, format: text, text, text">
                                <input type="text" class="form-control mb-3" oninput="SearchSelect()"
                                    placeholder="Cari Kriteria..." id="searchKriteria">

                                <ul class="list-unstyled mb-0 overflow-y-scroll"
                                    style="height: 18vh; min-height: 18vh; max-height: 18vh;">
                                    @foreach($kriteria as $k)
                                    <li class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="kriteria{{ $k->id }}"
                                                value="{{ $k->id.' '.$k->nama }}" name="AL" id="kriteria{{ $k->id }}">
                                            <label class="form-check-label" for="kriteria{{ $k->id }}">
                                                {{ $k->nama }}
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                @error('kriteria')
                <div class="invalid-feedback">{{ $message }}</div>

                @enderror
            </div>


            <div class="text-center mt-5">
                <button type="submit" class="btn btn-primary px-5 py-2">🚀 Kirim Jawaban</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')

@endsection