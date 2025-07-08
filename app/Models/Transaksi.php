<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'kode';
    protected $keyType = 'string'; // Ensure key type is string

    protected $fillable = [
        'kode', 'pembuat_id', 'pekerja_id', 'jumlah', 'dibuat'
    ];

    protected $casts = [
        'kode' => 'string',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function pekerja()
    {
        return $this->belongsTo(User::class, 'pekerja_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function job()
    {
        return $this->belongsTo(SideJob::class, 'job_id');
    }
}
