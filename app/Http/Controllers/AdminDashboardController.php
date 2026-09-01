<?php

namespace App\Http\Controllers;

use App\Models\ReservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HallModel;
use App\Models\CustomerModel;
use App\Models\AdminModel;
use App\Models\HallUnAvailability;
use App\Notifications\ReservationAccepted;
use App\Notifications\ReservationRejectedEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SmsServiceController;

class AdminDashboardController extends Controller
{
    public function admin_dashboard_fetch_data(HallModel $hall, ReservationModel $reservation)
    {
        // Get authenticated admin
        $admin = Auth::guard('admin')->user();
        // get all halls
        $halls = HallModel::all();
        // Fetch halls only for this admin using admin-id
        $halls = HallModel::where('admin_id', $admin->id)->with(['availability','fixedfacilities','unitfacilities','packages'])->get();
        //$period = $hall->availability;

        // reservation part

        // Get all hall IDs belonging to this admin
        $hallIds = HallModel::where('admin_id', $admin->id)->pluck('id');
        // Get reservations for these halls
        $reservations = ReservationModel::whereIn('hall_id', $hallIds)->with('hall','customer')->orderBy('created_at', 'desc')->paginate(5);
            
        return view('AdminDashboard', compact('halls', 'reservations', 'admin'));
    }



    /*public function hall_update(Request $request, HallModel $hall)
    {

        if ($hall->admin_id !== Auth::guard('admin')->user()->id)
        {
            abort(403, 'Unauthorized action');
        } else {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'type' => 'required|in:wedding,party,exhibition,reception,sport,arena,concert,memorial,lecture,building,floor,room,outdoortheator,multipurpose,resorts,bangalow,conference,banquet,convention,crematorium,auditorium,community,stadium,outdoorground',
                'price' => 'required|numeric',
                'capacity' => 'required|integer|min:1',
                'facilities' => 'required|array',
                'facilities.*' => 'required|in:water_electricity,wifi_internet,bathrooms_toilets,lighting_sound,heating_ac,seating,first_aid,kitchen,cafeteria,bar_lounge,catering,stage_podium,projectors,microphones,backup_generator,dressing_rooms,parking,storage,swimming_pool,office_facilities,meeting_rooms,conference_call,printers,ev_charging',
                'description' => 'nullable|string',
                'address' => 'required|max:500',
                'province' => 'required|max:100',
                'district' => 'required|max:100',
                'area' => 'required|max:100',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'images' => 'array',
                'images.*' => 'image|max:2048',

                'slots.*.date' => 'required|date|after_or_equal:today',
                'slots.*.start_time' => 'required',
                'slots.*.end_time' => 'required|after:slots.*.start_time',
                'new_slots.*.date' => 'sometimes|required|date|after_or_equal:today',
                'new_slots.*.start_time' => 'sometimes|required',
                'new_slots.*.end_time' => 'sometimes|required|after:new_slots.*.start_time',
            ]);

            // 1) Update the hall’s own columns
            $data = [
                'name' => $validated['name'],
                'type' => $validated['type'],
                'price' => $validated['price'],
                'capacity' => $validated['capacity'],
                'facilities' => $validated['facilities'],
                'description' => $validated['description'] ?? '',
                'address' => $validated['address'],
                'province' => $validated['province'],
                'district' => $validated['district'],
                'area' => $validated['area'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ];

            // 2) Handle image replacement
            if ($request->hasFile('images'))
            {
                // Delete existing images from storage
                foreach ($hall->images as $oldImage)
                {
                    Storage::disk('public')->delete($oldImage);
                }
                // Upload new images
                $stored = [];
                foreach ($request->file('images') as $file)
                {
                    // Consistent path with create method
                    $path = $file->store('halls/images', 'public');
                    $stored[] = $path;
                }
                $data['images'] = $stored;
            }

            //die(json_encode($data));
            $hall->update($data);

            // 3) Now your existing slot logic
            if ($request->has('delete_slots')) {
                HallUnAvailability::whereIn('id', $request->delete_slots)->delete();
            }
            foreach ($request->slots ?? [] as $id => $slot) {
                HallUnAvailability::findOrFail($id)->update($slot);
            }
            foreach ($request->new_slots ?? [] as $slot) {
                HallUnAvailability::create([
                    'hall_id' => $hall->id,
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]);
            }

            return redirect()->back()->with('success', 'Hall updated successfully!');
        }
    }*/
    
    public function DeactivateHall(HallModel $hall)
    {
        // Verify ownership
        if ($hall->admin_id !== Auth::guard('admin')->user()->id)
        {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }
        else
        {
            if($hall->available)
            {
                // Block deactivation if the hall has ongoing reservations
                $hasOngoingReservation = $hall->reservations()
                    ->where('status', '!=', 5)
                    ->where('user_cancelled', false)
                    ->whereDate('reservation_date', '>=', now()->toDateString())
                    ->exists();

                if ($hasOngoingReservation)
                {
                    return back()->with('error', 'Cannot make this hall unavailable because it has ongoing reservations.');
                }

                try
                {
                    $hall->update(['available' => false]);
                    return back()->with('success', 'Hall is now unavailable!');
                }
                catch (\Exception $e)
                {
                    return back()->with('error', 'Failed to make hall unavailable!');
                }
            }
            else
            {
                try {
                    $hall->update(['available' => true]);
                    return back()->with('success', 'Hall is now available!');
                } catch (\Exception $e) {
                    return back()->with('error', 'Failed to make hall available!');
                }
            }
            
        }
    }

    public function checkOngoingReservations(HallModel $hall)
    {
        // Verify ownership
        if ($hall->admin_id !== Auth::guard('admin')->user()->id)
        {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $ongoingReservations = $hall->reservations()
            ->where('status', '!=', 5)
            ->where('user_cancelled', false)
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->get(['customer_name', 'reservation_date', 'start_time', 'end_time']);

        return response()->json([
            'ongoing' => $ongoingReservations->isNotEmpty(),
            'count' => $ongoingReservations->count(),
            'reservations' => $ongoingReservations,
        ]);
    }

    public function insert_bank(Request $request)
    {
        // Validate the form input
        $request->validate(['bank' => 'required|string|max:255', 'account_name' => 'required|string|max:255', 'account_number' => 'required|string|max:50']);
        // Get authenticated admin
        $admin = Auth::guard('admin')->user();
        // Update admin's bank details
        $admin->update(['bank' => $request->bank, 'account_name' => $request->account_name, 'account_number' => $request->account_number]);
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Bank details updated successfully.');
    }

    public function accept_reservation_request(Request $request, ReservationModel $reservation)
    {
        //dump($reservation);

        // Prevent processing if already accepted
        if ($reservation->accepted == null)
        {
            // Get discount_custom from request, default to current value if not provided
            $discountCustom = $request->input('discount_custom', $reservation->discount_custom);
            // Get advanceAmount from request, default to current value if not provided
            $advanceAmount = $request->input('advanceAmount', $reservation->advanceAmount);
            // Get date fields from request, default to current values if not provided
            $advancePaymentDate = $request->input('advancePaymentDate', $reservation->advancePaymentDate);
            $cancellationExpiryDate = $request->input('cancellationExpiryDate', $reservation->cancellationExpiryDate);
            $rescheduledExpiryDate = $request->input('rescheduledExpiryDate', $reservation->rescheduledExpiryDate);
            
            // Get customer to check type
            $customer = CustomerModel::find($reservation->customer_id);
            
            // Update reservation status and discount_custom
            // NOTE: charge is NOT modified - original charge is kept in the database.
            // The discount is stored separately in discount_custom and deducted at display/calculation time.
            $reservation->update([
                'accepted' => true,
                'status' => 2,
                'reserved' => null,
                'discount_custom' => $discountCustom,
                'advanceAmount' => $advanceAmount,
                'advancePaymentDate' => $advancePaymentDate,
                'cancellationExpiryDate' => $cancellationExpiryDate,
                'rescheduledExpiryDate' => $rescheduledExpiryDate,
            ]);
            
            // Get related hall
            $hall = HallModel::find($reservation->hall_id);
            $customer = CustomerModel::find($reservation->customer_id);
            // Send notification to customer
            $customer->notify(new ReservationAccepted($reservation, $hall, $customer));
            //send sms notifying reservation request was accepted.
            $message = 'Your reservation has been accepted by admin for ' . $hall->name . ' on ' . $reservation->reservation_date . ' from ' . $reservation->start_time . ' to ' . $reservation->end_time . '.' . PHP_EOL . 'Your reservation ID is : ' . $reservation->id;
            $recipients = ($customer->telephone_number);
            $smsService = new SmsServiceController($message,$recipients);
            $smsService->sendSms();
            return back()->with('success', 'Reservation accepted! Email sent to customer.');
            //return back()->withErrors(['Test error message']);
        }
        elseif ($reservation->accepted)
        {
            return back()->with('error', 'Reservation already accepted!');
        }
        else
        {
            return back()->with('error', 'Reservation already rejected!');
        }
    }

    public function reject_reservation_request(ReservationModel $reservation)
    {
        // Prevent processing if already rejected
        if ($reservation->accepted == null)
        {
            $reservation->update(['accepted' => false, 'reserved' => null]);
            // Get related hall
            $hall = HallModel::find($reservation->hall_id);
            $customer = CustomerModel::find($reservation->customer_id);
            // Send notification to customer
            $customer->notify(new ReservationRejectedEmail($reservation, $hall, $customer));
            // Delete the unavailability record associated with this reservation
            HallUnAvailability::where('hall_id', $reservation->hall_id)
            ->where('date', $reservation->reservation_date)
            ->where('start_time', $reservation->start_time)
            ->where('end_time', $reservation->end_time)
            ->delete();
            // send sms notifying reservation request was rejected.
            $message = 'We regret to inform you that your reservation request ('.$hall->name.' on '.$reservation->reservation_date.' from '.$reservation->start_time.' to '.$reservation->end_time.' ) could not be approved due to unavoidable circumstances. Your reservation ID is : ' . $reservation->id;
            $recipients = ($customer->telephone_number);
            $smsService = new SmsServiceController($message,$recipients);
            $smsService->sendSms();
            return back()->with('success', 'Reservation rejected! Time slot freed and email sent to customer.');            
        }
        elseif($reservation->accepted)
        {
            return back()->with('error', 'Reservation already accepted!');
        }
        else
        {
            return back()->with('error', 'Reservation already rejected!');
        }
        
    }

    public function viewSlip(ReservationModel $reservation)
    {
        // Verify admin owns this hall
        if(auth('admin')->id() !== $reservation->hall->admin_id)
        {
            abort(403, 'Unauthorized action');
        }
        else
        {
            return view('admin-view-slip', compact('reservation'));
        }        
    }


}