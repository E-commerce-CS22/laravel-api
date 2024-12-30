<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'status',
        'profile',
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

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get user type (admin or customer)
     *
     * @return string
     */
    public function getUserType()
    {
        try {
            if ($this->admin()->exists()) {
                return 'admin';
            }
            if ($this->customer()->exists()) {
                return 'customer';
            }
            return 'unknown';
        } catch (\Exception $e) {
            \Log::error('Error in User@getUserType: ' . $e->getMessage());
            return 'unknown';
        }
    }
}
