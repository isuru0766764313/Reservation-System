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

        $paymentLabels = [
            'Preliminary' => 'Advance Payment',
            'Remainings' => 'Remaining Payment',
            'Cancellation' => 'Cancellation Fee',
        ];
        $paymentTypeLabel = $paymentLabels[$payment?->payment_alias] ?? ($payment?->payment_alias ?? 'N/A');

        $mail = (new MailMessage)
            ->subject($paymentTypeLabel . ' Submitted for Review - Reservation #' . ($reservation->ref_code ?? $reservation->id))
            ->greeting('Payment Slip Verification Required')
            ->line('A customer has submitted a **' . $paymentTypeLabel . '**. Please verify the payment slip and take appropriate action.')
            ->line('---')
            ->line('**PAYMENT DETAILS**')
            ->line('Payment Type: **' . $paymentTypeLabel . '**')
            ->line('Payment Amount: **Rs. ' . number_format($payment?->amount ?? 0, 2) . '**')
            ->line('Total Paid So Far: **Rs. ' . number_format($totalPaid + ($payment?->amount ?? 0), 2) . '**');

        if ($payment?->payment_alias !== 'Cancellation') {
            $mail->line('Remaining Balance: **Rs. ' . number_format(max(0, $remaining - ($payment?->amount ?? 0)), 2) . '**');
        }

        $mail->line('Date Submitted: **' . ($payment?->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') : 'N/A') . '**')
            ->line('---')
            ->line('**CUSTOMER DETAILS**')
            ->line('Name: **' . $reservation->customer_name . '**')
            ->line('Email: **' . $reservation->customer_email . '**')
            ->line('Telephone: **' . $reservation->customer_tel . '**')
            ->line('Customer Type: **' . ucfirst($reservation->customer->type ?? 'N/A') . '**')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation ID: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Venue: **' . $reservation->hall_name . '**')
            ->line('Venue Capacity: **' . number_format($hall->capacity) . ' people**')
            ->line('Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Time: **' . $reservation->start_time . ' - ' . $reservation->end_time . '**')
            ->line('Pre-arrange Time: **' . $reservation->pre_arrange_time . ' hour(s)**')
            ->line('Post-arrange Time: **' . $reservation->post_arrange_time . ' hour(s)**')
            ->line('Total Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('Advance Amount: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**')
            ->line('Advance Paid: **' . ($reservation->advancePaid ? 'Yes' : 'No') . '**');

        if ($reservation->reservation_type === 'package' && $reservation->package) {
            $mail->line('Package: **' . $reservation->package->name . '**');
        }

        $mail->line('---')
            ->line('**YOUR ACTION REQUIRED**')
            ->line('View the payment slip and verify the details.');

        if ($payment?->payment_alias === 'Cancellation') {
            $mail->line('Then **Accept** to confirm cancellation or **Reject** to restore the reservation.');
        } elseif ($payment?->payment_alias === 'Preliminary') {
            $mail->line('Then **Accept**, **Accept Advance Only**, or **Reject** the payment.');
        } elseif ($payment?->payment_alias === 'Remainings') {
            $mail->line('Then **Accept** to finalize the booking or **Reject** the payment.');
        }

        $mail->line('---')
            ->action('View Payment Slip', route('admin.view.slip', $reservation->id))
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
