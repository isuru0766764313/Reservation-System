<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification implements ShouldQueue
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
        $admin = $hall->admin;
        $customer = $reservation->customer;
        $payment = $reservation->payments()->where('status', 3)->latest()->first();

        $mail = (new MailMessage)
            ->subject('Payment Rejected - Reservation #' . ($reservation->ref_code ?? $reservation->id) . ' - ' . $hall->name)
            ->greeting('Dear ' . (trim(($customer->profile_title ?? '') . ' ' . ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) ?: 'Customer') . ',')
            ->line('We regret to inform you that your submitted payment could not be accepted. Please find the details below.')
            ->line('---')
            ->line('**REJECTED PAYMENT DETAILS**')
            ->line('Rejected Amount: **Rs. ' . number_format($payment?->amount ?? 0, 2) . '**')
            ->line('Payment Reference: **' . ($payment?->payment_alias ?? 'N/A') . '**')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation ID: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Venue: **' . $hall->name . '**')
            ->line('Venue Capacity: **' . number_format($hall->capacity) . ' people**')
            ->line('Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Event Time: **' . $reservation->start_time . ' - ' . $reservation->end_time . '**')
            ->line('Total Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('---')
            ->line('**WHAT THIS MEANS**');

        if ((int) $reservation->status === 6) {
            $mail->line('Your reservation time slot has been released and is no longer secured.');
        } else {
            $mail->line('Your reservation is still active. You may submit a corrected payment slip from your dashboard.');
        }

        $mail->line('---')
            ->line('**NEXT STEP**')
            ->line('Your payment could not be accepted. Please **contact the admin** via the details below for more information.')
            ->line('---')
            ->line('**VENUE CONTACT**')
            ->line('Location: **' . ($hall->address ?? $hall->area ?? 'N/A') . '**')
            ->line('Admin Email: **' . $admin->email . '**')
            ->line('Admin Telephone: **' . $admin->telephone_number . '**')
            ->line('---')
            ->action('View My Dashboard', route('load_customer_dashboard'))
            ->line('We apologize for the inconvenience and are happy to assist you further.')
            ->salutation("Best regards,\nAdmin,\nPrime Minister's Office.");

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
