@vite(['resources/js/app.js'])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Side Hunt - {{$nama_halaman}}</title>
    <!-- Fonts -->
    {{--
    <link rel="dns-prefetch" href="//fonts.bunny.net"> --}}
    <link rel="icon" href="{{ auto_asset('Dewa/img/logoOnlyIcon.svg')}}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <!-- Bootstrap JS and Popper (CDN version) -->


    <!-- <link rel="stylesheet" href="{{asset('css/basic-page.css')}}"> -->
    <link rel="stylesheet" href="{{ auto_asset('Dewa/css/basic-page.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('css')

</head>
<style>
    @yield('peta');
</style>

<body>

    <div class="landing-logo hide">
        <img src="{{ auto_asset('Dewa/img/logo.svg')}}" alt="">
    </div>
    <div>
        <nav class="navbar navbar-expand-md navbar-light shadow-md bg-darker h-auto bg-[545454]">
            <div class="container h-auto">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <div class="container-logo d-flex">
                        <img src="{{ auto_asset('Dewa/img/logo.svg')}}" alt="">
                    </div>

                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse h-auto normal navbar-collapse slide-down flex-column flex-md-row gap-3 gap-md-0"
                    id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav right auth-normal align-items-center ms-auto order-1 order-md-0 m-3">
                        @if(session('account')==null)
                        <li class="nav-item auth {{{($active_navbar=='Register')? 'active':''}}}">
                            <a class="nav-link " aria-current="page" href="{{ url('/Register') }}">
                                <p>Register</p>
                            </a>
                        </li>
                        <li class="nav-item auth {{{($active_navbar=='Login')? 'active':''}}}">
                            <a class="nav-link " aria-current="page" href="{{url('/Login')}}">
                                <p>Login</p>
                            </a>
                        </li>
                        @else
                        <div class="d-flex flex-column dropdown w-fit-md w-full-ls">

                            <div class="d-flex flex-row user gap-2 align-items-center justify-content-center"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <p class="m-0 p-0">Halo {{{explode(" ", session('account')['nama'])[0]}}},</p>
                                <svg width="30" height="29" viewBox="0 0 30 29" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="30" height="29" rx="14.5" fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.375 9.5C11.375 8.57174 11.7437 7.6815 12.4001 7.02513C13.0565 6.36875 13.9467 6 14.875 6C15.8033 6 16.6935 6.36875 17.3499 7.02513C18.0063 7.6815 18.375 8.57174 18.375 9.5C18.375 10.4283 18.0063 11.3185 17.3499 11.9749C16.6935 12.6313 15.8033 13 14.875 13C13.9467 13 13.0565 12.6313 12.4001 11.9749C11.7437 11.3185 11.375 10.4283 11.375 9.5ZM11.375 14.75C10.2147 14.75 9.10188 15.2109 8.28141 16.0314C7.46094 16.8519 7 17.9647 7 19.125C7 19.8212 7.27656 20.4889 7.76884 20.9812C8.26113 21.4734 8.92881 21.75 9.625 21.75H20.125C20.8212 21.75 21.4889 21.4734 21.9812 20.9812C22.4734 20.4889 22.75 19.8212 22.75 19.125C22.75 17.9647 22.2891 16.8519 21.4686 16.0314C20.6481 15.2109 19.5353 14.75 18.375 14.75H11.375Z"
                                        fill="#1B4841" />
                                </svg>
                            </div>
                            <ul class="dropdown-menu ms-md-0 bg-darker hover-bg-dark">
                                <li><a class="dropdown-item" href="/Profile">
                                        <div class="d-flex flex-row gap-3">
                                            <p>Profil</p>
                                        </div>
                                    </a></li>
                                <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item bg-danger" href="/Logout">
                                        <div class="d-flex flex-row gap-3">
                                            <p>Keluar</p>
                                        </div>
                                    </a></li>
                            </ul>
                        </div>

                        @endif
                    </ul>
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav left normal me-auto mb-2 mb-md-0 order-0 order-md-1">
                        <li class="nav-item ">
                            <a class="nav-link {{{($active_navbar=='Beranda')? 'active':''}}}" aria-current="page"
                                href="{{ url('/Index') }}">
                                <p>Beranda</p>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{{($active_navbar=='Cari Pekerjaan')? 'active':''}}}"
                                aria-current="page" href="/kerja/">
                                <p>Cari Pekerjaan</p>
                            </a>
                        </li>
                        @if(session()->has('account'))
                        @if(session('account')->role=='mitra' || session('account')->role=='user' || session('account')->role=='admin')
                        <li class="nav-item ">
                            <a class="nav-link {{{($active_navbar=='Management')? 'active':''}}}"
                                aria-current="page" href="/management/">
                                <p>Management</p>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if(session('account')!=null)

                        <li class="nav-item dropdown position-relative">
                            <a class="nav-link d-flex w-auto justify-content-start align-content-center flex-row gap-1 {{{($active_navbar=='Beri Lowongan Kerja')? 'active':''}}}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <p>Pekerjaan</p>
                                <i class="bi bi-caret-down-fill"></i>
                            </a>
                            <ul class="dropdown-menu normal ms-4 ms-md-0 bg-darker hover-bg-dark">
                                <li><a class="dropdown-item" href="{{ route('dewa.mitra.lowongan.terdaftar') }}">
                                        <div class="d-flex flex-row gap-3">
                                            <i class="bi bi-journal-text"></i>
                                            <p>Lowongan Terdaftar</p>
                                        </div>
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('dewa.mitra.lowongan.terdaftar') }}">
                                        <div class="d-flex flex-row gap-3">
                                            <i class="bi bi-people"></i>
                                            <p>Daftar Pelamar</p>
                                        </div>
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/kerja/create">
                                        <div class="d-flex flex-row gap-3">
                                            <i class="bi bi-plus-circle"></i>
                                            <p>Tambahkan Lowongan</p>
                                        </div>
                                    </a>
                                </li>

                            </ul>
                        </li>


                        @endif
                    </ul>


                </div>
            </div>
        </nav>
        @yield('add-onn')
        <div class="main-content">
            @yield('content')
        </div>


        <div class="bottom mt-4">
            <div class="semi-bottom normal">
                <div class="container bottom">
                    <div>
                        <p>Email</p>
                        <p>sidehunt@gmail.com</p>
                    </div>

                    <div>
                        <p>Phone</p>
                        <p>0895339385652</p>
                    </div>
                    <div class="lebih-panjang">
                        <p>Addresss</p>
                        <p>Jl. RE Martadinata, Kali Nangkaan, Dabasah, Kec. Bondowoso, Kab. Bondowoso, Jawa Timur 68211
                        </p>
                    </div>

                </div>
            </div>
            <div class="copyright">
                <p>@Copyright 2025</p>
            </div>
        </div>

    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(el => new bootstrap.Popover(el));
</script>


<script>
    @if (session('firstAccess') == true)
        show_logo()
    setTimeout(hide_logo, 1000);
    @endif

    @if (session('success'))
        successAlert("{{session('success')[0]}}", "{{session('success')[1]}}")
    setTimeout(hide_logo, 1000);
    @endif

    @if (session('fail'))
        failAlert('{{{session('fail')[0]}}}', '{{{session('fail')[1]}}}')
    @endif

    @if (session('alert'))
        let split = "{{session('alert')}}".split('|');
    NotifyAlert(split[0], split[1], 0);
    @endif
    function hide_logo() {
        let logo = document.querySelector('.landing-logo.show')
        logo.classList.replace('show', 'hide');
    }
    function show_logo() {
        let logo = document.querySelector('.landing-logo.hide')
        logo.classList.replace('hide', 'show');
    }
    // show_logo()
    function cek_view_port() {
        let b = window.getComputedStyle(document.querySelector
            ('.navbar-toggler')).display;
        if (b == 'block') {
            let content = document.querySelector('.navbar-nav.right.auth-normal');
            if (content) {
                content.classList.replace('auth-normal', 'auth-half');
            }

            let bottom = document.querySelector('.semi-bottom.normal')
            if (bottom) {
                bottom.classList.replace('normal', 'half');
            }

            let navleft = document.querySelector('.navbar-nav.left.normal');
            if (navleft) navleft.classList.replace('normal', 'half');
        }
        else {
            let content = document.querySelector('.navbar-nav.right.auth-half');
            if (content) {

                content.classList.replace('auth-half', 'auth-normal');
            }

            let bottom = document.querySelector('.semi-bottom.half')
            if (bottom) {
                bottom.classList.replace('half', 'normal');
            }

            let navleft = document.querySelector('.navbar-nav.left.half');
            if (navleft) navleft.classList.replace('half', 'normal');
        }
    }
    cek_view_port();
    window.addEventListener('resize', cek_view_port);

    function NotifyAlert(titled, message, type = null) {
        let img = null;
        if (type == null) {
            type == 0 ? img = "{{ auto_asset('Dewa/img/success.svg')}}" : img = "{{ auto_asset('Dewa/img/failed.svg') }}";
        }
        Swal.fire({
            title: titled,
            text: message,
            imageUrl: img,
            imageWidth: 100,
            imageHeight: 100,
            imageAlt: "Custom image",
            width: 300,
            // height: 400,
        });
    }


    function NotifyAlertSuccess(wht, message) {

        let img = null;
        let type = message.slice(0, 1);
        titled = null;
        let msg = message.slice(3).trim();
        console.log(type == 0)
        if (type == 0) {
            img = "{{ auto_asset('Dewa/img/failed.svg')}}";
            titled = wht + " Gagal!"
        }
        else {

            img = "{{ auto_asset('Dewa/img/success.svg')}}"
            titled = wht + " Berhasil"
        }
        Swal.fire({
            title: titled,
            text: msg,
            imageUrl: img,
            imageWidth: 100,
            imageHeight: 100,
            imageAlt: "Custom image",
            width: 300,
            // height: 400,
        });
    }

    function reverseGeocode(lat, lon) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then data => {
                if (data.address) {
                    alert("Nama jalan: " + (data.address.road || "Tidak ditemukan"));
                } else {
                    alert("Tidak ditemukan");
                }
            });
    }

    function successAlert(title, msg) {
        Swal.fire({
            title: title,
            icon: "success",
            text: msg,
            draggable: true
        });
    }
    function failAlert(title, msg) {
        Swal.fire({
            title: title,
            icon: "error",
            text: msg,
            draggable: true
        });
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@yield('script')

</html>