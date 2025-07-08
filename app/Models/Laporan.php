<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'deskripsi',
        'foto_selfie',
        'foto_dokumentasi',
    ];

    public function job()
    {
        return $this->belongsTo(Pekerjaan::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
