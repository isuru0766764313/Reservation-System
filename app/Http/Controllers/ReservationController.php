<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use App\Models\HallUnAvailability;
use App\Models\HallModel;
use App\Models\Reservation;
use App\Models\Payments;
use App\Models\ReservationModel;
use App\Models\PackagesModel;
use App\Notifications\reservation_request_admin;
use App\Notifications\reservation_request_customer;
use App\Notifications\PaymentConfirmation;
use App\Notifications\PaymentAccepted;
use App\Notifications\PaymentRejected;
use App\Notifications\AdvancePaymentAccepted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Services\ESMSWS;//sms api
use App\Http\Controllers\SmsServiceController;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Ramsey\Uuid\Type\Decimal;

class ReservationController extends Controller
{
    public function reservation_request_sent_regular(Request $request, HallModel $hall)
    {
        $request->validate([
            'selected_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'fixed_facilities' => 'sometimes|array',
            'fixed_facilities.*' => 'exists:fixed_price_facilities_table,id',
            'unit_facilities' => 'sometimes|array',
            'unit_facilities.*' => 'exists:unit_price_facilities_table,id',
            'pre_arrange_time' => 'required|integer|min:0|max:' . ($hall->max_pre_arrange_hours ?? 5),
            'post_arrange_time' => 'required|integer|min:0|max:' . ($hall->max_post_arrange_hours ?? 5),
            'actual_start_time' => 'required',
            'actual_end_time' => 'required',
            'agree_terms' => 'required|accepted',
            'clearence_form' => 'nullable|mimes:pdf|max:10240', // Note: max is in kilobytes (10MB = 10240KB)
        ]);

        $customer = Auth::guard('customer')->user();

        // Calculate duration
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $durationHours = $end->diffInHours($start);

        // Calculate charges
        $baseCharge = $durationHours * $hall->price;
        $facilitiesCharge = 0;

        // Fixed price facilities
        if ($request->has('fixed_facilities')) {
            $selectedFixedFacilities = $hall->fixedfacilities()->whereIn('id', $request->fixed_facilities)->get();
            foreach ($selectedFixedFacilities as $facility) {
                $facilitiesCharge += $facility->charge;
            }
        }

        // Unit price facilities
        if ($request->has('unit_facilities')) {
            $selectedUnitFacilities = $hall->unitfacilities()->whereIn('id', $request->unit_facilities)->get();
            foreach ($selectedUnitFacilities as $facility) {
                $facilitiesCharge += $facility->charge * $durationHours;
            }
        }

        if ($hall->discount > 0)//if there is a hall discount to be applied
        {
            $totalCharge = ($baseCharge + $facilitiesCharge - $hall->discount);
            /* if there is a deposit to be applied
            if($hall->deposit > 0)
            {
                $totalCharge = ($baseCharge + $facilitiesCharge - $hall->discount) + $hall->deposit;
            }
            else
            {
                $totalCharge = $baseCharge + $facilitiesCharge - ($hall->discount);
            }*/

        } else {
            $totalCharge = ($baseCharge + $facilitiesCharge);
            /* if there is a deposit to be applied
            if($hall->deposit > 0)
            {
                $totalCharge = ($baseCharge + $facilitiesCharge) + $hall->deposit;                
            }else
            {
                $totalCharge = $baseCharge + $facilitiesCharge;
            }*/

        }

        // Create reservation
        $reservation = ReservationModel::create([
            'ref_code' => $this->generateRefCode(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'customer_email' => $customer->email,
            'customer_tel' => $customer->telephone_number,

            'hall_id' => $hall->id,
            'hall_name' => $hall->name,

            'reservation_date' => $request->selected_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'pre_arrange_time' => $request->pre_arrange_time,
            'post_arrange_time' => $request->post_arrange_time,
            'agree_terms' => (bool) $request->agree_terms,
            'logged' => true,
            'user_cancelled' => false,

            'charge' => $totalCharge,
            'advanceAmount' => 25000, // make advance amount to be fixed
            'discount' => $hall->discount ?? null,
            'discount_custom' => $request->discount_custom ?? null,
            'deposit' => $hall->deposit,

            'accepted' => null,
            'reserved' => null,
            'payment_status' => false,

            // Reservation type
            'reservation_type' => 'regular',
            'package_id' => null,
            'clearence_form' => $request->hasFile('clearence_form') ? $request->file('clearence_form')->store('halls/clearence', 'public') : null,
        ]);

        // Attach facilities to reservation
        if ($request->has('fixed_facilities')) {
            $reservation->fixedFacilities()->attach($request->fixed_facilities);
        }

        if ($request->has('unit_facilities')) {
            $reservation->unitFacilities()->attach($request->unit_facilities);
        }

        // Save to hall_availabilities
        HallUnAvailability::create([
            'hall_id' => $hall->id,
            'date' => $request->selected_date,
            //'start_time' => $request->start_time,
            //'end_time' => $request->end_time,
            'start_time' => $request->actual_start_time,
            'end_time' => $request->actual_end_time,
        ]);

        // Notifications
        $admin = AdminModel::where('id', $hall->admin_id)->first();
        if ($admin) {
            $admin->notify(new reservation_request_admin($reservation));
            $customer->notify(new reservation_request_customer($reservation));
        }

        // Send SMS
        $message = 'Your REGULAR reservation has been logged for ' . $hall->name . ' on ' . $request->selected_date . ' from ' . $request->start_time . ' to ' . $request->end_time . '.' . PHP_EOL . 'Your reservation ID is : ' . $reservation->id;
        $recipients = ($customer->telephone_number . ',' . $admin->telephone_number);
        $smsService = new SmsServiceController($message, $recipients);
        $smsService->sendSms();

        return redirect()->back()->with('success', 'Regular reservation request sent successfully!');
    }



    public function reservation_request_sent_package(Request $request, HallModel $hall)
    {
        $request->validate([
            'selected_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'package_id' => 'required|exists:packages_table,id',
            'total_charge' => 'required|numeric|min:0',
            'pre_arrange_time' => 'required|integer|min:0|max:' . ($hall->max_pre_arrange_hours ?? 5),
            'post_arrange_time' => 'required|integer|min:0|max:' . ($hall->max_post_arrange_hours ?? 5),
            'actual_start_time' => 'required',
            'actual_end_time' => 'required',
            'agree_terms' => 'required|accepted',
            'clearence_form' => 'required|mimes:pdf|max:2048',
        ]);

        $customer = Auth::guard('customer')->user();

        // Get the selected package
        $package = PackagesModel::where('id', $request->package_id)
            ->where('hall_id', $hall->id)
            ->firstOrFail();

        // Use the dynamically calculated total charge from the frontend
        $totalCharge = $request->total_charge;

        // Create reservation
        $reservation = ReservationModel::create([
            'ref_code' => $this->generateRefCode(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->first_name . ' ' . $customer->last_name,
            'customer_email' => $customer->email,
            'customer_tel' => $customer->telephone_number,

            'hall_id' => $hall->id,
            'hall_name' => $hall->name,

            'reservation_date' => $request->selected_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'pre_arrange_time' => $request->pre_arrange_time,
            'post_arrange_time' => $request->post_arrange_time,
            'agree_terms' => (bool) $request->agree_terms,
            'logged' => true,
            'user_cancelled' => false,

            'charge' => $totalCharge,
            'advanceAmount' => 25000, // make advance amount to be fixed
            'discount' => $hall->discount,
            'deposit' => $hall->deposit,

            'accepted' => null,
            'reserved' => null,
            'payment_status' => false,

            // Reservation type
            'reservation_type' => 'package',
            'package_id' => $package->id,
            'clearence_form' => $request->hasFile('clearence_form') ? $request->file('clearence_form')->store('halls/clearence', 'public') : null,
        ]);

        // For package bookings, attach all facilities included in the package
        $fixedFacilityIds = $package->getFixedFacilitiesAttribute();
        $unitFacilityIds = $package->getUnitFacilitiesAttribute();

        if (!empty($fixedFacilityIds) && is_array($fixedFacilityIds)) {
            $reservation->fixedFacilities()->attach($fixedFacilityIds);
        }

        if (!empty($unitFacilityIds) && is_array($unitFacilityIds)) {
            $reservation->unitFacilities()->attach($unitFacilityIds);
        }

        // Also attach any customer-selected extra facilities for the package
        if ($request->has('package_fixed_facilities')) {
            $reservation->fixedFacilities()->attach($request->package_fixed_facilities);
        }
        if ($request->has('package_unit_facilities')) {
            $reservation->unitFacilities()->attach($request->package_unit_facilities);
        }

        // Save to hall_availabilities
        HallUnAvailability::create([
            'hall_id' => $hall->id,
            'date' => $request->selected_date,
            //'start_time' => $request->start_time,
            //'end_time' => $request->end_time,
            'start_time' => $request->actual_start_time,
            'end_time' => $request->actual_end_time,
        ]);

        // Notifications
        $admin = AdminModel::where('id', $hall->admin_id)->first();
        if ($admin) {
            $admin->notify(new reservation_request_admin($reservation));
            $customer->notify(new reservation_request_customer($reservation));
        }

        // Send SMS
        $message = 'Your PACKAGE reservation has been logged for ' . $hall->name . ' on ' . $request->selected_date . ' from ' . $request->start_time . ' to ' . $request->end_time . '.' . PHP_EOL . 'Package: ' . $package->name . PHP_EOL . 'Your reservation ID is : ' . $reservation->id;
        $recipients = ($customer->telephone_number . ',' . $admin->telephone_number);
        $smsService = new SmsServiceController($message, $recipients);
        $smsService->sendSms();

        return redirect()->back()->with('success', 'Package reservation request sent successfully!');
    }

    private function generateRefCode(): string
    {
        $last = ReservationModel::orderBy('id', 'desc')->value('ref_code');

        if (!$last) {
            return 'GRS-A000000';
        }

        $letter = substr($last, 4, 1);
        $number = (int) substr($last, 5);

        if ($number < 999999) {
            $number++;
        } else {
            $letter = chr(ord($letter) + 1);
            $number = 0;
        }

        return 'GRS-' . $letter . str_pad($number, 6, '0', STR_PAD_LEFT);
    }


    public function open_payment_page($encryptedReservation)
    {
        try {
            // Decrypt the reservation ID
            $reservationId = Crypt::decryptString($encryptedReservation);
        } catch (DecryptException $e) {
            abort(404, 'Invalid reservation ID');
        }
        // Retrieve the reservation
        $reservation = ReservationModel::findOrFail($reservationId);
        // Authorization check - ensure customer owns this reservation
        if (auth('customer')->id() !== $reservation->customer->id) {
            abort(403, 'Unauthorized access to this reservation');
        } else {
            return view('payment', compact('reservation'));
        }
    }

    public function make_payment(Request $request, ReservationModel $reservation)
    {
        // --- 1. CLEAN AND VALIDATE INPUT ---

        // The receipt validation can be done up front since both branches require it.
        $request->validate([
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // You might want to add validation for amount, e.g., 'amount' => 'required|numeric'
        ]);

        // **THE ESSENTIAL FIX:** Clean the amount string by removing thousands separators
        // and casting it to a float. This prevents the SQL truncation error.
        $cleaned_amount_string = str_replace(',', '', $request->amount);
        $final_amount = (float) $cleaned_amount_string;

        $path = $request->file('receipt')->store('receipts', 'public');

        // --- 2. CALCULATE AND CHECK ADVANCE STATUS ---

        // Get total paid BEFORE this payment
        $totalPaidBefore = Payments::where('reservation_id', $reservation->id)->sum('amount');

        // Calculate total AFTER this payment
        $totalPaidAfter = $totalPaidBefore + $final_amount;

        $advanceAmount = $reservation->advanceAmount;

        // Check if advance is paid AFTER this payment
        $advancePaid = $totalPaidAfter >= $advanceAmount;

        // --- 3. DETERMINE PAYMENT ALIAS FROM ACTUAL STATE ---
        // Derive payment_alias from the database state instead of trusting the form input,
        // because the form contains multiple conflicting hidden fields with the same name.
        // PHP only keeps the last value ('Cancellation'), making the form input unreliable.
        // Use the existence of a Preliminary payment record rather than comparing amounts,
        // since advanceAmount in the DB may have been modified by the admin.

        $hasPreliminary = Payments::where('reservation_id', $reservation->id)
            ->where('payment_alias', 'Preliminary')
            ->exists();

        // If the form explicitly submitted a Cancellation payment, trust it
        if ($request->payment_alias === 'Cancellation') {
            $payment_alias = 'Cancellation';
        } elseif ($reservation->status === 5) {
            // Reservation already cancelled — this is a cancellation fee payment
            $payment_alias = 'Cancellation';
        } elseif ($totalPaidBefore >= $reservation->charge && $hasPreliminary) {
            // Already fully paid and a Preliminary exists — any further payment is a cancellation fee
            $payment_alias = 'Cancellation';
        } elseif (!$hasPreliminary) {
            $payment_alias = 'Preliminary';
        } else {
            $payment_alias = 'Remainings';
        }

        // --- 4. CREATE PAYMENT RECORD ---

        $payments = Payments::create([
            'reservation_id' => $reservation->id,
            'payment_alias' => $payment_alias,
            'amount' => $final_amount,
            'receipt_path' => $path,
            'status' => 1,
        ]);

        // --- 5. POST-PAYMENT LOGIC ---

        // If this is a cancellation payment, reservation is already cancelled — just notify admin
        if ($payment_alias === 'Cancellation') {
            $admin = $reservation->hall->admin;
            $admin->notify(new PaymentConfirmation($reservation));
            return redirect()->route('load_customer_dashboard')->with('success', 'Cancellation payment slip submitted. Awaiting admin approval.');
        }

        // Use the calculated total after payment
        $totalPaid = $totalPaidAfter;
        $remaining = ($reservation->charge) - $totalPaid;
        $admin = $reservation->hall->admin;
        $admin->notify(new PaymentConfirmation($reservation));

        if ($advancePaid) {
            $reservation->update(['advancePaid' => true]);
            return redirect()->route('load_customer_dashboard')->with('success', 'Payment submitted successfully!');
        } else {
            $reservation->update(['advancePaid' => false]);
            return redirect()->route('load_customer_dashboard')->with('success', 'Payment submitted successfully!')->with('remaining', $remaining)->with('total_paid', $totalPaid);
        }
    }

    public function acceptAdvancePayment(ReservationModel $reservation)
    {
        // Verify admin owns this hall
        if (auth('admin')->id() !== $reservation->hall->admin_id) {
            abort(403, 'Unauthorized action');
        } else {
            if ($reservation->accepted && $reservation->advance_accepted === null) {
                $reservation->update([
                    'advance_accepted' => true,
                    'status' => 3,
                ]);
                // Approve the Preliminary payment
                Payments::where('reservation_id', $reservation->id)
                    ->where('payment_alias', 'Preliminary')
                    ->where('status', 1)
                    ->update(['status' => 2]);
                // Notify customer
                $reservation->customer->notify(new AdvancePaymentAccepted($reservation));
                return redirect()->route('admin.dashboard.route')->with('success', 'Advance payment accepted! Customer can now submit remaining payment.');
            } elseif ($reservation->advance_accepted !== null) {
                return back()->with('error', 'Advance payment already processed!');
            } else {
                return back()->with('error', 'Reservation not yet accepted!');
            }
        }
    }

    public function acceptPayment(ReservationModel $reservation)
    {
        // Verify admin owns this hall
        if (auth('admin')->id() !== $reservation->hall->admin_id) {
            abort(403, 'Unauthorized action');
        } else {
            if ($reservation->accepted && ($reservation->reserved == null || Payments::where('reservation_id', $reservation->id)->where('payment_alias', 'Cancellation')->where('status', 1)->exists())) {
                // Check if this is a cancellation payment approval
                $cancellationPayment = Payments::where('reservation_id', $reservation->id)
                    ->where('payment_alias', 'Cancellation')
                    ->where('status', 1)
                    ->first();

                if ($cancellationPayment) {
                    $cancellationPayment->update(['status' => 2]);

                    // Update reservation: mark as cancelled
                    $start = Carbon::parse($reservation->start_time);
                    $end = Carbon::parse($reservation->end_time);
                    $actualStartTime = $start->copy()->subHours($reservation->pre_arrange_time)->format('H:i:s');
                    $actualEndTime = $end->copy()->addHours($reservation->post_arrange_time)->format('H:i:s');

                    $newCharge = $reservation->charge + $reservation->hall->cancellation_fee;

                    $reservation->update([
                        'user_cancelled' => true,
                        'status' => 5,
                        'charge' => $newCharge,
                    ]);

                    // Delete the unavailability record
                    HallUnAvailability::where('hall_id', $reservation->hall_id)
                        ->where('date', $reservation->reservation_date)
                        ->where('start_time', $actualStartTime)
                        ->where('end_time', $actualEndTime)
                        ->delete();

                    return redirect()->route('admin.dashboard.route')->with('success', 'Cancellation payment approved.');
                }

                $reservation->update([
                    'accepted' => true,
                    'reserved' => true,
                    'status' => 4,
                ]);
                // Approve all pending payments for this reservation
                Payments::where('reservation_id', $reservation->id)
                    ->where('payment_alias', 'Remainings')
                    ->where('status', 1)
                    ->update(['status' => 2]);
                // Send confirmation to customer
                $reservation->customer->notify(new PaymentAccepted($reservation));
                // send sms notifying reservation payment was accepted.
                $message = 'Your reservation has been Scheduled for ' . $reservation->hall_name . ' on ' . $reservation->reservation_date . ' from ' . $reservation->start_time . ' to ' . $reservation->end_time . '.' . PHP_EOL . 'Your reservation ID is : ' . $reservation->id;
                $recipients = ($reservation->customer_tel);
                $smsService = new SmsServiceController($message, $recipients);
                $smsService->sendSms();
                return redirect()->route('admin.dashboard.route')->with('success', 'Payment accepted successfully!');

                //return back()->with('error', 'Reservation already rejected!');
            } elseif ($reservation->accepted && $reservation->reserved) {
                return back()->with('error', 'Already Reserved!');
            } else {
                return back()->with('error', 'Already Rejected!');
            }

        }
    }

    public function rejectPayment(ReservationModel $reservation)
    {
        // Verify admin owns this hall
        if (auth('admin')->id() !== $reservation->hall->admin_id) {
            abort(403, 'Unauthorized action');
        } else {
            if ($reservation->accepted && ($reservation->reserved == null || Payments::where('reservation_id', $reservation->id)->where('payment_alias', 'Cancellation')->where('status', 1)->exists())) {
                // Check if this is a cancellation payment rejection
                $cancellationPayment = Payments::where('reservation_id', $reservation->id)
                    ->where('payment_alias', 'Cancellation')
                    ->where('status', 1)
                    ->first();

                if ($cancellationPayment) {
                    $cancellationPayment->update(['status' => 3]);
                    $reservation->update([
                        'user_cancelled' => false,
                        'status' => 4,
                        'charge' => $reservation->charge - $reservation->hall->cancellation_fee,
                    ]);
                    $start = Carbon::parse($reservation->start_time);
                    $end = Carbon::parse($reservation->end_time);
                    HallUnAvailability::create([
                        'hall_id' => $reservation->hall_id,
                        'date' => $reservation->reservation_date,
                        'start_time' => $start->copy()->subHours($reservation->pre_arrange_time)->format('H:i:s'),
                        'end_time' => $end->copy()->addHours($reservation->post_arrange_time)->format('H:i:s'),
                    ]);
                    return redirect()->route('admin.dashboard.route')->with('success', 'Cancellation payment rejected. Reservation restored.');
                }

                $reservation->update(['accepted' => true, 'reserved' => false, 'status' => 6]);
                // Reject all pending payments for this reservation
                Payments::where('reservation_id', $reservation->id)
                    ->where('status', 1)
                    ->update(['status' => 3]);
                // Send rejection notice to customer
                $reservation->customer->notify(new PaymentRejected($reservation));
                // Calculate actual start/end times (pre/post arrange)
                $start = Carbon::parse($reservation->start_time);
                $end = Carbon::parse($reservation->end_time);
                $actualStartTime = $start->copy()->subHours($reservation->pre_arrange_time)->format('H:i:s');
                $actualEndTime = $end->copy()->addHours($reservation->post_arrange_time)->format('H:i:s');
                // Delete the unavailability record associated with this reservation
                HallUnAvailability::where('hall_id', $reservation->hall_id)
                    ->where('date', $reservation->reservation_date)
                    ->where('start_time', $actualStartTime)
                    ->where('end_time', $actualEndTime)
                    ->delete();
                // send sms notifying reservation payment was rejected.
                $message = 'We regret to inform you that your payment for your reservation (' . $reservation->hall_name . ' on ' . $reservation->reservation_date . ' from ' . $reservation->start_time . ' to ' . $reservation->end_time . ' ) could not be accepted. Pls contact the admin.Your reservation ID is : ' . $reservation->id;
                $recipients = ($reservation->customer_tel);
                $smsService = new SmsServiceController($message, $recipients);
                $smsService->sendSms();
                return redirect()->route('admin.dashboard.route')->with('failed', 'Payment rejected!');

                //return back()->with('error', 'Reservation already rejected!');
            } elseif ($reservation->accepted && $reservation->reserved) {
                return back()->with('error', 'Already Reserved!');
            } else {
                return back()->with('error', 'Already Rejected!');
            }

        }
    }

    public function acceptSinglePayment(Payments $payment)
    {
        if (auth('admin')->id() !== $payment->reservation->hall->admin_id) {
            abort(403, 'Unauthorized action');
        }
        if ($payment->status != 1) {
            return back()->with('error', 'Payment already processed!');
        }
        $payment->update(['status' => 2]);
        $reservation = $payment->reservation;

        // Preliminary / advance payment approval
        if ($payment->payment_alias === 'Preliminary') {
            $reservation->update(['status' => 3]);
            $reservation->customer->notify(new AdvancePaymentAccepted($reservation));
            $message = 'Your advance payment for reservation (' . $reservation->hall_name . ' on ' . $reservation->reservation_date . ' from ' . $reservation->start_time . ' to ' . $reservation->end_time . ' ) has been accepted. Please proceed with the remaining payment to finalize your booking. Your reservation ID is : ' . $reservation->id;
            $recipients = ($reservation->customer_tel);
            $smsService = new SmsServiceController($message, $recipients);
            $smsService->sendSms();
            return back()->with('success', 'Advance payment accepted! Customer can now submit remaining payment.');
        }

        // Cancellation payment approval
        if ($payment->payment_alias === 'Cancellation') {
            $start = Carbon::parse($reservation->start_time);
            $end = Carbon::parse($reservation->end_time);
            $actualStartTime = $start->copy()->subHours($reservation->pre_arrange_time)->format('H:i:s');
            $actualEndTime = $end->copy()->addHours($reservation->post_arrange_time)->format('H:i:s');
            $newCharge = $reservation->charge + $reservation->hall->cancellation_fee;
            $reservation->update([
                'user_cancelled' => true,
                'status' => 5,
                'charge' => $newCharge,
            ]);
            HallUnAvailability::where('hall_id', $reservation->hall_id)
                ->where('date', $reservation->reservation_date)
                ->where('start_time', $actualStartTime)
                ->where('end_time', $actualEndTime)
                ->delete();
            return back()->with('success', 'Cancellation payment approved.');
        }

        // Remaining / full payment approval
        $reservation->update([
            'accepted' => true,
            'reserved' => true,
            'status' => 4,
        ]);
        Payments::where('reservation_id', $reservation->id)
            ->where('payment_alias', 'Remainings')
            ->where('status', 1)
            ->update(['status' => 2]);
        $reservation->customer->notify(new PaymentAccepted($reservation));
        $message = 'Your reservation has been Scheduled for ' . $reservation->hall_name . ' on ' . $reservation->reservation_date . ' from ' . $reservation->start_time . ' to ' . $reservation->end_time . '.' . PHP_EOL . 'Your reservation ID is : ' . $reservation->id;
        $recipients = ($reservation->customer_tel);
        $smsService = new SmsServiceController($message, $recipients);
        $smsService->sendSms();
        return back()->with('success', 'Payment accepted successfully!');
    }

    public function rejectSinglePayment(Payments $payment)
    {
        if (auth('admin')->id() !== $payment->reservation->hall->admin_id) {
            abort(403, 'Unauthorized action');
        }
        if ($payment->status != 1) {
            return back()->with('error', 'Payment already processed!');
        }
        $payment->update(['status' => 3]);
        $payment->reservation->customer->notify(new PaymentRejected($payment->reservation));
        return back()->with('success', 'Payment #' . $payment->id . ' rejected.');
    }

    public function rejectReservation(ReservationModel $reservation, Request $request)
    {
        if (auth('admin')->id() !== $reservation->hall->admin_id) {
            abort(403, 'Unauthorized action');
        }
        if ((int) $reservation->status === 6) {
            return back()->with('error', 'Reservation already rejected!');
        }
        
        $remarks = $request->input('remarks', 'No reason provided');
        
        $reservation->update([
            'accepted' => true, 
            'reserved' => false, 
            'status' => 6,
            'remarks' => $remarks
        ]);
        
        return back()->with('success', 'Reservation rejected successfully!');
    }

    public function cancel_reservation_by_customer(ReservationModel $reservation)
    {
        // Verify customer owns this reservation
        if (auth('customer')->id() !== $reservation->customer_id) {
            abort(403, 'Unauthorized action');
        } else {
            if ($reservation->user_cancelled) {
                // if user has already cancelled the reservation
                return back()->with('error', 'Reservation already cancelled!');
            } else {
                // Calculate actual start/end times (using pre/post arrange times)
                $start = Carbon::parse($reservation->start_time);
                $end = Carbon::parse($reservation->end_time);
                $actualStartTime = $start->copy()->subHours($reservation->pre_arrange_time)->format('H:i:s');
                $actualEndTime = $end->copy()->addHours($reservation->post_arrange_time)->format('H:i:s');

                // Record the cancellation fee (covered by amounts already paid)
                // Charge and other payment records remain untouched
                Payments::create([
                    'reservation_id' => $reservation->id,
                    'payment_alias' => 'Cancellation',
                    'amount' => $reservation->hall->cancellation_fee,
                    'receipt_path' => null,
                    'status' => 2, // approved - no admin verification needed
                ]);

                $reservation->update([
                    'user_cancelled' => true,
                    'status' => 5,
                ]);

                // Delete the unavailability record associated with this reservation
                HallUnAvailability::where('hall_id', $reservation->hall_id)
                    ->where('date', $reservation->reservation_date)
                    ->where('start_time', $actualStartTime)
                    ->where('end_time', $actualEndTime)
                    ->delete();

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json(['success' => true, 'message' => 'Reservation cancelled successfully!']);
                }
                return redirect()->route('load_customer_dashboard')->with('success', 'Reservation cancelled successfully!');
            }
        }
    }


    public function re_schedule_reservation_by_customer(Request $request, ReservationModel $reservation)
    {
        // Verify customer owns this reservation
        if (auth('customer')->id() !== $reservation->customer_id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized action');
        }

        // Validate the request data
        $validated = $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'new_start_time' => 'required|date_format:H:i',
            'new_end_time' => 'required|date_format:H:i|after:new_start_time',
            'new_pre_arrange_time' => 'nullable|integer|min:0|max:' . ($reservation->hall->max_pre_arrange_hours ?? 5),
            'new_post_arrange_time' => 'nullable|integer|min:0|max:' . ($reservation->hall->max_post_arrange_hours ?? 5),
        ]);

        // Check if reservation has already passed
        $originalEndDateTime = Carbon::parse($reservation->reservation_date . ' ' . $reservation->end_time);
        if ($originalEndDateTime->isPast()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot re-schedule a reservation that has already ended.'], 400);
            }
            return redirect()->back()->with('error', 'Cannot re-schedule a reservation that has already ended.');
        }

        DB::beginTransaction();

        try {
            // Get new values with defaults
            $newDate = $validated['new_date'];
            $newStartTime = $validated['new_start_time'];
            $newEndTime = $validated['new_end_time'];
            $newPreArrange = (int) ($request->input('new_pre_arrange_time') ?? 0);
            $newPostArrange = (int) ($request->input('new_post_arrange_time') ?? 0);

            // Calculate actual times for the NEW reservation
            $newStart = Carbon::createFromFormat('H:i', $newStartTime);
            $newEnd = Carbon::createFromFormat('H:i', $newEndTime);

            $newActualStart = $newStart->copy()->subHours($newPreArrange)->format('H:i');
            $newActualEnd = $newEnd->copy()->addHours($newPostArrange)->format('H:i');

            \Log::info('Rescheduling reservation ID: ' . $reservation->id, [
                'old_date' => $reservation->reservation_date,
                'old_start' => $reservation->start_time,
                'old_end' => $reservation->end_time,
                'new_date' => $newDate,
                'new_start' => $newStartTime,
                'new_end' => $newEndTime,
                'new_pre_arrange' => $newPreArrange,
                'new_post_arrange' => $newPostArrange,
                'new_actual_start' => $newActualStart,
                'new_actual_end' => $newActualEnd,
            ]);

            // Calculate old actual start/end times to match the specific unavailability record
            $oldStart = Carbon::parse($reservation->start_time);
            $oldEnd = Carbon::parse($reservation->end_time);
            $oldActualStart = $oldStart->copy()->subHours($reservation->pre_arrange_time)->format('H:i:s');
            $oldActualEnd = $oldEnd->copy()->addHours($reservation->post_arrange_time)->format('H:i:s');

            // Step 1: Delete old HallUnAvailability record (match specific record)
            $deletedCount = HallUnAvailability::where('hall_id', $reservation->hall_id)
                ->where('date', $reservation->reservation_date)
                ->where('start_time', $oldActualStart)
                ->where('end_time', $oldActualEnd)
                ->delete();

            \Log::info('Deleted ' . $deletedCount . ' old HallUnAvailability record(s) for hall ' . $reservation->hall_id);

            // Step 2: Create new HallUnAvailability record with updated details
            $hallUnavailability = HallUnAvailability::create([
                'hall_id' => $reservation->hall_id,
                'date' => $newDate,
                'start_time' => $newActualStart,
                'end_time' => $newActualEnd,
            ]);

            \Log::info('Created new HallUnAvailability record with ID: ' . $hallUnavailability->id);

            // Step 3: Update the reservation with new details
            $reservation->update([
                'reservation_date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime,
                'pre_arrange_time' => $newPreArrange,
                'post_arrange_time' => $newPostArrange,
                're_scheduled' => true,
                'status' => 7,
            ]);

            \Log::info('Updated ReservationModel with ID: ' . $reservation->id);

            DB::commit();

            \Log::info('Reservation rescheduled successfully. Reservation ID: ' . $reservation->id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reservation rescheduled successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Reservation rescheduled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reschedule error for reservation ' . $reservation->id . ': ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while rescheduling. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while rescheduling. Please try again.');
        }
    }

    public static function getReservationStatusLabel(int $status): string
    {
        return match ($status) {
            1 => 'Pending',
            2 => 'Accepted',
            3 => 'Confirmed',
            4 => 'Reserved',
            5 => 'Cancelled',
            6 => 'Rejected',
            7 => 'Rescheduled',
            8 => 'User-cancelled',
            default => 'Unknown',
        };
    }

    public static function getPaymentStatusLabel(int $status): string
    {
        return match ($status) {
            1 => 'Pending',
            2 => 'Approved',
            3 => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Get the status badge (label + CSS class) for a reservation on the customer dashboard.
     * Centralizes all status-display logic so new statuses only need to be added here.
     *
     * @param  \App\Models\ReservationModel  $reservation
     * @return array{label: string, class: string}
     */
    public static function getCustomerStatusBadge($reservation): array
    {
        $status = (int) $reservation->status;

        switch ($status) {
            case 1: // Pending
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-warning text-dark'
                ];
            case 2: // Accepted
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-primary text-white'
                ];
            case 3: // Confirmed
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-info text-white'
                ];
            case 4: // Reserved (final success)
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-success text-white'
                ];
            case 5: // Cancelled (by admin or system)
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-danger text-white'
                ];
            case 6: // Rejected
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-secondary text-white'
                ];
            case 7: // Rescheduled
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-warning text-dark' // or 'bg-orange' if you have custom
                ];
            case 8: // User-cancelled (distinct from admin-cancelled)
                return [
                    'status_id' => $status,
                    'label' => self::getReservationStatusLabel($status),
                    'class' => 'bg-dark text-white'
                ];
            default:
                return [
                    'status_id' => $status,
                    'label' => 'Unknown',
                    'class' => 'bg-secondary text-white'
                ];
        }
    }

    /**
     * Get badge for a single payment record.
     * Pass $prelim, $remain, or $cancel from the blade.
     *
     * @param  \App\Models\Payments|null  $payment
     * @return array{label: string, class: string}
     */
    public static function getCustomerPaymentStatusBadge($stage): array
    {
        $status = (int) ($stage?->status ?? 0);

        switch ($status) {
            case 1: // Pending
                return ['label' => self::getPaymentStatusLabel($status), 'class' => 'bg-warning text-dark'];
            case 2: // Approved
                return ['label' => self::getPaymentStatusLabel($status), 'class' => 'bg-warning text-dark'];
            case 3: // Rejected
                return ['label' => self::getPaymentStatusLabel($status), 'class' => 'bg-danger'];
            default: // No payment record
                return ['label' => 'Unavailable', 'class' => 'bg-danger'];
        }
    }

    public static function getPaymentStatusBadge($payment): array
    {
        $status = (int) $payment->status;
        return match ($status) {
            1 => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            2 => ['label' => 'Approved', 'class' => 'bg-success'],
            3 => ['label' => 'Rejected', 'class' => 'bg-danger'],
            default => ['label' => 'Unknown', 'class' => 'bg-secondary'],
        };
    }
}
