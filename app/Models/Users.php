<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Model implements AuthenticatableContract
{
    use HasFactory, Notifiable, Authenticatable;
    // use HasFactory, Notifiable;
    protected $table = 'users'; // pastikan ini eksplisit

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        //dewa
        'nama',
        'email',
        'alamat',
        'telpon',
        'password',
        'role',
        'preferensi_user',

        //adam
        'dompet',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pembuat()
    {
        return $this->hasMany(Pekerjaan::class, 'pembuat');
    }

    public function pelamar()
    {
        return $this->belongsToMany(Users::class, 'pelamars');
    }

    /**
     * Get all financial transactions for this user
     */
    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    /**
     * Get payments (top-ups) for this user
     */
    public function payments()
    {
        return $this->hasMany(FinancialTransaction::class)->payments();
    }

    /**
     * Get payouts (withdrawals) for this user
     */
    public function payouts()
    {
        return $this->hasMany(FinancialTransaction::class)->payouts();
    }

    /**
     * Ratings given by this user
     */
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    /**
     * Ratings received by this user
     */
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }

    /**
     * Get average rating for this user
     */
    public function getAverageRating($type = null)
    {
        return Rating::getAverageRating($this->id, $type);
    }

    /**
     * Get rating count for this user
     */
    public function getRatingCount($type = null)
    {
        return Rating::getRatingCount($this->id, $type);
    }

    /**
     * Cek user admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role == 'admin';
    }

    /**
     * Cek user mitra
     *
     * @return bool
     */
    public function isMitra(): bool
    {
        return $this->role == 'mitra';
    }

    /**
     * Cek user mitra
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role == 'user';
    }
}
