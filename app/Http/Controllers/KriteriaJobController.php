<?php

namespace App\Http\Controllers;
use App\Models\KriteriaJob;
use App\Models\Pekerjaan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KriteriaJobController extends Controller
{
    public function cek_exist($nama){
        $cek = KriteriaJob::where('nama',ucwords($nama))->first();
        if($cek==null){
            return false;
        }
        else{
            return $cek->id;
        }        
    }

    public function store($nama){
        // KriteriaJob::create(($nama));
        $kriteria = new KriteriaJob();
        $kriteria->nama = $nama;
        if($kriteria->save()){
            return $kriteria;
        }
        else{
            return false;
        }
    }
}
