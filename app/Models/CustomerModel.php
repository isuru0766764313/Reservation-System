<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use URL;
use App\Notifications\VerifyCustomerEmail;

class CustomerModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = "customers_table";
    protected $guard = 'customer';

    protected $fillable =
        [
            'profile_title',
            'first_name',
            'last_name',
            'email',
            'telephone_number',
            'national_id',
            'password',
            'temp_password',
            'type',
            'email_verified_at',
            'mobile_verified_at',
            'email_verification_otp',
            'mobile_verification_otp',
            'otp_expires_at',
            'password_reset_expiry'
        ];

    protected $hidden =
        [
            'password',
            'remember_token',
        ];

    protected $casts =
        [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password_reset_expiry' => 'datetime'
        ];

    // define the inverse relationship to reservations:
    public function reservations()
    {
        return $this->hasMany(ReservationModel::class, 'customer_id');
    }
}
