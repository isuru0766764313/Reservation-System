<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentAccepted extends Notification implements ShouldQueue
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

        $startTs = strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600);
        $endTs = strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600);
        $actualStart = date('H:i', max($startTs, strtotime('00:00')));
        $actualEnd = date('H:i', min($endTs, strtotime('23:59')));

        $totalPaid = $reservation->payments()->where('status', 2)->sum('amount');

        $lat = $hall->latitude;
        $lng = $hall->longitude;
        if ($lat && $lng) {
            $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";
        } else {
            $address = urlencode($hall->address . ', ' . $hall->area . ', ' . $hall->district);
            $mapUrl = "https://www.google.com/maps?q={$address}";
        }

        $mail = (new MailMessage)
            ->subject('Reservation Completed! - ' . $hall->name)
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) . ', Your Reservation is Completed!')
            ->line('Your payment has been **accepted** and your reservation is now fully settled. Here are the complete details:')
            ->line('---')
            ->line('**FINAL RESERVATION DETAILS**')
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
            ->line('Total Paid: **Rs. ' . number_format($totalPaid, 2) . '**');

        if ($reservation->discount > 0) {
            $mail->line('Hall Discount Applied: **Rs. ' . number_format($reservation->discount, 2) . '**');
        }
        if ($reservation->deposit > 0) {
            $mail->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**');
        }
        if ($reservation->cancellationExpiryDate) {
            $mail->line('Cancellation Deadline: **' . \Carbon\Carbon::parse($reservation->cancellationExpiryDate)->format('d M Y') . '**');
        }
        if ($reservation->rescheduledExpiryDate) {
            $mail->line('Reschedule Deadline: **' . \Carbon\Carbon::parse($reservation->rescheduledExpiryDate)->format('d M Y') . '**');
        }

        $mail->line('---')
            ->line('**VENUE ADDRESS & CONTACT**')
            ->line('Location: **' . ($hall->address ?? $hall->area ?? 'N/A') . '**')
            ->line('Admin Email: **' . $admin->email . '**')
            ->line('Admin Telephone: **' . $admin->telephone_number . '**')
            ->line('---')
            ->line('**IMPORTANT REMINDERS**')
            ->line('✅ Please arrive at the venue **in-time** as per your reservation schedule.')
            ->line('✅ If you need to **reschedule**, please do so before the reschedule deadline.')
            ->line('✅ If you need to **cancel**, please note that cancellation fees may apply.')
            ->line('✅ Bring a copy of this confirmation email for reference.')
            ->line('---')
            ->action('View My Dashboard', route('load_customer_dashboard'))
            ->line('Find the venue on Google Maps: ' . $mapUrl)
            ->line('Thank you for choosing the Prime Minister\'s Office facilities. We look forward to hosting your event!')
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
