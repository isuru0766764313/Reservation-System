<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmation extends Notification implements ShouldQueue
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
        $payment = $reservation->payments->last();
        $hall = $reservation->hall;
        $admin = $hall->admin;

        $totalPaid = $reservation->payments()->where('status', 2)->sum('amount');
        $remaining = max(0, (($reservation->charge - $reservation->discount_custom) + $reservation->deposit) - $totalPaid);
        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

        $paymentLabels = [
            'Preliminary' => 'Advance Payment',
            'Remainings' => 'Remaining Payment',
            'Cancellation' => 'Cancellation Fee',
        ];
        $paymentTypeLabel = $paymentLabels[$payment?->payment_alias] ?? ($payment?->payment_alias ?? 'N/A');

        $mail = (new MailMessage)
            ->subject($paymentTypeLabel . ' Submitted for Review - Reservation #' . ($reservation->ref_code ?? $reservation->id))
            ->greeting('Dear Admin,')
            ->line('A customer has submitted the **' . $paymentTypeLabel . '**. Please take appropriate action.')
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
            ->line('Advance Payment: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**' . '(Submitted on :' . ($payment?->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') : 'N/A') . ')')
            ->line('---')
            ->line('Balance Amount: **Rs. ' . number_format($remaining, 2) . '**')      
            ->line('---')
            ->line('**REQUIRED ACTION**')
            ->line('View the Advance payment slip and take necessary actions.')
            ->line('---')
            ->action('View Payment Slip', route('admin.view.slip', $reservation->id))
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
