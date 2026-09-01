<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendAdminOTP extends Notification implements ShouldQueue
{
    use Queueable;

    public $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Email Verification - Public Facilities Reservation Portal (Admin)')
            ->greeting('Dear Admin,')
            ->line('Thank you for registering as an admin on the **Public Facilities Reservation Portal** (Prime Minister\'s Office).')
            ->line('Use the verification code below to activate your admin account:')
            ->line(new HtmlString(
                '<div style="text-align:center; margin:20px 0; padding:15px; background-color:#f8f9fa; border-radius:8px; border:2px dashed #007bff;">'
                . '<h1 style="font-size:42px; letter-spacing:8px; color:#007bff; margin:0;">' . $this->otp . '</h1>'
                . '</div>'
            ))
            ->line('⏰ **This code will expire in 10 minutes.**')
            ->line('If you did not create an account, please ignore this email.')
            ->salutation("Regards,\n" . 'Prime Minister\'s Office');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
