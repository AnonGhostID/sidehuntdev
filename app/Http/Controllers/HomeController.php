<?php

namespace App\Http\Controllers;

use App\Models\KriteriaJob;
use App\Models\Pelamar;
use App\Models\Pekerjaan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Users;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $Pekerjaan = Pekerjaan::latest()->paginate(10);
    //     $peta = Pekerjaan::all();

    //     // return view('pekerjaan.list', compact('Pekerjaan'));

    //     return view('home', compact('Pekerjaan', 'peta'));
    // }

    //Dewa
    public function index()
    {
        $jobs = Pekerjaan::latest()->paginate(10);
        $peta = Pekerjaan::all();
        $active_navbar = 'Beranda';
        $nama_halaman = 'Beranda';


        // return view('pekerjaan.list', compact('sidejob'));
        if (session()->has('account')) {
            // dd(session('account')->preferensi_user);
            if (session('account')->preferensi_user == null) {
                return redirect('/question-new-user')->with('success', ['Isi Data Terlebih Dahulu', 'Izin Mengganggu waktunya sebentar']);
            }
            else{
                return view('Dewa.index', compact('jobs', 'peta', 'active_navbar', 'nama_halaman'));
            }
        } else {
            return view('Dewa.index', compact('jobs', 'peta', 'active_navbar', 'nama_halaman'));
        }
    }

    public function show(string $id): View
    {
        //Cari data sesuai id
        $Pekerjaan = Pekerjaan::findorfail($id);
        $pelamar = Pelamar::where('job_id', $Pekerjaan->id)->paginate(10);

        //Kirimkan ke view ini
        return view('pekerjaan.detail', compact('Pekerjaan', 'pelamar'));
    }
    public function Register()
    {
        if (session('account') == null) {
            $active_navbar = 'Register';
            $nama_halaman = 'Register';
            return view('Dewa.register', compact('active_navbar', 'nama_halaman'));
        } else {
            return redirect('/Index')->with('success', ['Anda Sedang Login', 'Logout terlebih dahulu']);
        }
    }
    public function Login()
    {
        if (session('account') != null) {
            return redirect('/Index')->with('success', 'Anda Sudah Login|Logout terlebih dahulu');
            return redirect('/Index')->with('success', ['Anda Sudah Login', 'Logout terlebih dahulu']);
        } else {
            $active_navbar = 'Login';
            $nama_halaman = 'Login';
            return view('Dewa.Login', compact('active_navbar', 'nama_halaman'));
        }
    }

    public function admin()
    {
        $Pekerjaan = Pekerjaan::latest()->paginate(10);
        $user = Users::paginate(10);
        $pelamar = Pelamar::latest()->paginate(10);
        $transaksi = Transaksi::latest()->paginate(10);

        $user_count = Users::count();
        $job_count = Pekerjaan::count();
        $pelamar_count = Pelamar::count();
        $transaksi_count = Transaksi::count();

        return view('admin.AdminDashboard', compact('Pekerjaan', 'transaksi', 'user', 'pelamar', 'user_count', 'job_count', 'pelamar_count', 'transaksi_count'));
    }

    public function management()
    {
        $this->middleware('isAdmin');
        return view('manajemen/dashboard');
    }

    function new_user()
    {
        $nama_halaman = 'Form New User';
        $active_navbar = 'Form New User';
        $kriteria = KriteriaJob::all();

        return view('Dewa.formQuestionUser', compact('active_navbar', 'nama_halaman', 'kriteria'));
    }
}
