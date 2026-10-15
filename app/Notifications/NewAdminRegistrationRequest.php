<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewAdminRegistrationRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The admin (organisation type) who just submitted a registration request.
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
        $reviewUrl = route('admin.approval.show', ['applicant' => $this->applicant->id]);

        return (new MailMessage)
            ->subject('New Admin Registration Request - Approval Required')
            ->greeting('Dear Super Admin,')
            ->line('A new organisation admin has requested access to the **Public Facilities Reservation Portal**.')
            ->line(new HtmlString(
                '<div style="background-color:#f8f9fa; border-radius:8px; padding:15px; margin:15px 0;">'
                . '<p style="margin:5px 0;"><strong>Company Name:</strong> ' . e($this->applicant->company_name) . '</p>'
                . '<p style="margin:5px 0;"><strong>Email:</strong> ' . e($this->applicant->email) . '</p>'
                . '<p style="margin:5px 0;"><strong>Contact Number:</strong> ' . e($this->applicant->telephone_number) . '</p>'
                . '<p style="margin:5px 0;"><strong>Requested On:</strong> ' . $this->applicant->created_at->format('Y-m-d H:i') . '</p>'
                . '</div>'
            ))
            ->line('Review the request and approve or reject it using the button below. The applicant cannot verify or sign in until you approve.')
            ->action('Review Registration Request', $reviewUrl)
            ->line('If you did not expect this request, you can safely ignore this email.')
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
            'company_name' => $this->applicant->company_name,
            'email' => $this->applicant->email,
        ];
    }
}
