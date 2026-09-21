<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HallModel;
use App\Models\CustomerModel;
use App\Models\AdminModel;

class ReservationRejectedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;
    public $hall;
    public $customer;

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
        //$admin = $hall->admin;
        $admin = AdminModel::find($hall->admin_id);

        return (new MailMessage)
            ->subject('Reservation Rejected - ' . $hall->name)
            ->greeting('Dear ' . trim(($reservation->customer->profile_title ?? '') . ' ' . $reservation->customer->first_name . ' ' . $reservation->customer->last_name) . ',')
            ->line('We sincerely regret to inform you that your reservation request has been **rejected** due to unforeseen and unavoidable circumstances.')
            ->line('---')
            ->line('**REJECTED RESERVATION DETAILS**')
            ->line('Reservation ID: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Reservation Type: **' . ucfirst($reservation->reservation_type) . '**')
            ->line('Venue: **' . $hall->name . '**')
            ->line('Venue Capacity: **' . number_format($hall->capacity) . ' people**')
            ->line('Requested Date: **' . \Carbon\Carbon::parse($reservation->reservation_date)->format('l, d M Y') . '**')
            ->line('Requested Time: **' . $reservation->start_time . ' - ' . $reservation->end_time . '**')
            ->line('Total Charge: **Rs. ' . number_format($reservation->charge, 2) . '**')
            ->line('---')
            ->line('**VENUE LOCATION**')
            ->line('Address: **' . ($hall->address ?? 'N/A') . '**')
            ->line('Location: **' . ($hall->area ?? 'N/A') . ', ' . ($hall->district ?? 'N/A') . '**')
            ->line('---')
            ->line('**NEXT STEP**')
            ->line('Your reservation has been **rejected**. You may browse other venues and make a new reservation.')
            ->line('---')
            ->line('**VENUE CONTACT**')
            ->line('Admin Email: **' . $admin->email . '**')
            ->line('Admin Telephone: **' . $admin->telephone_number . '**')
            ->line('---')            
            ->action('Browse Other Venues', route('load_venues_page'))
            ->line('We apologize for any inconvenience this may cause and appreciate your understanding.')
            ->line('---')
            ->salutation("Best regards,\nAdmin,\nPrime Minister's Office.");
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
