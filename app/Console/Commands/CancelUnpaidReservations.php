<?php

namespace App\Console\Commands;

use App\Models\ReservationModel;
use App\Models\HallUnAvailability;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelUnpaidReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel reservations where advancePaymentDate has passed and advance has not been paid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Find reservations where:
        // - advancePaymentDate is set and is <= today
        // - advancePaid is false (not yet paid)
        // - accepted is not already false (not already rejected/cancelled)
        // - user_cancelled is false (user hasn't manually cancelled)
        $reservations = ReservationModel::whereNotNull('advancePaymentDate')
            ->where('advancePaymentDate', '<=', $today)
            ->where('advancePaid', false)
            ->where(function ($query) {
                $query->where('accepted', true)
                      ->orWhereNull('accepted');
            })
            ->where('user_cancelled', false)
            ->get();

        $count = 0;

        foreach ($reservations as $reservation) {
            try {
                // Mark the reservation as cancelled by the system
                $reservation->update([
                    'accepted' => false,
                    'reserved' => null,
                    'user_cancelled' => true,
                ]);

                // Delete the hall unavailability record to free the time slot
                HallUnAvailability::where('hall_id', $reservation->hall_id)
                    ->where('date', $reservation->reservation_date)
                    ->where('start_time', $reservation->start_time)
                    ->where('end_time', $reservation->end_time)
                    ->delete();

                Log::info('Auto-cancelled unpaid reservation ID: ' . $reservation->id . ' (Hall: ' . $reservation->hall_name . ', Date: ' . $reservation->reservation_date . ')');

                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to auto-cancel reservation ID: ' . $reservation->id . ' - ' . $e->getMessage());
            }
        }

        $this->info("Successfully auto-cancelled {$count} unpaid reservation(s).");

        return 0;
    }
}
