<?php

namespace App\Providers;

use Date;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * This method to share data over multiple blade files
         * I could acess this value by its key name from test blade
         * */

        View::share("date",Date::now()->format("Y-m-d"));

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) 
        {
            return (new MailMessage)
                ->subject('Public Hall Resevation System - Verify Email Address')
                ->line('Please click the button below to verify your email address.')
                ->action('Go to your account', $url);
        });
    }
}
