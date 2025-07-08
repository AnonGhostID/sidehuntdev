<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create notification for job application status change
     */
    public static function createJobStatusNotification($userId, $jobName, $status)
    {
        $title = $status === 'diterima' ? 'Lamaran Diterima!' : 'Lamaran Ditolak';
        $message = $status === 'diterima' 
            ? "Selamat! Lamaran Anda untuk pekerjaan '{$jobName}' telah diterima."
            : "Maaf, lamaran Anda untuk pekerjaan '{$jobName}' tidak dapat diterima.";

        return self::create([
            'user_id' => $userId,
            'type' => 'application_status',
            'title' => $title,
            'message' => $message,
            'data' => [
                'job_name' => $jobName,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Create notification for new job application
     */
    public static function createNewApplicationNotification($mitraId, $jobName, $applicantName)
    {
        return self::create([
            'user_id' => $mitraId,
            'type' => 'new_application',
            'title' => 'Pelamar Baru!',
            'message' => "Ada pelamar baru ({$applicantName}) untuk pekerjaan '{$jobName}'.",
            'data' => [
                'job_name' => $jobName,
                'applicant_name' => $applicantName,
            ],
        ]);
    }
}
