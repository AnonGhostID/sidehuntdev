<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PekerjaanController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SideJobController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ManagementPageController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\PayoutController;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;

//DEWA
Route::get('/', function () {
    return redirect('/Index');
});


//Auth
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/Index', [HomeController::class, 'index'])->name('home');
Route::get('/Login', [HomeController::class, 'Login']);
Route::get('/Register', [HomeController::class, 'Register']);
Route::get('/Logout', [UsersController::class, 'logout']);
Route::get('/NotAllowed', function(){
    $nama_halaman = 'Akses Ditolak';
    $active_navbar = 'none';
    return view('Dewa.NotAllowedPage', compact('nama_halaman','active_navbar'));
});



Route::post('/Login_account', [UsersController::class, 'Login_Account']);
Route::post('/Register_account', action: [UsersController::class, 'create']);
Route::get('/kerja/', action: [PekerjaanController::class, 'index']);
Route::get('/kerja/create', [PekerjaanController::class, 'create'])->middleware(['role:mitra|admin']);
Route::get('/kerja/{id}', [PekerjaanController::class, 'show'])->name('pekerjaan.show');
Route::post('/kerja/lamar/{id}', [PekerjaanController::class, 'lamarPekerjaan'])->name('pekerjaan.lamar')->middleware(['role:user']);

Route::middleware(['role:user|mitra|admin'])->group(function () {
    Route::post('/user/preferensi/save', action: [UsersController::class, 'save_preverensi']);
    Route::post('/kerja/add', action: [PekerjaanController::class, 'store']);
    Route::post('/Profile/Edit', [UsersController::class, 'Profile_Edit']);
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('user.transaksi');
    //Kerja
    Route::get('/question-new-user', action: [HomeController::class, 'new_user']);

    //NewUser

    //Profile
    Route::get('/Profile', [UsersController::class, 'Profile']);
    
    // Routes for mitra and user only
    Route::middleware(['role:user|mitra|admin'])->group(function () {
        Route::prefix('management')->name('manajemen.')->group(function () {
            Route::get('/', [ManagementPageController::class, 'dashboard'])->name('dashboard');
    
            // Pekerjaan
            Route::get('/pekerjaan-berlangsung', [ManagementPageController::class, 'pekerjaanBerlangsung'])->name('pekerjaan.berlangsung');
            Route::get('/pekerjaan-terdaftar', [ManagementPageController::class, 'pekerjaanTerdaftar'])->name('pekerjaan.terdaftar');
            Route::get('/upload-laporan', [ManagementPageController::class, 'uploadLaporan'])->name('laporan.upload');
            Route::post('/upload-laporan', [ManagementPageController::class, 'storeLaporan'])->name('laporan.store');
            Route::get('/riwayat-pekerjaan', [ManagementPageController::class, 'riwayatPekerjaan'])->name('pekerjaan.riwayat');
            Route::get('/pekerjaan/{id}/manage', [ManagementPageController::class, 'managePekerjaan'])->name('pekerjaan.manage');
            Route::post('/pekerjaan/{id}/update-status', [PekerjaanController::class, 'updateStatus'])->name('pekerjaan.updateStatus');
            Route::post('/pekerjaan/{id}/terima-hasil', [PekerjaanController::class, 'terimaHasilPekerjaan'])->name('pekerjaan.terimaHasil');
            Route::post('/pekerjaan/{id}/rating', [ManagementPageController::class, 'storeJobRating'])->name('pekerjaan.rating.store');
            Route::post('/rating/worker', [ManagementPageController::class, 'storeWorkerRating'])->name('rating.worker.store');
    
            // Keuangan
            // Route::get('/gateway-pembayaran', [ManagementPageController::class, 'gatewayPembayaran'])->name('pembayaran.gateway');
            Route::get('/Top-Up', [ManagementPageController::class, 'topUp'])->name('topUp');
            Route::post('/Top-Up', [TopUpController::class, 'store'])->name('topup.store');
    
            // TopUp Controller disini
            Route::get('/Top-Up/{external_id}', [TopUpController::class, 'payment'])->name('topup.payment');
            Route::post('/Top-Up/check-status', [TopUpController::class, 'checkStatus'])->name('topup.check-status');
            Route::post('/Top-Up/expire-timeout', [TopUpController::class, 'expireOnTimeout'])->name('topup.expire-timeout');
            Route::post('/Top-Up/cancel/{external_id}', [TopUpController::class, 'cancel'])->name('topup.cancel');
            //
            Route::get('/tarik-saldo', [ManagementPageController::class, 'tarikSaldo'])->name('tarik_saldo');
            Route::post('/tarik-saldo', [\App\Http\Controllers\PayoutController::class, 'store'])->name('payout.store');
            Route::get('/tarik-saldo/history', [\App\Http\Controllers\PayoutController::class, 'history'])->name('payout.history');
            Route::get('/tarik-saldo/balance', [\App\Http\Controllers\PayoutController::class, 'checkBalance'])->name('payout.balance');
            Route::get('/riwayat-transaksi', [ManagementPageController::class, 'riwayatTransaksi'])->name('transaksi.riwayat');
            // AJAX endpoint for fetching riwayat transaksi data without page reload
            Route::get('/riwayat-transaksi/data', [ManagementPageController::class, 'riwayatTransaksiData'])->name('transaksi.riwayat.data');
            Route::get('/refund-dana', [ManagementPageController::class, 'refundDana'])->name('dana.refund');
            Route::get('/laporan-keuangan', [ManagementPageController::class, 'laporanKeuangan'])->name('keuangan.laporan');
    
            // Pelaporan & Bantuan (Unified)
            Route::get('/panel-bantuan', [ManagementPageController::class, 'panelBantuan'])->name('bantuan.panel');
            Route::post('/panel-bantuan', [ManagementPageController::class, 'storeBantuanDanPenipuan'])->name('bantuan.store');
            Route::post('/panel-bantuan/{id}/respond', [ManagementPageController::class, 'respondTicket'])->name('bantuan.respond');
    
            // Rute Notifikasi
            Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/notifications/page', [App\Http\Controllers\NotificationController::class, 'page'])->name('notifications.page');
            Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
            Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
            Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
            Route::get('/notifications/poll', [App\Http\Controllers\NotificationController::class, 'poll'])->name('notifications.poll');

            // Fitur Lainnya
            Route::get('/chat', [ManagementPageController::class, 'chatPengguna'])->name('chat');
            Route::get('/track-record-pelamar', [ManagementPageController::class, 'trackRecordPelamar'])->name('pelamar.track-record');
            Route::post('/transaksi/{jobId}', [TransaksiController::class, 'buatTransaksi'])->name('transaksi.buat');
            Route::post('/pekerjaan/{id}/delete', [ManagementPageController::class, 'deletePekerjaan'])->name('pekerjaan.delete');
        });
    });

    // Admin-only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('management/admin')->name('manajemen.admin.')->group(function () {
            Route::get('/pemantauan-laporan', [ManagementPageController::class, 'pemantauanLaporanAdmin'])->name('laporan.pemantauan');
            Route::get('/users', [ManagementPageController::class, 'usersListAdmin'])->name('users.list');
            Route::get('/users/tambah', [ManagementPageController::class, 'usersTambahAdmin'])->name('users.tambah');
        });
        
        Route::get('/admin', [HomeController::class, 'admin'])->name('admin.index');
        Route::get('/admin/user/{id}/edit', [UsersController::class, 'showAdmin'])->name('admin.show.profile');
        Route::match(['get', 'put'], '/admin/user/{id}', [UsersController::class, 'update'])->name('admin.update.profile');
        Route::get('/admin/user/edit/{id}', [UsersController::class, 'edit'])->name('admin.edit.profile');
        Route::get('/admin/user/delete/{id}', [UsersController::class, 'delete'])->name('admin.delete.profile');
        Route::get('/admin/transaksi/setujui/{kode}', [TransaksiController::class, 'setujuiTransaksi'])->name('admin.transaksi.setuju');
        Route::post('/admin/transaksi/tolak/{kode}', [TransaksiController::class, 'tolakTransaksi'])->name('admin.transaksi.tolak');
    });
});

//Only Mitra
Route::middleware(['role:mitra|admin'])->group(function () {
    Route::get('/dewa/mitra/lowongan-terdaftar', [PekerjaanController::class, 'lowonganTerdaftar'])->name('dewa.mitra.lowongan.terdaftar');
    Route::patch('/dewa/mitra/pelamar/{pelamar}/terima', [PekerjaanController::class, 'terima'])->name('dewa.mitra.pelamar.terima');
    Route::patch('/dewa/mitra/pelamar/{pelamar}/tolak', [PekerjaanController::class, 'tolak'])->name('dewa.mitra.pelamar.tolak');
});

//End Dewa

// Auth::routes();

// Route::get('/cari', [SideJobController::class, 'cari'])->name('sidejob.cari');
// Route::get('/job/{id}', [SideJobController::class, 'show'])->name('sidejob.detail');



// Route::get('/management', [HomeController::class, 'management'])->name('management');
// Route::get('/management', [HomeController::class, 'management'])->name('management')->middleware('isAdmin');
// Route::middleware(['auth'])->group(function () {
// Route::get('/user/lamaran', [UsersController::class, 'pelamaran'])->name('user.history');
// Route::get('/sidejob', [SideJobController::class, 'index'])->name('sidejob.index');
// Route::get('/sidejob/create', [SideJobController::class, 'create'])->name('sidejob.create');
// Route::post('/sidejob', [SideJobController::class, 'store'])->name('sidejob.store');
// Route::get('/sidejob/{id}', [SideJobController::class, 'show'])->name('sidejob.show');
// Route::get('/sidejob/{sidejob}/edit', [SideJobController::class, 'edit'])->name('sidejob.edit');
// Route::put('/sidejob/{sidejob}', [SideJobController::class, 'update'])->name('sidejob.update');
// Route::delete('/sidejob/{sidejob}', [SideJobController::class, 'destroy'])->name('sidejob.destroy');
// Route::post('/sidejob/{sidejob}/buatPermintaan', [SideJobController::class, 'buatPermintaan'])->name('sidejob.buatPermintaan');
// Route::patch('/pelamar/{pelamar}/terima', [SideJobController::class, 'terima'])->name('pelamar.terima');
// Route::patch('/pelamar/{pelamar}/tolak', [SideJobController::class, 'tolak'])->name('pelamar.tolak');

// });

// Route::middleware(['role', 'mitra'])->group(function () {
    

//Sidejob
// Route::get('/sidejob/{id}', [SideJobController::class, 'showAdmin'])->name('admin.sidejob.show');
// Route::get('/sidejob/edit/{id}', [SideJobController::class, 'editAdmin'])->name('admin.sidejob.edit');

// });
