<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use URL;
use App\Notifications\VerifyAdminEmail;

class AdminModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "admins_table";
    protected $guard = 'admin';

    protected $fillable =
    [
        'company_name',
        'telephone_number',
        'email',
        'password',
        'bank',
        'account_name',
        'account_number',
        'email_verified_at',
        'verification_otp',
        'otp_expires_at',
    ];

    protected $hidden = 
    [
        'password',
        'remember_token',
    ];

    protected $casts = 
    [
        'email_verified_at' => 'datetime',
    ];
    

    /*public function getEmailVerificationUrl()
    {
    return URL::temporarySignedRoute
    (
        'admin.verification.verify',
        now()->addMinutes(config('auth.verification.expire', 60)),
        [
            'id' => $this->getKey(),
            'hash' => sha1($this->getEmailForVerification()),
        ]);
    }

    public function sendEmailVerificationNotification()
    {
        // Use admin-specific notification
        $this->notify(new VerifyAdminEmail);
    }*/

    // Add relaships to "HallModel" class
    public function halls()
    {
        return $this->hasMany(HallModel::class, 'admin_id');
    }

}
