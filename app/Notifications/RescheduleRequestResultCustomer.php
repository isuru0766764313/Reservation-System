<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RescheduleRequestResultCustomer extends Notification implements ShouldQueue
{
    use Queueable;

    public $rescheduleRequest;
    public $accepted;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($rescheduleRequest, $accepted)
    {
        $this->rescheduleRequest = $rescheduleRequest;
        $this->accepted = $accepted;
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
        $req = $this->rescheduleRequest;
        $reservation = $req->reservation;

        if ($this->accepted) {
            $mail = (new MailMessage)
                ->subject('Re-schedule Request Accepted - #' . ($reservation->ref_code ?? $reservation->id))
                ->greeting('Dear ' . $reservation->customer_name . ',')
                ->line('Your re-schedule request has been **accepted** by the admin.')
                ->line('---')
                ->line('**NEW SCHEDULE**')
                ->line('Hall Name: **' . $reservation->hall_name . '**')
                ->line('Reservation Ref Code: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
                ->line('New Date: **' . \Carbon\Carbon::parse($req->new_date)->format('l, d M Y') . '**')
                ->line('New Event Time Period: **' . date('h:i A', strtotime($req->new_start_time)) . ' - ' . date('h:i A', strtotime($req->new_end_time)) . '**')
                ->line('---')
                ->salutation("Best regards,\nPublic Facilities Reservation System.");
        } else {
            $mail = (new MailMessage)
                ->subject('Re-schedule Request Rejected - #' . ($reservation->ref_code ?? $reservation->id))
                ->greeting('Dear ' . $reservation->customer_name . ',')
                ->line('We regret to inform you that your re-schedule request has been **rejected** by the admin.')
                ->line('Your reservation remains on its original schedule.')
                ->line('---')
                ->line('**ORIGINAL SCHEDULE (STILL VALID)**')
                ->line('Hall Name: **' . $reservation->hall_name . '**')
                ->line('Reservation Ref Code: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
                ->line('Date: **' . \Carbon\Carbon::parse($req->old_date)->format('l, d M Y') . '**')
                ->line('Event Time Period: **' . date('h:i A', strtotime($req->old_start_time)) . ' - ' . date('h:i A', strtotime($req->old_end_time)) . '**');

            if (!empty($req->remarks)) {
                $mail->line('---')
                    ->line('Reason given by the admin: **' . $req->remarks . '**');
            }

            $mail->line('---')
                ->salutation("Best regards,\nPublic Facilities Reservation System.");
        }

        return $mail;
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
