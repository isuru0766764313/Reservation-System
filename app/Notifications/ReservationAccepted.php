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
            ->subject('Reservation Accepted! Advance Payment Required - ')
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . $customer->first_name . ' ' . $customer->last_name))
            ->line('Your reservation request was **accepted** by the admin. Please pay advance payment to secure your reservation.')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation Ref Code: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Hall Name: **' . $reservation->hall_name . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**');
            if ($reservation->reservation_type === 'package' && $reservation->package)
            {
            $mail
            ->line('Package Name: **' . ucfirst($reservation->package->name) . '**');
            }
            $mail
            ->line('Reservation Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Event Time Period: **' . date('h:i A', strtotime($actualStart)) . ' - ' . date('h:i A', strtotime($actualEnd)) . '**')
            ->line('Advance Payment Due Date: **' . \Carbon\Carbon::parse($reservation->advancePaymentDate)->format('l, d M Y') . '**')
            ->line('Re-schedule Due Date: **' . \Carbon\Carbon::parse($reservation->rescheduledExpiryDate)->format('l, d M Y') . '**')
            ->line('Cancellation Due Date: **' . \Carbon\Carbon::parse($reservation->cancellationExpiryDate)->format('l, d M Y') . '**')
            ->line('Charge: **Rs. ' . number_format($reservation->charge, 2) . '**');
            if ($reservation->discount_custom > 0)
            {
            $mail
            ->line('Discount: **Rs. ' . number_format($reservation->discount_custom, 2) . '**');
            }
            $mail
            ->line('Final Charge: **Rs. ' . number_format((($reservation->charge) - ($reservation->discount_custom ?? 0)), 2) . '**');
            if ($reservation->deposit > 0)
            {
            $mail
            ->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**');
            }
            $mail
            ->line('Advance Payment: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**')
            ->line('⚠️ Please note that if the advance payment is not recieved by the "Advance Payment Due Date", your reservation will be automatically cancelled.')
            ->line('---')
            ->line('**NEXT STEP**')
            ->line('Pay Advance Amount: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**')
            ->line('---')
            ->action('Go to My Dashboard', route('load_customer_dashboard'))
            ->line('Thank you for choosing our service. We look forward to hosting your event!')
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
