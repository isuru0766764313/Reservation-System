<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class reservation_request_admin extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
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
        $reservation = $this->reservation;
        $hall = $reservation->hall;

        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

        $mail = (new MailMessage)
            ->subject('New Reservation Request #' . ($reservation->ref_code ?? $reservation->id) . ' - ' . $reservation->hall_name)
            ->greeting('Dear Admin,')
            ->line('A new reservation request has been submitted.')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation Ref Code: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Hall Name: **' . $reservation->hall_name . '**')
            ->line('Customer Name: **' . $reservation->customer_name . '**')
            ->line('Customer Email: **' . $reservation->customer_email . '**')
            ->line('Customer Telephone: **' . $reservation->customer_tel . '**')
            ->line('Customer Type: **' . ucfirst($reservation->customer->type ?? 'N/A') . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**');
            if ($reservation->reservation_type === 'package' && $reservation->package)
            {
            $mail
            ->line('Package Name: **' . ucfirst($reservation->package->name) . '**');
            }
        $mail
            ->line('Reservation Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Event Time Period: **' . date('h:i A', strtotime($reservation->start_time)) . ' - ' . date('h:i A', strtotime($reservation->end_time)) . '**')
            ->line('Pre-arrange Time: **' . $reservation->pre_arrange_time . ' hour(s)**')
            ->line('Post-arrange Time: **' . $reservation->post_arrange_time . ' hour(s)**')
            ->line('Full Event Time Period: **' . date('h:i A', strtotime($actualStart)) . ' - ' . date('h:i A', strtotime($actualEnd)) . '**')
            ->line('Charge: **Rs. ' . number_format($reservation->charge, 2) . '**');

        $mail->line('---')                       
            ->line('**REQUIRED ACTION**')
            ->line('Review the reservation request.')
            ->line('---')
            ->action('Review Reservation Request', route('admin.dashboard.route'))
            ->line('Please take action at your earliest convenience.')
            ->salutation("Best regards,\nPublic Facilities Reservation System.");

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
