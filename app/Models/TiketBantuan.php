<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketBantuan extends Model
{
    use HasFactory;

    protected $table = 'TiketBantuan';

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'description',
        'status',
        'admin_response',
        'pihak_terlapor',
        'tanggal_kejadian',
        'bukti_pendukung',
    ];

    protected $casts = [
        'bukti_pendukung' => 'array',
        'tanggal_kejadian' => 'date',
    ];

    /**
     * Ticket belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users::class, 'user_id');
    }
}
