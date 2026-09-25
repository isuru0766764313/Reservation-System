<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HallModel;
use App\Models\CustomerModel;
use App\Models\AdminModel;

class CustomerCancelled extends Notification
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
        $hall = HallModel::find($reservation->hall_id);
        $customer = CustomerModel::find($reservation->customer_id);
        $admin = AdminModel::find($hall->admin_id);

        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

        if ((int) $reservation->status === 5) {
            $cancellationFee = $reservation->payments->where('payment_alias', 'Cancellation')->first()?->amount ?? 0;
            $totalPaid = $reservation->payments->where('status', 2)->where('payment_alias', '!=', 'Cancellation')->sum('amount'); // only approved sum wihtout cancellation record
            $actualPaid = $reservation->payments->where('payment_alias', '!=', 'Cancellation')->sum('amount'); // total sum wihtout cancellation record
            $paybackAmount = max(0, $actualPaid - $cancellationFee); // cancellation fee should be subtracted.
        } else {
            $cancellationFee = 0;
            $totalPaid = $reservation->payments->where('status', 2)->sum('amount');
            $actualPaid = $reservation->payments->sum('amount');
            $paybackAmount = $actualPaid;
        }

        $remaining = max(0, (($reservation->charge - $reservation->discount_custom) + $reservation->deposit) - $totalPaid);
        $payment = $reservation->payments->last();
        $balanceAmount = $payment ? $payment->amount : 0;

        $mail = (new MailMessage)
            ->subject('Reservation Cancelled - ' . ($reservation->ref_code ?? $reservation->id))
            ->greeting('Dear Admin,')
            ->line('Customer has cancelled the reservation')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation Ref Code: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Hall Name: **' . $reservation->hall_name . '**')
            ->line('Customer Name: **' . $reservation->customer_name . '**')
            ->line('Customer Email: **' . $reservation->customer_email . '**')
            ->line('Customer Telephone: **' . $reservation->customer_tel . '**')
            ->line('Customer Type: **' . ucfirst($reservation->customer->type ?? 'N/A') . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**');

        if ($reservation->reservation_type === 'package' && $reservation->package) {
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

        if ($reservation->discount_custom > 0) {
            $mail
                ->line('Discount: **Rs. ' . number_format($reservation->discount_custom ?? 0, 2) . '**');
        }
        $mail
            ->line('Final Charge: **Rs. ' . number_format((($reservation->charge) - ($reservation->discount_custom ?? 0)), 2) . '**');
        if ($reservation->deposit > 0) {
            $mail
                ->line('Refundable Deposit: **Rs. ' . number_format($reservation->deposit, 2) . '**')
                ->line('Advance Payment: **Rs. ' . number_format($reservation->advanceAmount, 2) . '**')
                ->line('Deducted Cancellation Fee: **Rs. ' . number_format($cancellationFee ?? 0, 2) . '**');
        }
        $mail
            ->line('---')
            ->line('Total amount paid so far: **Rs. ' . number_format($paybackAmount, 2) . '**' . ' should be refunded as soon as possible.')
            ->line('---')
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
