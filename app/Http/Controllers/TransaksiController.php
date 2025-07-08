<?php

namespace App\Http\Controllers;

use App\Models\Pelamar;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Str;


class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::where('pembuat_id', auth()->id())
            ->orWhere('pekerja_id', auth()->id())
            ->paginate(10);

        $user = Auth::user();

        return view('users.transaksi', compact('transaksi','user'));
    }

    public function buatTransaksi(Request $request, $pekerjaId)
    {
        try {
            $user = Auth::user();
            $today = Carbon::now();
            $request->validate([
                'jumlah' => 'required|integer|min:1'
            ]);
            $uuid = Str::uuid();
            $transaksi = Transaksi::create([
                'kode' => (string) Str::uuid(),
                'pembuat_id' => $user->id,
                'pekerja_id' => $pekerjaId,
                'jumlah' => $request->jumlah,
                'dibuat' => $today->toDateString(),
                'status' => 'tertunda'
            ]);
            
            return response()->json(['message' => 'Transaksi berhasil dibuat', 'transaksi' => $transaksi]);
        } catch (\Exception $e) {
            dd(get_defined_vars());
            Log::error('Gagal membuat transaksi: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat transaksi, silakan coba lagi.'], 500);
        }
    }

    public function setujuiTransaksi($kode)
    {
        $transaksi = Transaksi::where('kode', $kode)->firstOrFail();
    
        if ($transaksi->status === 'tertunda' && Auth::user()->isAdmin) {
            $transaksi->status = 'sukses';
            $transaksi->save();
    
            $pekerja = Users::find($transaksi->pekerja_id);
            $pekerja->dompet += $transaksi->jumlah;
            $pekerja->save();
    
            return response()->json(['message' => 'Transaksi disetujui', 'transaksi' => $transaksi]);
        }
    
        return response()->json(['message' => 'Anda tidak memiliki izin atau transaksi tidak dapat disetujui'], 403);
    }
    
    

    public function tolakTransaksi($kode)
    {
        $transaksi = Transaksi::where('kode', $kode)->firstOrFail();
    
        if (Auth::user()->isAdmin) {
            $transaksi->status = 'tolak';
            $transaksi->save();
    
            return response()->json(['message' => 'Transaksi ditolak', 'transaksi' => $transaksi]);
        }
    
        return response()->json(['message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini'], 403);
    }
    
}
