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
        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

        /*$startTs = strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600);
        $endTs = strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600);
        $actualStart = date('H:i', max($startTs, strtotime('00:00')));
        $actualEnd = date('H:i', min($endTs, strtotime('23:59')));*/

        $totalPaid = $reservation->payments()->where('status', 2)->sum('amount');
        $remaining = max(0, (($reservation->charge - $reservation->discount_custom) + $reservation->deposit) - $totalPaid);
        $payment = $reservation->payments->last();
        $balanceAmount = $payment ? $payment->amount : 0;

        $lat = $hall->latitude;
        $lng = $hall->longitude;
        if ($lat && $lng) {
            $mapUrl = "https://www.google.com/maps?q={$lat},{$lng}";
        } else {
            $address = urlencode($hall->address . ', ' . $hall->area . ', ' . $hall->district);
            $mapUrl = "https://www.google.com/maps?q={$address}";
        }

        $mail = (new MailMessage)
            ->subject('Reservation Completed - Reservation #' . ($reservation->ref_code ?? $reservation->id))
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . $customer->first_name . ' ' . $customer->last_name))
            ->line('Your **Balance Payment Rs. ' . number_format($balanceAmount, 2) . '** has been approved by the admin. Your reservation is now **booked** and the event time period is secured.')
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
            ->line('---')            
            ->action('View My Dashboard', route('load_customer_dashboard'))
            ->line('Find the venue on Google Maps: ' . $mapUrl)
            ->line('---')            
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
