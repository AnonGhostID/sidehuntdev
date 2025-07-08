<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KriteriaJob;
use App\Models\Pekerjaan;
use App\Models\Pelamar;
use App\Models\Notification;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PekerjaanController extends Controller
{
    public function index()
    {
        $all = Pekerjaan::all();
        // dd($all[0]);
        $match = ($this->getThe20ies());
        $active_navbar = 'Cari Pekerjaan';
        $nama_halaman = 'Cari Pekerjaan';


        return view("Dewa.Mitra.pekerjaan-cari", compact("active_navbar", 'nama_halaman', 'all', 'match'));
    }
    public function create()
    {
        $active_navbar = 'Beri Lowongan Kerja';
        $nama_halaman = 'Tambah Pekerjaan';
        $kriteria = KriteriaJob::all();
        return view("Dewa.Mitra.pekerjaan-add", compact("active_navbar", 'nama_halaman', 'kriteria'));
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'nama' => 'required|string|max:60',
            'min_gaji' => 'required|integer|max:99999999999999999999', // maksimal 20 digit
            'max_gaji' => 'required|integer|max:99999999999999999999', // maksimal 20 digit
            'max_pekerja' => 'required|integer|max:9999999999', // maksimal 10 digit
            'deskripsi' => 'required|string|max:1000',
            'alamat' => 'required|string|max:100',
            'petunjuk_alamat' => 'required|string|max:100',
            'koordinat' => 'required|string',
            'start_job' => 'required|date|after_or_equal:today',
            'end_job' => 'required|date|after_or_equal:start_job',
            'deadline_job' => 'nullable|date|before_or_equal:end_job',
            'kriteria' => 'required|array|min:3',
            'foto_job' => 'nullable|file|mimes:jpg,jpeg,png,img,image/jpeg, image/png|max:5120',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 60 karakter.',

            'min_gaji.required' => 'Gaji minimum wajib diisi.',
            'min_gaji.integer' => 'Gaji minimum harus berupa angka bulat.',
            'min_gaji.max' => 'Gaji minimum maksimal 20 digit angka.',

            'max_gaji.required' => 'Gaji maksimum wajib diisi.',
            'max_gaji.integer' => 'Gaji maksimum harus berupa angka bulat.',
            'max_gaji.max' => 'Gaji maksimum maksimal 20 digit angka.',

            'max_pekerja.required' => 'Pekerja diterima wajib diisi.',
            'max_pekerja.integer' => 'Pekerja diterima harus berupa angka bulat.',
            'max_pekerja.max' => 'Pekerja diterima maksimal 10 digit angka.',

            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat maksimal 100 karakter.',

            'petunjuk_alamat.required' => 'Petunjuk alamat wajib diisi.',
            'petunjuk_alamat.string' => 'Petunjuk alamat harus berupa teks.',
            'petunjuk_alamat.max' => 'Petunjuk alamat maksimal 100 karakter.',

            'koordinat.required' => 'Lokasi Alamat wajib diisi.',

            'start_job.required' => 'Tanggal & waktu mulai wajib diisi.',
            'start_job.date' => 'Format tanggal & waktu mulai tidak valid.',
            'start_job.after_or_equal' => 'Tanggal & waktu mulai tidak boleh sebelum hari ini.',

            'end_job.required' => 'Tanggal & waktu selesai wajib diisi.',
            'end_job.date' => 'Format tanggal & waktu selesai tidak valid.',
            'end_job.after_or_equal' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',

            'deadline_job.date' => 'Format deadline tidak valid.',
            'deadline_job.before_or_equal' => 'Deadline harus sebelum atau sama dengan waktu berakhirnya kerja.',

            'foto_job.mimes' => 'Foto harus berformat JPG/PNG/JPEG/IMG/IMAGE.',
            'foto_job.max' => 'Ukuran foto maksimal 5MB.',


            'kriteria.required' => 'Minimal masukkan 3 kriteria.',
            'kriteria.array' => 'Format kriteria tidak valid.',
            'kriteria.min' => 'Minimal masukkan 3 kriteria.',



        ]);
        if ($request->koordinat != null) {
            $request['pembuat'] = session('account')['id'];
            $kriteria = [];
            // dd($request->kriteria);     
            foreach ($request->kriteria as $nama) {
                $cek = (new KriteriaJobController())->cek_exist($nama);
                if ($cek == false) {
                    $new = (new KriteriaJobController())->store(trim($nama));
                    // dD($new);
                    $kriteria[] = $new->id;
                } else {
                    $kriteria[] = $cek;
                }
            }

            $request['kriteria'] = implode(",", $kriteria);

            // dd($request['kriteria']);


            $data = collect($request->all())->mapWithKeys(function ($value, $key) {
                return [$key => is_array($value) ? $value : (string)$value];
            })->toArray();
            if ($request['deadline_job'] == null) {
                $data['deadline_job'] = null;
            } else {
                $data['deadline_job'] = $request['deadline_job'];
            }

            if ($request->hasFile('foto_job')) {
                $file = $request->file('foto_job');
                $filename = $request->nama . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs('job', $filename, 'public');
                $data['foto_job'] = $filename;
            }
            if (Pekerjaan::create($data)) {
                return redirect()->back()->with('success', ['Berhasil', 'Pekerjaan Berhasil didaftarkan']);
            };
        } else {
            return redirect()->back()->with('fail', ['Gagal Disimpan!', 'Pin belum diset di map']);
        }
    }


    public function terima(Pelamar $pelamar)
    {
        $result = $this->updatePelamarStatus($pelamar, 'diterima');
        
        if (!$result) {
            return redirect()->back()->with('error', 'Tidak dapat menerima pelamar karena sudah mencapai batas maksimal pekerja.');
        }
        
        return redirect()->back()->with('success', 'Pelamar berhasil diterima.');
    }

    public function tolak(Pelamar $pelamar)
    {
        $this->updatePelamarStatus($pelamar, 'ditolak');
        return redirect()->back();
    }

    private function updatePelamarStatus(Pelamar $pelamar, string $status)
    {
        $job = $pelamar->sidejob;
        $oldStatus = $pelamar->status;

        // If trying to accept (diterima), check if we exceed max_pekerja limit
        if ($status === 'diterima' && $oldStatus !== 'diterima' && $job) {
            $currentAccepted = $job->pelamar()->where('status', 'diterima')->count();
            
            // Check if accepting this pelamar would exceed the limit
            if ($currentAccepted >= $job->max_pekerja) {
                // Don't update status, return early (could throw exception or handle differently)
                return false;
            }
        }

        // Update pelamar status
        $pelamar->update(['status' => $status]);

        // Update jumlah_pelamar_diterima count in pekerjaan table
        if ($job) {
            $acceptedCount = $job->pelamar()->where('status', 'diterima')->count();
            $job->update(['jumlah_pelamar_diterima' => $acceptedCount]);

            // Create notification
            Notification::createJobStatusNotification(
                $pelamar->user_id,
                $job->nama,
                $status
            );
        }

        return true;
    }

    public function show($id)
    {
        $job = Pekerjaan::findOrFail($id);
        $active_navbar = 'Detail Pekerjaan';
        $nama_halaman = 'Detail Pekerjaan';
        
        return view('Dewa.Mitra.pekerjaan-detail', compact('job', 'active_navbar', 'nama_halaman'));
    }

    public function lamarPekerjaan($id)
    {
        if (!session()->has('account')) {
            return redirect('/Login')->with('error', 'Anda harus login terlebih dahulu');
        }

        $user = session('account');
        $pekerjaan = Pekerjaan::findOrFail($id);

        // Check if user is the job creator
        if ($pekerjaan->pembuat == $user['id']) {
            return redirect()->back()->with('error', 'Anda tidak dapat melamar pekerjaan yang Anda buat sendiri');
        }

        // Check if user has already applied
        $existingApplication = Pelamar::where('user_id', $user['id'])
                                     ->where('job_id', $id)
                                     ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'Anda sudah melamar pekerjaan ini');
        }

        // Create new application
        Pelamar::create([
            'user_id' => $user['id'],
            'job_id' => $id,
            'status' => 'pending'
        ]);

        // Create notification for the job creator (mitra)
        $applicant = Users::find($user['id']);
        if ($applicant) {
            Notification::createNewApplicationNotification(
                $pekerjaan->pembuat,
                $pekerjaan->nama,
                $applicant->nama
            );
        }

        return redirect()->back()->with('success', 'Lamaran berhasil dikirim!');
    }

    public function lowonganTerdaftar()
    {
        if (!session()->has('account')) {
            return redirect('/Login');
        }

        $user = session('account');
        
        // Get jobs created by current user (mitra) with their applicants
        $jobs = Pekerjaan::where('pembuat', $user['id'])
                ->with(['pelamar.user'])
                ->orderBy('created_at', 'desc')
                ->get();

        $active_navbar = 'Lowongan Terdaftar';
        $nama_halaman = 'Lowongan Terdaftar';

        return view('Dewa.Mitra.lowongan-terdaftar', compact('jobs', 'active_navbar', 'nama_halaman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        if ($pekerjaan->status == 'Open') {
            $pekerjaan->status = 'Berlangsung';
            $pekerjaan->save();
            return redirect()->back()->with('success', 'Status pekerjaan berhasil diubah menjadi On Progress.');
        }

        return redirect()->back()->with('error', 'Status pekerjaan tidak dapat diubah.');
    }

    public function terimaHasilPekerjaan(Request $request, $id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        if ($pekerjaan->status != 'Berlangsung') {
            return redirect()->back()->with('error', 'Pekerjaan belum dalam status Berlangsung.');
        }

        $pelamarDiterima = $pekerjaan->pelamar()->where('status', 'diterima')->first();

        if (!$pelamarDiterima) {
            return redirect()->back()->with('error', 'Tidak ada pelamar yang diterima untuk pekerjaan ini.');
        }

        $userPelamar = $pelamarDiterima->user;

        if (!$userPelamar) {
            return redirect()->back()->with('error', 'Data user pelamar tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            // Transfer min_gaji to pelamar's wallet
            $userPelamar->dompet += $pekerjaan->min_gaji;
            $userPelamar->save();

            // Update job status to Selesai
            $pekerjaan->status = 'Selesai';
            $pekerjaan->save();

            DB::commit();
            return redirect()->back()->with('success', 'Hasil pekerjaan berhasil diterima dan dana telah ditransfer.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menerima hasil pekerjaan: ' . $e->getMessage());
        }
    }

    function cosineSimilarityPercent($text1, $text2)
    {
        // 1. Hitung frekuensi kata dari masing-masing teks
        $words1 = array_count_values(str_word_count(strtolower($text1), 1));
        $words2 = array_count_values(str_word_count(strtolower($text2), 1));

        // 2. Gabungkan semua kata unik dari kedua teks
        $allWords = array_unique(array_merge(array_keys($words1), array_keys($words2)));

        // 3. Bangun vektor BoW untuk kedua teks
        $vec1 = [];
        $vec2 = [];

        foreach ($allWords as $word) {
            $vec1[] = $words1[$word] ?? 0;
            $vec2[] = $words2[$word] ?? 0;
        }

        // 4. Hitung dot product dan magnitude dari vektor
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $magnitude1 += $vec1[$i] ** 2;
            $magnitude2 += $vec2[$i] ** 2;
        }

        // 5. Jika salah satu vektor nol (kosong), hasil 0%
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        // 6. Hitung cosine similarity dan ubah ke persen
        $similarity = $dotProduct / (sqrt($magnitude1) * sqrt($magnitude2));
        return round($similarity * 100, 2); // Misal 0.75 → 75.00%
    }

    public function getThe20ies()
    {
        $result = [];
        $all = Pekerjaan::all();
        if (session()->has('account')){//apabila sudah login
    
            foreach ($all as $kerja) {
                //cek hasil CSP
                $decimal = $this->cosineSimilarityPercent(
                    json_encode($kerja),
                    json_encode(session('account')->preferensi_user)
                );
    
                //membatasi jumlah pekerjaan disarankan
                if (count($result) < 15) {
                    $result[$kerja->id] = $decimal;
                }
                //jika sudah mencapai jumlah, maka cut yg paling kecil didalam array
                else {
                    //angka berapa yg paling kecil dalam array
                    $minimal_value = min($result);
                    //cari keynya dari angka paling kecil
                    $key_minimal_value = array_search($minimal_value, $result);
                    
                    //cek jika hasil CSP lebih besar dr minimal value maka ganti
                    if ($decimal > $minimal_value) {
                        unset($result[$key_minimal_value]);// hapus yang terkecil
                        $result[$kerja->id] = $decimal;//ganti isi array
                    }
                }
            }
            (arsort($result));//urutkan array dari yg terbesar ke terkecil
            // dd($result);
        }
        else{
            // $result = $all;
            // dd($result,'belum');
        }

        return ($result);
    }

    function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }
}
