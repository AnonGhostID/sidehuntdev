<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pelamar;
use App\Models\Users;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Ui\Presets\React;
// use User;

class UsersController extends Controller
{
    //Untuk non-admin
    // public function pelamaran()
    // {
    //     $user = auth()->user();
    //     $lamaran = Pelamar::with('sidejob')->where("user_id", $user->id)->get();

    //     return view('users.lamaran', compact('lamaran'));
    // }



    //Untuk admin
    public function showAdmin($id)
    {
        $user = Users::findOrFail($id);
        $jobs = SideJob::where('pembuat', $id)->get();
        return view('admin.users.profile', compact('user', 'jobs'));
    }

    // public function create(): View
    // {
    //     return view('admin.users.create');
    // }

    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'nama' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'alamat' => ['required', 'string', 'max:255'],
    //         'telpon' => ['required', 'string', 'numeric'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);
    //     Users::create([
    //         'nama' => $request['nama'],
    //         'email' => $request['email'],
    //         'alamat' => $request['alamat'],
    //         'telpon' => $request['telpon'],
    //         'password' => Hash::make($request['password']),
    //     ]);
    //     return redirect()->back()->with(['success' => 'User berhasil tersimpan']);
    // }

    public function edit(string $id): View
    {
        $user = Users::findorfail($id);

        return view('admin.users.edit', compact('user'));
    }

    //Cara code untuk ubah data
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'alamat' => ['required', 'string', 'max:255'],
            'telpon' => ['required', 'string', 'numeric'],
            // 'password' => ['string', 'min:8'],
        ]);
        $user = Users::findorfail($id);
        $user->update([
            'nama' => $request['nama'],
            'email' => $request['email'],
            'alamat' => $request['alamat'],
            'telpon' => $request['telpon'],
            // 'password' => Hash::make($request['password']),
        ]);

        return redirect()->route('admin.index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy($id): RedirectResponse
    {
        $user = Users::findorfail($id);
        $user->delete();

        return redirect()->back()->with(['success' => 'Data berhasil dihapus']);
    }

    //NEW
    public function Login_Account(Request $req)
    {
        // dd('.asuk');
        $credentials = $req->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = Users::where('email', $req->email)->first();

        // dd(Hash::check($req->password,$user->password));
        if ($user) {
            if ($user && Hash::check($req->password, $user->password)) {
                if (Auth::attempt($credentials)) {
                    session(['account' => $user]);
                    // dd(session('account'));

                    // dd('masuk');
                    if (session('account')->preferensi_user == null) {
                        return redirect('/question-new-user')->with('success', ['Izin Mengganggu waktunya sebentar', 'Isi Data Terlebih Dahulu']);
                    } else {
                        return redirect('/Index')->with('success', ['Sukses', 'Login Berhasil']);
                    }
                }
                // }
                // else{
                //     return back()->withErrors([
                //         'message' => '0, Email atau password salah',
                //     ])->withInput();
                // }
            } else {
                return back()->with([
                    'fail' => ['Login Gagal', 'Password salah'],
                ])->withInput();
            }
        } else {
            return back()->with([
                'fail' => ['Login Gagal', 'Tidak ada akun yang terdaftar dengan email ini'],
            ])->withInput();
        }
    }
    public function logout()
    {
        if (session('account') != null) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect('/Login')->with('success', ['Berhasil', 'Logout Berhasil']);
        } else {
            $allert = 'Logout Gagal|Anda tidak dalam sesi yang aktif';
            return back()->with('alert', $allert);
        }
    }

    public function create(Request $req)
    {
        // dd(dd($req));

        $req->validate([

            'nama-depan' => 'required|string|max:60',
            'nama-belakang' => 'required|string|max:60',
            'email' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!$this->cekExistEmail($value)) {
                        $fail('Gunakan email yang belum pernah terdaftar.');
                    }
                },
            ],
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->cek_password($value)) {
                        $fail('Password tidak memenuhi kriteria keamanan');
                    }
                },
            ],
            'password-retype' => 'required|same:password',
        ], [
            'nama-depan.required' => 'Nama wajib diisi.',
            'nama-depan.string' => 'Nama harus berupa teks.',
            'nama-depan.max' => 'Nama maksimal 60 karakter.',

            'nama-belakang.required' => 'Nama wajib diisi.',
            'nama-belakang.string' => 'Nama harus berupa teks.',
            'nama-belakang.max' => 'Nama maksimal 60 karakter.',

            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',

            'password.required' => 'Password wajib diisi.',
            'password-retype.required' => 'Verifikasi Password wajib diisi.',
            'password-retype.same' => 'Verifikasi Password harus sama dengan password.',
        ]);
        $data = collect($req->all())->mapWithKeys(function ($value, $key) {
            return [$key => is_array($value) ? $value : (string)$value];
        })->toArray();
        // dd('masuk');
        $data['password'] = Hash::make($req->password);
        $data['nama'] = $req['nama-depan'] . ' ' . $req['nama-belakang'];
        if (Users::create($data)) {
            return redirect()->back()->with('success', ['Registrasi Berhasil', 'Akun anda berhasil didaftarkan, Silahkan Login']);
        } else {
            return redirect()->back()->with('fail', ['Registrasi Gagal', 'Maaf Terjadi Kesalahan dalam penyimpanan data']);
        }
    }



    function cekExistEmail($emailUntukDicek)
    {
        $user = Users::where('email', $emailUntukDicek)->first();
        return ($user) ? false : true;
    }


    function cek_password($password)
    {
        return strlen($password) >= 8 &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password);
    }

    function Profile()
    {
        $jobs = Pekerjaan::latest()->paginate(10);
        $peta = Pekerjaan::all();
        $active_navbar = 'Profile';
        $nama_halaman = 'Profile';
        $kode_Nomor = json_decode(file_get_contents(public_path('Dewa/json/dial_country.json')), TRUE);
        // dD($kode_Nomor);


        // return view('pekerjaan.list', compact('Pekerjaan'));

        return view('Dewa/profile', compact('jobs', 'peta', 'active_navbar', 'nama_halaman', 'kode_Nomor'));
    }

    function Profile_Edit(Request $req)
    {
        // dd($req);
        // dd(session('account')->id);
        $user = Users::findOrFail(session('account')->id);
        $user->nama = $req->nama;
        $user->alamat = $req->alamat;
        $user->telpon = $req->telpon;
        if ($user->save()) {
            session(['account' => $user]);

            return redirect()->back()->with('success', ['Berhasil', 'Ubah Data Profile Berhasil']);
        }
        // dd($user);
    }

    function save_preverensi(Request $request)
    {
        $alal = $request->json()->all();
        $user = Users::findOrFail(session('account')->id);
        $user->preferensi_user = json_encode($request->except('_token'));
        if ($user->save()) {
            session(['account' => $user]);

            return redirect('/')->with('success', ['Terimakasih', 'Semoga Lekas Diterima Kerja']);
        }
    }
}
