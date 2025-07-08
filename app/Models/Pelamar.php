<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelamar extends Model
{
    /** @use HasFactory<\Database\Factories\PelamarFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function sidejob()
    {
        return $this->belongsTo(Pekerjaan::class, 'job_id');
    }
    
    public function getStatusPekerjaan()
    {
        if ($this->status == 'diterima') {
            // Check if the related job is marked as completed
            if ($this->sidejob && $this->sidejob->status == 'Selesai') {
                return 'Selesai';
            }
            return 'Dalam Pengerjaan';
        } elseif ($this->status == 'pending') {
            return 'Menunggu diterima oleh Mitra';
        } else {
            return ucfirst($this->status);
        }
    }
}
