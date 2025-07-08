<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rater_id',
        'rated_id', 
        'job_id',
        'rating',
        'comment',
        'type'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Validation rules
     */
    public static function rules()
    {
        return [
            'rater_id' => 'required|exists:users,id',
            'rated_id' => 'required|exists:users,id|different:rater_id',
            'job_id' => 'required|exists:pekerjaans,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'type' => 'required|in:worker_to_employer,employer_to_worker'
        ];
    }

    /**
     * Relationship: User who gave the rating
     */
    public function rater()
    {
        return $this->belongsTo(Users::class, 'rater_id');
    }

    /**
     * Relationship: User who received the rating
     */
    public function rated()
    {
        return $this->belongsTo(Users::class, 'rated_id');
    }

    /**
     * Relationship: Job this rating is for
     */
    public function job()
    {
        return $this->belongsTo(Pekerjaan::class, 'job_id');
    }

    /**
     * Scope: Get ratings for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('rated_id', $userId);
    }

    /**
     * Scope: Get ratings by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('rater_id', $userId);
    }

    /**
     * Scope: Get ratings for a specific job
     */
    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope: Get worker-to-employer ratings
     */
    public function scopeWorkerToEmployer($query)
    {
        return $query->where('type', 'worker_to_employer');
    }

    /**
     * Scope: Get employer-to-worker ratings
     */
    public function scopeEmployerToWorker($query)
    {
        return $query->where('type', 'employer_to_worker');
    }

    /**
     * Get average rating for a user
     */
    public static function getAverageRating($userId, $type = null)
    {
        $query = self::where('rated_id', $userId);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->avg('rating') ?: 0;
    }

    /**
     * Get rating count for a user
     */
    public static function getRatingCount($userId, $type = null)
    {
        $query = self::where('rated_id', $userId);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->count();
    }

    /**
     * Check if user can rate another user for a specific job
     */
    public static function canRate($raterId, $ratedId, $jobId, $type)
    {
        // Check if rating already exists
        $existingRating = self::where([
            'rater_id' => $raterId,
            'rated_id' => $ratedId,
            'job_id' => $jobId,
            'type' => $type
        ])->exists();

        if ($existingRating) {
            return false;
        }

        // Check if job is completed
        $job = Pekerjaan::find($jobId);
        if (!$job || $job->status !== 'Selesai') {
            return false;
        }

        // Check if there's a valid relationship between users for this job
        $pelamar = Pelamar::where('job_id', $jobId)
                         ->where('status', 'diterima')
                         ->first();

        if (!$pelamar) {
            return false;
        }

        // For worker_to_employer: rater must be the worker, rated must be the job creator
        if ($type === 'worker_to_employer') {
            return $pelamar->user_id == $raterId && $job->pembuat == $ratedId;
        }

        // For employer_to_worker: rater must be the job creator, rated must be the worker
        if ($type === 'employer_to_worker') {
            return $job->pembuat == $raterId && $pelamar->user_id == $ratedId;
        }

        return false;
    }
}
