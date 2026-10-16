<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminRegistrationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The admin whose registration was approved.
     */
    public $applicant;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($applicant)
    {
        $this->applicant = $applicant;
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
            ->subject('Admin Registration Approved')
            ->greeting('Dear ' . $this->applicant->company_name . ',')
            ->line('Good news! Your administrator account registration for the **Public Facilities Reservation System** has been approved.')
            ->line('You can now sign in with your email and password. For security, you will be asked to complete OTP verification (email & mobile) on your first sign in.')
            ->action('Sign In Now', route('admin.login.get.route'))
            ->salutation("Best regards,\nPublic Facilities Reservation System.");
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
            'applicant_id' => $this->applicant->id,
            'status' => 'approved',
        ];
    }
}
