<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdvancePaymentAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $reservation = $this->reservation;
        $hall = $reservation->hall;
        $admin = $hall->admin;

        $remaining = max(0, (($reservation->charge - ($reservation->discount_custom ?? 0)) + $reservation->deposit) - $reservation->advanceAmount);

        $lat = $hall->latitude;
        $lng = $hall->longitude;
        if ($lat && $lng) {
            $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";
        } else {
            $address = urlencode($hall->address . ', ' . $hall->area . ', ' . $hall->district);
            $mapUrl = "https://www.google.com/maps?q={$address}";
        }

        $mail = (new MailMessage)
            ->subject('Advance Payment Accepted - Reservation #' . ($reservation->ref_code ?? $reservation->id))
            ->greeting('Dear ' . trim(($reservation->customer->profile_title ?? '') . ' ' . ($reservation->customer->first_name ?? '') . ' ' . ($reservation->customer->last_name ?? '')) . ',')
            ->line('Your **advance payment of Rs. ' . number_format($reservation->advanceAmount, 2) . '** has been accepted by the admin.')
            ->line('Your reservation is now **confirmed** and the time slot is secured. Please proceed with the remaining payment to finalize your booking.')
            ->line('---')
            ->line('**RESERVATION SUMMARY**')
            ->line('Reservation ID: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Venue: **' . $hall->name . '**')
            ->line('Venue Capacity: **' . number_format($hall->capacity) . ' people**')
            ->line('Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Event Time: **' . $reservation->start_time . ' - ' . $reservation->end_time . '**')
            ->line('---')
            ->line('**PAYMENT BREAKDOWN**')
            ->line('Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('Discount: **Rs. ' . number_format($reservation->discount_custom, 2) . '**')
            ->line('Final Charge: **Rs. ' . number_format(($reservation->charge - $reservation->discount_custom), 2) . '**')
            ->line('Advance Paid: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**')
            ->line('Remaining Balance: **Rs. ' . number_format($remaining, 2) . '**');

        if ($reservation->deposit > 0) {
            $mail->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**');
        }

        $mail->line('---')
            ->line('**NEXT STEP**')
            ->line('Your advance payment has been **accepted**. Please log in to your dashboard to submit the **remaining payment of Rs. ' . number_format($remaining, 2) . '**.')
            ->line('---')
            ->line('**VENUE ADDRESS & CONTACT**')
            ->line('Location: **' . ($hall->address ?? $hall->area ?? 'N/A') . '**')
            ->line('Admin Email: **' . $admin->email . '**')
            ->line('Admin Telephone: **' . $admin->telephone_number . '**')
            ->line('---')
            ->action('Go to My Dashboard', route('load_customer_dashboard'))
            ->line('Find the venue on Google Maps: ' . $mapUrl)
            ->line('Thank you for choosing the Prime Minister\'s Office facilities.')
            ->salutation("Best regards,\nAdmin,\nPrime Minister's Office.");

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
