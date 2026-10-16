<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminRegistrationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The admin whose registration was rejected.
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
            ->subject('Admin Registration Rejected')
            ->greeting('Dear ' . $this->applicant->company_name . ',')
            ->line('We are sorry to inform you that your administrator account registration for the **Public Facilities Reservation System** has been rejected.')
            ->line('If you believe this is a mistake, please contact the system administrator for more information.')
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
            'status' => 'rejected',
        ];
    }
}
