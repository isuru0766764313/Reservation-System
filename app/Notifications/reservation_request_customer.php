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
        $actualStart = date('H:i', strtotime($reservation->start_time) - ($reservation->pre_arrange_time * 3600));
        $actualEnd = date('H:i', strtotime($reservation->end_time) + ($reservation->post_arrange_time * 3600));

        $mail = (new MailMessage)
            ->subject('Reservation Request Logged #' . ($reservation->ref_code ?? $reservation->id) . ' - ' . $reservation->hall_name)
            ->greeting('Dear ' . trim(($customer->profile_title ?? '') . ' ' . ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) . ',')
            ->line('Thank you for using the Public Facilities Reservation System. Your reservation request was successfully logged.')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation Ref Code: **' . ($reservation->ref_code ?? 'N/A') . '**')
            ->line('Hall Name: **' . $reservation->hall_name . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Reservation Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d') . '**')
            ->line('Event Time Period: **' . date('h:i A', strtotime($actualStart)) . ' - ' . date('h:i A', strtotime($actualEnd)) . '**')
            ->line('Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('---')
            ->line('Your reservation request is under review.')
            ->salutation("Best regards,\nAdmin,\nPublic Facilities Reservation System.");

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
