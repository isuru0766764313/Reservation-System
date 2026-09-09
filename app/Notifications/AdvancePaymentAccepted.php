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
        $customer = $reservation->customer;
        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

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
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . $customer->first_name . ' ' . $customer->last_name))
            ->line('Your **Advance Payment Rs. ' . number_format($reservation->advanceAmount, 2) . '** has been approved by the admin. Your reservation is now **confirmed** and the event time period is secured.')
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
            ->line('Re-schedule Due Date: **' . \Carbon\Carbon::parse($reservation->rescheduledExpiryDate)->format('l, d M Y') . '**')
            ->line('Cancellation Due Date: **' . \Carbon\Carbon::parse($reservation->cancellationExpiryDate)->format('l, d M Y') . '**')
            ->line('Charge: **Rs. ' . number_format($reservation->charge, 2) . '**');
            if ($reservation->discount_custom > 0)
            {
            $mail
            ->line('Discount: **Rs. ' . number_format($reservation->discount_custom ?? 0, 2) . '**');
            }
            $mail
            ->line('Final Charge: **Rs. ' . number_format((($reservation->charge) - ($reservation->discount_custom ?? 0)), 2) . '**');
            if ($reservation->deposit > 0)
            {
            $mail->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**');
            }
            $mail
            ->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**')
            ->line('Advance Payment: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**' . '-' . 'Approved')
            ->line('Balance Amount: **Rs. ' . number_format($remaining, 2) . '**')
            ->line('---')
            ->line('**NEXT STEP**')
            ->line('Please submit the **balance payment of Rs. ' . number_format($remaining, 2) . '**.')
            ->line('---')
            ->line('Find the venue on Google Maps: ' . $mapUrl)
            ->line('---')
            ->action('Upload Balance payment Slip', route('load_customer_dashboard'))            
            ->line('Thank you for choosing the Prime Minister\'s Office facilities.')
            ->salutation("Best regards,\nPublic Facilities Reservation System.");

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
