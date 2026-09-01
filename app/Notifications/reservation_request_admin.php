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
            ->line('A new reservation request has been submitted and requires your review.')
            ->line('---')
            ->line('**RESERVATION SUMMARY**')
            ->line('Reservation ID: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Reservation Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Event Time: **' . $reservation->start_time . ' - ' . $reservation->end_time . '**')
            ->line('Venue Capacity: **' . number_format($hall->capacity) . ' people**')
            ->line('Pre-arrange Time: **' . $reservation->pre_arrange_time . ' hour(s)**')
            ->line('Post-arrange Time: **' . $reservation->post_arrange_time . ' hour(s)**')
            ->line('Full Time Block: **' . $actualStart . ' - ' . $actualEnd . '**');

        if ($reservation->reservation_type === 'package' && $reservation->package) {
            $mail->line('Package: **' . $reservation->package->name . '**');
        }

        $mail->line('Total Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('Advance Amount Required: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**');

        if ($reservation->discount > 0) {
            $mail->line('Hall Discount: **Rs. ' . number_format($reservation->discount, 2) . '**');
        }
        if ($reservation->deposit > 0) {
            $mail->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**');
        }

        $mail->line('---')
            ->line('**CUSTOMER DETAILS**')
            ->line('Name: **' . $reservation->customer_name . '**')
            ->line('Email: **' . $reservation->customer_email . '**')
            ->line('Telephone: **' . $reservation->customer_tel . '**')
            ->line('---')
            ->line('**VENUE DETAILS**')
            ->line('Hall: **' . $reservation->hall_name . '**')
            ->line('Address: **' . ($hall->address ?? 'N/A') . '**')
            ->line('Location: **' . ($hall->area ?? 'N/A') . ', ' . ($hall->district ?? 'N/A') . '**')
            ->line('---')
            ->line('**REQUIRED ACTION**')
            ->line('Review the reservation and **Accept** or **Reject** it. Accepting will notify the customer to proceed with payment.')
            ->line('---')
            ->action('View & Manage Reservation', route('admin.dashboard.route'))
            ->line('Please take action at your earliest convenience.')
            ->salutation("Regards,\n" . 'Prime Minister\'s Office');

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
