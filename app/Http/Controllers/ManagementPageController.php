<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Pelamar;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Jika Anda memerlukan info Auth
use App\Models\Transaksi;
use App\Models\User;
use App\Models\FinancialTransaction;
use App\Models\Users;
use App\Models\TiketBantuan;
use App\Models\Rating;
use App\Services\TarikSaldoService;
use Carbon\Carbon;

class ManagementPageController extends Controller
{
    // Pastikan user terautentikasi untuk mengakses halaman manajemen
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     // Anda mungkin ingin menambahkan middleware admin untuk beberapa rute di sini atau di file rute
    // }

    public function dashboard()
    {
        $user = session('account');
        $totalPekerjaans = Pekerjaan::where('pembuat', $user['id'])
                  ->where('is_active', '1')
                  ->count();
        $totalPekerja = Users::where('role', 'user')->count();
        
        // Get fresh user data from database to ensure balance is current
        $currentUser = Users::find($user['id']);
        
        // Update session with fresh data if balance is different
        if ($currentUser && $currentUser->dompet != $user->dompet) {
            session(['account' => $currentUser]);
            $user = $currentUser;
        }
        
        return view('manajemen.dashboard', compact('totalPekerjaans','totalPekerja', 'user'));
    }

    // --- Fitur Manajemen Utama ---
    public function pekerjaanBerlangsung()
    {
        $user = session('account');
        $pekerjaanBerlangsung = [];
        
        if ($user->isUser()) {
            // For regular users, get jobs they've applied to and been accepted or are pending
            $pekerjaanBerlangsung = Pelamar::where('user_id', $user->id)
                ->with(['sidejob', 'user'])
                ->get();
        } elseif ($user->isMitra()) {
            // For mitra, get jobs they've created that have applicants
            $pekerjaanIds = Pekerjaan::where('pembuat', $user->id)
                ->pluck('id');
                
            $pekerjaanBerlangsung = Pelamar::whereIn('job_id', $pekerjaanIds)
                ->with(['sidejob', 'user'])
                ->get();
        }
        
        // Load the job creators for each job
        foreach ($pekerjaanBerlangsung as $pelamar) {
            if ($pelamar->sidejob) {
                $pembuatId = $pelamar->sidejob->pembuat;
                $pembuatUser = Users::find($pembuatId);
                $pelamar->sidejob->pembuatUser = $pembuatUser;
            }
        }
        
        return view('manajemen.pekerjaan.berlangsung', compact('pekerjaanBerlangsung'));
    }

    public function pekerjaanTerdaftar()
    {
        $user = session('account');
        $pekerjaans = Pekerjaan::where('pembuat', $user['id'])
                               ->with(['pelamar', 'pembuat'])
                               ->orderBy('created_at', 'desc')
                               ->get();
        
        return view('manajemen.pekerjaan.terdaftar', compact('pekerjaans'));
    }

    public function uploadLaporan()
    {
        $user = session('account');
        $jobs = Pelamar::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->with('sidejob')
            ->get()
            ->pluck('sidejob')
            ->filter(function($job) {
                // Only show jobs that are not yet finished
                return $job && $job->status !== 'Selesai';
            })
            ->unique('id');

        return view('manajemen.laporan.upload', compact('jobs'));
    }

    public function storeLaporan(Request $request)
    {
        $user = session('account');

        $request->validate([
            'pekerjaan_id' => 'required|exists:pekerjaans,id',
            'deskripsi_laporan' => 'required|string',
            'foto_selfie' => 'required|image',
            'dokumentasi_pekerjaan.*' => 'required|image',
        ]);

        // Check if the job is already completed
        $pekerjaan = Pekerjaan::find($request->pekerjaan_id);
        if ($pekerjaan && $pekerjaan->status === 'Selesai') {
            return redirect()->back()->with('error', 'Tidak dapat mengunggah laporan untuk pekerjaan yang sudah selesai.');
        }

        // Check if user is actually assigned to this job
        $pelamar = Pelamar::where('user_id', $user->id)
            ->where('job_id', $request->pekerjaan_id)
            ->where('status', 'diterima')
            ->first();
        
        if (!$pelamar) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunggah laporan untuk pekerjaan ini.');
        }

        // Check if a report has already been submitted for this job by this user
        $existingLaporan = Laporan::where('job_id', $request->pekerjaan_id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($existingLaporan) {
            return redirect()->back()->with('error', 'Anda sudah mengunggah laporan untuk pekerjaan ini sebelumnya.');
        }

        $selfiePath = $request->file('foto_selfie')->store('laporan/selfie', 'public');

        $dokPaths = [];
        foreach ($request->file('dokumentasi_pekerjaan', []) as $file) {
            $dokPaths[] = $file->store('laporan/dokumentasi', 'public');
        }

        Laporan::create([
            'job_id' => $request->pekerjaan_id,
            'user_id' => $user->id,
            'deskripsi' => $request->deskripsi_laporan,
            'foto_selfie' => $selfiePath,
            'foto_dokumentasi' => json_encode($dokPaths),
        ]);

        return redirect()->route('manajemen.laporan.upload')->with('success', 'Laporan berhasil dikirim.');
    }

    public function topUp()
    {
        return view('manajemen.keuangan.topUp');
    }

    public function tarikSaldo()
    {
        $user = session('account');
        $userModel = Users::find($user['id']);
        $supportedBanks = TarikSaldoService::getSupportedBanks();
        $supportedEwallets = TarikSaldoService::getSupportedEwallets();
        
        // Get recent payouts
        $recentPayouts = FinancialTransaction::where('user_id', $user['id'])
            ->payouts()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('manajemen.keuangan.tarik_saldo', compact('userModel', 'supportedBanks', 'supportedEwallets', 'recentPayouts'));
    }

    public function riwayatTransaksi()
    {
        // Fetch both payments and payouts for logged-in user
        $perPage = 10;
        $transaksi = FinancialTransaction::where('user_id', session('account')['id'])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        return view('manajemen.keuangan.riwayat_transaksi', compact('transaksi'));
    }

    /**
     * AJAX endpoint to fetch riwayat transaksi data without page reload
     */
    public function riwayatTransaksiData(Request $request)
    {
        $user = session('account');
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10);
        // Determine per page count
        if ($perPage === 'all') {
            $perPage = FinancialTransaction::where('user_id', $user['id'])->count();
        } else {
            $perPage = (int) $perPage;
        }
        $query = FinancialTransaction::where('user_id', $user['id']);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('external_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('xendit_reference_id', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%");
            });
        }
        $transaksi = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'per_page' => $request->query('per_page')]);

        // Render partials
        $rows = view('manajemen.keuangan.partials.riwayat_transaksi_rows', compact('transaksi'))->render();
        $pagination = view('manajemen.keuangan.partials.riwayat_transaksi_pagination', compact('transaksi'))->render();

        return response()->json(['table' => $rows, 'pagination' => $pagination]);
    }

    public function refundDana()
    {
        return view('manajemen.keuangan.refund_dana');
    }

    public function laporanKeuangan(Request $request)
    {
        $user = session('account');
        
        // Get month and year from request or use 'all' to show all transactions
        $selectedMonth = $request->get('month', 'all');
        $selectedYear = $request->get('year', 'all');
        
        // Base query for top-up transactions
        $topUpQuery = FinancialTransaction::where('user_id', $user->id)
            ->payments()
            ->whereIn('status', ['completed']);
        
        // Apply month/year filter only if specific month/year is selected
        if ($selectedMonth !== 'all' && $selectedYear !== 'all') {
            $topUpQuery->whereMonth('created_at', $selectedMonth)
                      ->whereYear('created_at', $selectedYear);
        } elseif ($selectedMonth !== 'all') {
            // Only month is selected, filter by month for current year
            $topUpQuery->whereMonth('created_at', $selectedMonth)
                      ->whereYear('created_at', now()->year);
        } elseif ($selectedYear !== 'all') {
            // Only year is selected, show all months for that year
            $topUpQuery->whereYear('created_at', $selectedYear);
        }
        
        $topUpTransactions = $topUpQuery->orderBy('created_at', 'desc')->get();
        
        // Base query for job income transactions
        $jobIncomeQuery = Transaksi::where('pekerja_id', $user->id)
            ->where('status', 'sukses');
        
        // Apply month/year filter only if specific month/year is selected
        if ($selectedMonth !== 'all' && $selectedYear !== 'all') {
            $jobIncomeQuery->whereMonth('dibuat', $selectedMonth)
                          ->whereYear('dibuat', $selectedYear);
        } elseif ($selectedMonth !== 'all') {
            // Only month is selected, filter by month for current year
            $jobIncomeQuery->whereMonth('dibuat', $selectedMonth)
                          ->whereYear('dibuat', now()->year);
        } elseif ($selectedYear !== 'all') {
            // Only year is selected, show all months for that year
            $jobIncomeQuery->whereYear('dibuat', $selectedYear);
        }
        
        $jobIncomeTransactions = $jobIncomeQuery->orderBy('dibuat', 'desc')->get();
        
        // Base query for job expense transactions
        $jobExpenseQuery = Transaksi::where('pembuat_id', $user->id)
            ->where('status', 'sukses');
        
        // Apply month/year filter only if specific month/year is selected
        if ($selectedMonth !== 'all' && $selectedYear !== 'all') {
            $jobExpenseQuery->whereMonth('dibuat', $selectedMonth)
                           ->whereYear('dibuat', $selectedYear);
        } elseif ($selectedMonth !== 'all') {
            // Only month is selected, filter by month for current year
            $jobExpenseQuery->whereMonth('dibuat', $selectedMonth)
                           ->whereYear('dibuat', now()->year);
        } elseif ($selectedYear !== 'all') {
            // Only year is selected, show all months for that year
            $jobExpenseQuery->whereYear('dibuat', $selectedYear);
        }
        
        $jobExpenseTransactions = $jobExpenseQuery->orderBy('dibuat', 'desc')->get();
        
        // Combine all transactions for display
        $allTransactions = collect();
        
        // Add top-up transactions as income
        foreach ($topUpTransactions as $transaction) {
            $allTransactions->push([
                'date' => $transaction->created_at,
                'description' => $transaction->description ?: 'Top Up Saldo',
                'income' => $transaction->amount,
                'expense' => 0,
                'type' => 'topup',
                'transaction' => $transaction
            ]);
        }
        
        // Add job income transactions
        foreach ($jobIncomeTransactions as $transaction) {
            $allTransactions->push([
                'date' => $transaction->dibuat,
                'description' => 'Pendapatan Pekerjaan',
                'income' => $transaction->jumlah,
                'expense' => 0,
                'type' => 'job_income',
                'transaction' => $transaction
            ]);
        }
        
        // Add job expense transactions
        foreach ($jobExpenseTransactions as $transaction) {
            $allTransactions->push([
                'date' => $transaction->dibuat,
                'description' => 'Pembayaran Pekerjaan',
                'income' => 0,
                'expense' => $transaction->jumlah,
                'type' => 'job_expense',
                'transaction' => $transaction
            ]);
        }
        
        // Sort by date (newest first)
        $allTransactions = $allTransactions->sortByDesc('date');
        
        // Calculate totals
        $totalIncome = $allTransactions->sum('income');
        $totalExpense = $allTransactions->sum('expense');
        $netIncome = $totalIncome - $totalExpense;
        
        // Calculate transaction counts by type
        $topUpCount = $topUpTransactions->count();
        $jobIncomeCount = $jobIncomeTransactions->count();
        $jobExpenseCount = $jobExpenseTransactions->count();
        $totalTransactions = $allTransactions->count();
        
        // Calculate averages
        $avgIncome = $totalIncome > 0 ? $totalIncome / $allTransactions->where('income', '>', 0)->count() : 0;
        $avgExpense = $totalExpense > 0 ? $totalExpense / $allTransactions->where('expense', '>', 0)->count() : 0;
        
        // Get month name in Indonesian
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $monthName = $selectedMonth !== 'all' ? $monthNames[$selectedMonth] : 'Semua Bulan';
        
        // Create a more descriptive period name
        if ($selectedMonth !== 'all' && $selectedYear !== 'all') {
            $periodName = $monthNames[$selectedMonth] . ' ' . $selectedYear;
        } elseif ($selectedMonth !== 'all') {
            $periodName = $monthNames[$selectedMonth] . ' ' . now()->year;
        } elseif ($selectedYear !== 'all') {
            $periodName = 'Semua Bulan ' . $selectedYear;
        } else {
            $periodName = 'Semua Periode';
        }
        
        // Get available years for dropdown (from 2020 to current year + 1)
        $availableYears = range(2020, now()->year + 1);
        
        return view('manajemen.keuangan.laporan_keuangan', compact(
            'allTransactions', 
            'totalIncome', 
            'totalExpense', 
            'netIncome',
            'monthName',
            'selectedMonth',
            'selectedYear',
            'availableYears',
            'monthNames',
            'topUpCount',
            'jobIncomeCount',
            'jobExpenseCount',
            'totalTransactions',
            'avgIncome',
            'avgExpense',
            'periodName'
        ));
    }

    public function panelBantuanDanPenipuan()
    {
        $user = session('account');
        if ($user->isAdmin()) {
            $tickets = TiketBantuan::with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $tickets = TiketBantuan::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }
        return view('manajemen.bantuan.panel', compact('tickets', 'user'));
    }

    public function storeBantuanDanPenipuan(Request $request)
    {
        $user = session('account');
        $type = $request->input('type', 'bantuan');
        
        if ($type === 'penipuan') {
            $data = $request->validate([
                'type' => 'required|in:bantuan,penipuan',
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'pihak_terlapor' => 'required|string|max:255',
                'tanggal_kejadian' => 'required|date',
                'bukti_pendukung.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,gif|max:10240',
            ]);
            
            $buktiPaths = [];
            if ($request->hasFile('bukti_pendukung')) {
                foreach ($request->file('bukti_pendukung') as $file) {
                    $buktiPaths[] = $file->store('bukti_penipuan', 'public');
                }
            }
            
            TiketBantuan::create([
                'user_id' => $user->id,
                'type' => 'penipuan',
                'subject' => $data['subject'],
                'description' => $data['description'],
                'pihak_terlapor' => $data['pihak_terlapor'],
                'tanggal_kejadian' => $data['tanggal_kejadian'],
                'bukti_pendukung' => $buktiPaths,
            ]);
            
            return redirect()->route('manajemen.bantuan.panel')->with('success', 'Laporan penipuan berhasil dikirim.');
        } else {
            $data = $request->validate([
                'type' => 'required|in:bantuan,penipuan',
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
            ]);
            
            TiketBantuan::create([
                'user_id' => $user->id,
                'type' => 'bantuan',
                'subject' => $data['subject'],
                'description' => $data['description'],
            ]);
            
            return redirect()->route('manajemen.bantuan.panel')->with('success', 'Tiket bantuan berhasil dibuat.');
        }
    }

    public function panelBantuan()
    {
        return $this->panelBantuanDanPenipuan();
    }

    public function respondTicket(Request $request, $id)
    {
        $ticket = TiketBantuan::findOrFail($id);
        $data = $request->validate([
            'admin_response' => 'required|string',
        ]);
        $ticket->status = 'closed';
        $ticket->admin_response = $data['admin_response'];
        $ticket->save();
        return redirect()->route('manajemen.bantuan.panel')->with('success', 'Tiket telah ditutup.');
    }

    // --- Fitur Administrasi Sistem (Contoh) ---
    public function pemantauanLaporanAdmin()
    {
        // $this->authorize('viewAdminContent'); // Contoh otorisasi
        return view('manajemen.admin.laporan_pemantauan');
    }

    public function usersListAdmin()
    {
        // $this->authorize('manageUsers');
        // Logika untuk mengambil daftar user
        return view('manajemen.admin.users.list');
    }

    public function usersTambahAdmin()
    {
        // $this->authorize('manageUsers');
        return view('manajemen.admin.users.tambah');
    }
    
    // --- Fitur Manajemen Lainnya ---
    public function notifikasiStatusPekerjaan()
    {
        return view('manajemen.notifikasi.status_pekerjaan');
    }

    public function notifikasiStatusPelamaran()
    {
        return view('manajemen.notifikasi.status_pelamaran');
    }

    public function chatPengguna()
    {

        return view('manajemen.chat.panel');
    }
    
    public function riwayatPekerjaan()
    {
        $user = session('account');
        
        // Get completed jobs for the user
        $riwayatPekerjaan = Pelamar::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->with(['sidejob', 'user'])
            ->whereHas('sidejob', function($query) {
                $query->where('status', 'Selesai');
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Load the job creators and ratings for each job
        foreach ($riwayatPekerjaan as $pelamar) {
            if ($pelamar->sidejob) {
                $pembuatId = $pelamar->sidejob->pembuat;
                $pembuatUser = Users::find($pembuatId);
                $pelamar->sidejob->pembuatUser = $pembuatUser;
                
                // Load ratings for this job - both directions
                $jobId = $pelamar->sidejob->id;
                
                // Rating given by worker to employer
                $workerToEmployerRating = Rating::where('job_id', $jobId)
                    ->where('rater_id', $user->id)
                    ->where('rated_id', $pembuatId)
                    ->where('type', 'worker_to_employer')
                    ->first();
                
                // Rating given by employer to worker
                $employerToWorkerRating = Rating::where('job_id', $jobId)
                    ->where('rater_id', $pembuatId)
                    ->where('rated_id', $user->id)
                    ->where('type', 'employer_to_worker')
                    ->first();
                
                $pelamar->workerToEmployerRating = $workerToEmployerRating;
                $pelamar->employerToWorkerRating = $employerToWorkerRating;
            }
        }
        
        return view('manajemen.pekerjaan.riwayat', compact('riwayatPekerjaan'));
    }

    public function managePekerjaan($id)
    {
        $user = session('account');
        $pekerjaan = Pekerjaan::with('pelamar')->findOrFail($id);

        if (!$user->isAdmin() && $pekerjaan->pembuat != $user->id) {
            abort(403);
        }

        $laporans = Laporan::where('job_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manajemen.pekerjaan.manage', compact('pekerjaan', 'laporans'));
    }


    public function storeJobRating(Request $request, $jobId)
    {
        $request->validate([
            'pekerja_id' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500'
        ]);

        $user = session('account');
        $pekerjaan = Pekerjaan::findOrFail($jobId);

        // Check if user can rate this worker for this job
        if (!Rating::canRate($user->id, $request->pekerja_id, $jobId, 'employer_to_worker')) {
            return redirect()->back()->with('rating_error', 'Anda tidak dapat memberikan rating untuk pekerjaan ini.');
        }

        try {
            // Create the rating
            Rating::create([
                'rater_id' => $user->id,
                'rated_id' => $request->pekerja_id,
                'job_id' => $jobId,
                'rating' => $request->rating,
                'comment' => $request->komentar,
                'type' => 'employer_to_worker'
            ]);

            return redirect()->back()->with('rating_success', 'Rating berhasil diberikan kepada pekerja!');
        } catch (\Exception $e) {
            return redirect()->back()->with('rating_error', 'Gagal memberikan rating. Silakan coba lagi.');
        }
    }

    /**
     * Store worker rating for employer
     */
    public function storeWorkerRating(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:pekerjaans,id',
            'employer_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = session('account');

        // Check if user can rate this employer for this job
        if (!Rating::canRate($user->id, $request->employer_id, $request->job_id, 'worker_to_employer')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat memberikan rating untuk pekerjaan ini.'], 403);
        }

        try {
            // Create the rating
            Rating::create([
                'rater_id' => $user->id,
                'rated_id' => $request->employer_id,
                'job_id' => $request->job_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'type' => 'worker_to_employer'
            ]);

            return response()->json(['success' => true, 'message' => 'Rating berhasil diberikan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memberikan rating. Silakan coba lagi.'], 500);
        }
    }

    public function trackRecordPelamar()
    {
        $user = session('account');
        
        // Only allow mitra and admin to access this page
        if (!$user->isMitra() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        // Get jobs created by the current mitra
        $mitraJobIds = Pekerjaan::where('pembuat', $user->id)->pluck('id');
        
        // Get applicants who are currently applying (status = 'pending') to mitra's jobs
        $pendingApplicants = Pelamar::whereIn('job_id', $mitraJobIds)
            ->where('status', 'pending')
            ->with(['user', 'sidejob'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // For each applicant, get their job history (track record)
        $applicantsWithHistory = [];
        
        foreach ($pendingApplicants as $applicant) {
            $userId = $applicant->user_id;
            
            // Skip if we already processed this user
            if (isset($applicantsWithHistory[$userId])) {
                continue;
            }
            
            // Get user's complete job history
            $jobHistory = Pelamar::where('user_id', $userId)
                ->with(['sidejob.pembuat'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Calculate statistics
            $totalJobs = $jobHistory->count();
            $completedJobs = $jobHistory->where('status', 'diterima')
                ->filter(function($job) {
                    if (!$job->sidejob) return false;
                    $jobStatus = trim(strtolower($job->sidejob->status));
                    return in_array($jobStatus, ['selesai', 'completed', 'finished', 'done']);
                })->count();
            $rejectedJobs = $jobHistory->where('status', 'ditolak')->count();
            $pendingJobs = $jobHistory->where('status', 'pending')->count();
            
            // Calculate completion rate
            $completionRate = $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 1) : 0;
            
            $applicantsWithHistory[$userId] = [
                'user' => $applicant->user,
                'current_application' => $applicant,
                'job_history' => $jobHistory,
                'statistics' => [
                    'total_jobs' => $totalJobs,
                    'completed_jobs' => $completedJobs,
                    'rejected_jobs' => $rejectedJobs,
                    'pending_jobs' => $pendingJobs,
                    'completion_rate' => $completionRate
                ]
            ];
        }
        
        return view('manajemen.pelamar.track_record', compact('applicantsWithHistory'));
    }

    /**
     * Securely serve evidence files for admin users
     */
    public function serveEvidenceFile($ticketId, $fileIndex)
    {
        $user = session('account');
        
        // Only allow admin users to access evidence files
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        $ticket = TiketBantuan::findOrFail($ticketId);
        
        // Check if file index exists
        if (!isset($ticket->bukti_pendukung[$fileIndex])) {
            abort(404, 'File not found');
        }
        
        $filePath = $ticket->bukti_pendukung[$fileIndex];
        $fullPath = storage_path('app/public/' . $filePath);
        
        // Check if file exists
        if (!file_exists($fullPath)) {
            abort(404, 'File not found on disk');
        }
        
        // Get file info
        $fileName = basename($filePath);
        $mimeType = mime_content_type($fullPath);
        
        // Return file response
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function deletePekerjaan(Request $request, $id)
    {
        $user = session('account');
        $pekerjaan = Pekerjaan::findOrFail($id);

        // Only allow owner and only if status is 'Open'
        if ($pekerjaan->pembuat != $user->id || $pekerjaan->status !== 'Open') {
            return redirect()->back()->with('error', 'Pekerjaan hanya dapat dihapus jika status masih Open dan Anda adalah pemiliknya.');
        }

        // Refund min_gaji to owner's dompet
        $owner = Users::find($pekerjaan->pembuat);
        if ($owner) {
            $owner->dompet += $pekerjaan->min_gaji;
            $owner->save();
            // Optionally, update session if current user is owner
            if ($user->id == $owner->id) {
                session(['account' => $owner]);
            }
        }

        // Delete the pekerjaan
        $pekerjaan->delete();

        return redirect()->route('manajemen.pekerjaan.terdaftar')
                         ->with('success', 'Pekerjaan berhasil dihapus dan dana sebesar Rp. ' . number_format($pekerjaan->min_gaji, 0, ',', '.') . ' telah dikembalikan ke Dompet Anda.');
    }

}
