<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class reservation_request_customer extends Notification implements ShouldQueue
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

        $mail = (new MailMessage)
            ->subject('Reservation Request Logged #' . ($reservation->ref_code ?? $reservation->id) . ' - ' . $reservation->hall_name)
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) . ',')
            ->line('Thank you for using the Public Facilities Reservation Portal. Your reservation request has been successfully logged and is now pending admin approval.')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Ref Code: **' . ($reservation->ref_code ?? 'N/A') . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Hall Name: **' . $reservation->hall_name . '**')
            ->line('Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d') . '**')
            ->line('Time Slot: **' . date('h:i A', strtotime($reservation->start_time)) . ' - ' . date('h:i A', strtotime($reservation->end_time)) . '**')
            ->line('---')
            ->line('Your reservation is **pending admin approval**. You will be notified once the admin reviews your request.')
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
