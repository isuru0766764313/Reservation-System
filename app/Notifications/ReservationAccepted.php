<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;
    public $hall;
    public $customer;

    public function __construct($reservation, $hall, $customer)
    {
        $this->reservation = $reservation;
        $this->hall = $hall;
        $this->customer = $customer;
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
        $hall = $this->hall;
        $customer = $this->customer;
        $admin = $hall->admin;

        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

        $advancePaymentDeadline = $reservation->advancePaymentDate
            ? \Carbon\Carbon::parse($reservation->advancePaymentDate)->format('d M Y')
            : 'To be confirmed';

        $mail = (new MailMessage)
            ->subject('Reservation Approved! Payment Required - ' . $hall->name)
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . $customer->first_name . ' ' . $customer->last_name) . '!')
            ->line('Your reservation request has been **approved** by the admin. Please proceed with the advance payment to secure your booking.')
            ->line('---')
            ->line('**RESERVATION SUMMARY**')
            ->line('Reservation ID: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Venue: **' . $hall->name . '**')
            ->line('Venue Capacity: **' . number_format($hall->capacity) . ' people**')
            ->line('Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Event Time: **' . $reservation->start_time . ' - ' . $reservation->end_time . '**')
            ->line('Pre-arrange Setup: **' . $reservation->pre_arrange_time . ' hour(s)**')
            ->line('Post-arrange Clean-up: **' . $reservation->post_arrange_time . ' hour(s)**')
            ->line('Full Time Block: **' . $actualStart . ' - ' . $actualEnd . '**');

        if ($reservation->reservation_type === 'package' && $reservation->package) {
            $mail->line('Package: **' . $reservation->package->name . '**');
        }

        $mail->line('Total Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('Advance Amount Required: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**')
            ->line('Advance Payment Deadline: **' . $advancePaymentDeadline . '**');

        if ($reservation->discount > 0) {
            $mail->line('Hall Discount Applied: **Rs. ' . number_format($reservation->discount, 2) . '**');
        }
        if ($reservation->deposit > 0) {
            $mail->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**');
        }

        $mail->line('')
            ->line('⚠️ **Please note:** If the advance payment is not completed by the deadline above, your reservation will be automatically cancelled.')
            ->line('---')
            ->line('**VENUE ADDRESS & CONTACT**')
            ->line('Location: **' . ($hall->address ?? $hall->area ?? 'N/A') . '**')
            ->line('Admin Email: **' . $admin->email . '**')
            ->line('Admin Telephone: **' . $admin->telephone_number . '**')
            ->line('---')
            ->line('**NEXT STEP**')
            ->line('Your reservation has been **approved**. Please log in to your dashboard to make the advance payment.')
            ->line('---')
            ->action('Go to My Dashboard', route('load_customer_dashboard'))
            ->line('Thank you for choosing our service. We look forward to hosting your event!')
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
