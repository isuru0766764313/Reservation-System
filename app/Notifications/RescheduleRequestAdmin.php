<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class RescheduleRequestAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $rescheduleRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($rescheduleRequest)
    {
        $this->rescheduleRequest = $rescheduleRequest;
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
        $req = $this->rescheduleRequest;
        $reservation = $req->reservation;

        // Actual (full) time periods including arrange buffers, for clarity
        $oldActualStart = date('H:i', strtotime($req->old_start_time) - ($req->old_pre_arrange_time * 3600));
        $oldActualEnd = date('H:i', strtotime($req->old_end_time) + ($req->old_post_arrange_time * 3600));
        $newActualStart = date('H:i', strtotime($req->new_start_time) - ($req->new_pre_arrange_time * 3600));
        $newActualEnd = date('H:i', strtotime($req->new_end_time) + ($req->new_post_arrange_time * 3600));

        $mail = (new MailMessage)
            ->subject('Re-schedule Request - #' . ($reservation->ref_code ?? $reservation->id) . ' - ' . $reservation->hall_name)
            ->greeting('Dear Admin,')
            ->line('A customer has requested to re-schedule their reservation. Please review and accept or reject it from your dashboard.')
            ->line('---')
            ->line('**RESERVATION DETAILS**')
            ->line('Reservation Ref Code: **#' . ($reservation->ref_code ?? $reservation->id) . '**')
            ->line('Hall Name: **' . $reservation->hall_name . '**')
            ->line('Customer Name: **' . $reservation->customer_name . '**')
            ->line('Customer Email: **' . $reservation->customer_email . '**')
            ->line('Customer Telephone: **' . $reservation->customer_tel . '**');

        $mail
            ->line('---')
            ->line('**CURRENT SCHEDULE**')
            ->line('Date: **' . \Carbon\Carbon::parse($req->old_date)->format('l, d M Y') . '**')
            ->line('Event Time Period: **' . date('h:i A', strtotime($req->old_start_time)) . ' - ' . date('h:i A', strtotime($req->old_end_time)) . '**')
            ->line('Pre-arrange Time: **' . $req->old_pre_arrange_time . ' hour(s)**')
            ->line('Post-arrange Time: **' . $req->old_post_arrange_time . ' hour(s)**')
            ->line('Full Event Time Period: **' . date('h:i A', strtotime($oldActualStart)) . ' - ' . date('h:i A', strtotime($oldActualEnd)) . '**');

        $mail
            ->line('---')
            ->line('**REQUESTED NEW SCHEDULE**')
            ->line('Date: **' . \Carbon\Carbon::parse($req->new_date)->format('l, d M Y') . '**')
            ->line('Event Time Period: **' . date('h:i A', strtotime($req->new_start_time)) . ' - ' . date('h:i A', strtotime($req->new_end_time)) . '**')
            ->line('Pre-arrange Time: **' . $req->new_pre_arrange_time . ' hour(s)**')
            ->line('Post-arrange Time: **' . $req->new_post_arrange_time . ' hour(s)**')
            ->line('Full Event Time Period: **' . date('h:i A', strtotime($newActualStart)) . ' - ' . date('h:i A', strtotime($newActualEnd)) . '**')
            ->line('---');

        // Action buttons — only available when the admin dashboard route exists (i.e. we are
        // not running unit tests where routes are not loaded). This keeps the notification
        // safe in every environment while giving admins one-click actions in real emails.
        if (Route::has('admin.dashboard.route')) {
            $dashboardUrl = route('admin.dashboard.route') . '#reservationModal-' . $reservation->id;
            $mail->line('You can accept or reject this request directly from your dashboard by opening the reservation detail modal (Ref. Code #' . ($reservation->ref_code ?? $reservation->id) . ').')
                ->action('Open Dashboard & Review Request', $dashboardUrl);
        }

        $mail->salutation("Best regards,\nPublic Facilities Reservation System.");

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
