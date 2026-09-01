<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Notifications\SendCustomerOTP;
use Illuminate\Auth\Events\Registered;
use App\Models\CustomerModel;
use App\Models\ReservationModel;
use App\Models\Payments;
use App\Models\HallModel;
use App\Models\FixedPriceFacilitiesModel;
use App\Models\UnitPriceFacilitiesModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Services\ESMSWS;//sms api
use App\Http\Controllers\SmsServiceController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    private $verified = true;

    function ShowRegisterPage()
    {
        return view('home');
    }

    function Register(Request $request)
    {
        $request->validate(
            [
                'profile_title' => 'required|string|in:Mr.,Mrs.,Miss.,Doc.,Rev.,Prof.',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email:rfc,dns|unique:customers_table',
                'telephone_number' => 'required',
                'national_id' => 'required|unique:customers_table',
                'password' => 'required',
                'confirm_password' => 'required',
                'type' => 'required|string|in:private,government',
            ]);

        $email_otp = Str::random(6); // generate randome otp for email
        $mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);// generate randome otp for mobile
        //add row of data
        $data['profile_title'] = $request->profile_title;
        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['email'] = $request->email;
        $data['telephone_number'] = $request->telephone_number;
        $data['national_id'] = $request->national_id;
        $data['password'] = Hash::make($request->password); // Hash::make for encryption
        $data['type'] = $request->type;
        $data['email_verification_otp'] = $email_otp;
        $data['mobile_verification_otp'] = $mobile_otp;
        $data['otp_expires_at'] = Carbon::now()->addMinutes(10);

        $customer = CustomerModel::create($data); // add raw of data to table

        // lets check creation of $customer is successfull or not
        if (!$customer) {
            //if  there is no $customer redirects to "home_route" with a error massage.
            return redirect(route('registration_get_route'))->with('error_key_1', 'Registration is failed. Pls try again');
        } else {
            // send sms otp to customer at registration..
            $message = 'Welcome to Public Facilities Reservation Portal.' . PHP_EOL . '"' . $mobile_otp . '" is your one-time entry otp code. Do not share with others.';
            $recipients = ($request->telephone_number);
            $smsService = new SmsServiceController($message, $recipients);
            $smsService->sendSms();


            // Send OTP via email
            $customer->notify(new SendCustomerOTP($data['email_verification_otp']));

            // Store customer ID in session for verification
            $request->session()->put('verify_customer_id', $customer->id);

            // Redirect to verification notice route
            return redirect()->route('customer.verification.notice')
                ->with([
                    'email' => $customer->email,
                    'telephone_number' => $customer->telephone_number
                ]);
        }
    }

    public function verifyOTP(Request $request)
    {
        //$request->validate(['otp' => 'required|string|size:6']);
        $request->validate([
            'otp' => 'required|string|size:6',
            'otp1' => 'required|digits:1',
            'otp2' => 'required|digits:1',
            'otp3' => 'required|digits:1',
            'otp4' => 'required|digits:1',
            'otp5' => 'required|digits:1',
            'otp6' => 'required|digits:1',
        ]);
        // Get email otp to another variable
        $email_otp = $request->otp;
        // Concatenate mobile OTP parts
        $mobile_otp = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4 . $request->otp5 . $request->otp6;

        $customer = CustomerModel::find(session('verify_customer_id'));

        if (!$request->session()->has('verify_customer_id')) {
            return redirect()->route('home_route')->with('error', 'Session expired');
        }

        if (!$customer) {
            return redirect()->route('registration_get_route')->with('error', 'Session expired. Please register again.');
        }

        //if ($customer->email_verification_otp === $email_otp && $customer->mobile_verification_otp === $mobile_otp && Carbon::now()->lt($customer->otp_expires_at))
        if ($customer->email_verification_otp === $email_otp && $customer->mobile_verification_otp === $mobile_otp && Carbon::now()->lt($customer->otp_expires_at)) {

            $customer->update([
                'email_verified_at' => now(),
                'mobile_verified_at' => now(),
                'email_verification_otp' => null,
                'mobile_verification_otp' => null,
                'otp_expires_at' => null
            ]);

            $request->session()->forget('verify_customer_id');
            Auth::guard('customer')->login($customer);

            // Redirect to home route (the home route controller will load halls data automatically)
            return redirect()->route('home_route')->with('success', 'Email verified successfully!');
        } else {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

    }

    public function resendOTP(Request $request)
    {
        $customer = CustomerModel::find(session('verify_customer_id'));

        if (!$customer) {
            return redirect()->route('registration_get_route')->with('error', 'Session expired. Please register again.');
        }

        $new_email_otp = Str::random(6); // generate randome otp for email
        $new_mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);// generate randome otp for mobile

        $customer->update([
            'email_verification_otp' => $new_email_otp,
            'mobile_verification_otp' => $new_mobile_otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        //  re-send sms otp to customer at registration..
        $message = 'Welcome back to Public Facilities Reservation Portal. Please verify your account before sign in' . PHP_EOL . '"' . $new_mobile_otp . '" is your one-time entry code. Do not share with others.';
        $recipients = ($customer->telephone_number);
        $smsService = new SmsServiceController($message, $recipients);
        $smsService->sendSms();

        $customer->notify(new SendCustomerOTP($new_email_otp));

        return back()->with('status', 'New OTP has been sent to your email and telephone number!');
    }

    function ShowLoginPage()
    {
        return view('home');
    }

    function Login(Request $request)
    {
        try {
            // First validate inputs
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Check if admin is logged in and logout to prevent cross-session conflicts
            if (Auth::guard('admin')->check()) {
                Auth::guard('admin')->logout();
            }

            // Check if user is already logged in
            if (Auth::guard('customer')->check()) {
                $current_user = Auth::guard('customer')->user();

                // If trying to login as the same user, just redirect
                if ($current_user->email === $request->email) {
                    // Regenerate session to prevent fixation
                    $request->session()->regenerate();

                    return redirect()->intended(route('load_venues_page'))
                        ->with('success', 'You are already logged in!');
                } else {
                    // Different user trying to login - logout current user first
                    Auth::guard('customer')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }

            // Check if the email exists in the customers table
            $customerExists = CustomerModel::where('email', $request->email)->exists();

            if (!$customerExists) {
                return redirect()
                    ->back()
                    ->with('error_key_2', 'Email is not available. Pls sign up')
                    ->withInput($request->only('email'));
            }

            // Attempt login with credentials
            $credentials = $request->only('email', 'password');

            if (Auth::guard('customer')->attempt($credentials)) {
                // Regenerate session to prevent fixation attacks
                $request->session()->regenerate();

                $customer = Auth::guard('customer')->user();

                // Check email verification status
                if (!$customer->email_verified_at) {
                    Auth::guard('customer')->logout();
                    $request->session()->put('verify_customer_id', $customer->id);

                    // Generate new OTPs
                    $new_email_otp = Str::random(6);
                    $new_mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                    // Update customer with new OTPs
                    $customer->update([
                        'email_verification_otp' => $new_email_otp,
                        'mobile_verification_otp' => $new_mobile_otp,
                        'otp_expires_at' => Carbon::now()->addMinutes(10)
                    ]);

                    // Send SMS OTP
                    $message = 'Welcome back to Public Facilities Reservation Portal. Please verify your account before sign in' . PHP_EOL . '"' . $new_mobile_otp . '" is your one-time entry code. Do not share with others.';
                    $recipients = ($customer->telephone_number);
                    $smsService = new SmsServiceController($message, $recipients);
                    $smsService->sendSms();

                    // Send email OTP
                    $customer->notify(new SendCustomerOTP($new_email_otp));

                    // Redirect to verification notice
                    return redirect()->route('customer.verification.notice')
                        ->with([
                            'email' => $customer->email,
                            'telephone_number' => $customer->telephone_number
                        ]);
                } else {
                    return redirect()->intended(route('load_venues_page'))
                        ->with('success', 'Login successful!');
                }
            } else {
                return redirect()
                    ->back()
                    ->with('error_key_2', 'You entered wrong credentials. Try again.')
                    ->withInput($request->only('email'));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error_key_2', 'An error occurred during login. Please try again.');
        }
    }







    function Logout(Request $request)
    {
        // Logout only the customer guard
        Auth::guard('customer')->logout();

        // Remove customer-specific session data
        $request->session()->forget('verify_customer_id');

        // Invalidate only customer's session
        $request->session()->invalidate();

        // Regenerate CSRF token to prevent conflicts
        $request->session()->regenerateToken();

        return redirect(route('home_route'));
    }

    //////Hall process////

    public function Load_Venues_Page(Request $request)
    {
        // Get all distinct filter options
        $filterOptions = [
            'types' => HallModel::distinct()->pluck('type'),
            'provinces' => HallModel::distinct()->pluck('province'),
            'districts' => HallModel::distinct()->pluck('district'),
            'areas' => HallModel::distinct()->pluck('area'),
            'facilities' => $this->getAllFacilitiesForFiltering()
        ];

        // Base query - shows only available halls by default
        $query = HallModel::query()->where('available', true)->with(['availability', 'fixedfacilities', 'unitfacilities']);

        // Apply filters
        $this->applyFilters($query, $request);

        // Get paginated results
        $halls = $query->paginate(12);

        return view('VenuesPage', array_merge(
            ['halls' => $halls],
            $filterOptions,
            ['request' => $request],
            ['maxPrice' => HallModel::max('price') ?? 1000000]
        ));
    }

    /**
     * Get all unique facilities from fixed and unit price facilities
     */
    private function getAllFacilitiesForFiltering()
    {
        // Get fixed price facilities
        $fixedFacilities = \App\Models\FixedPriceFacilitiesModel::distinct()
            ->pluck('name')
            ->unique()
            ->values();

        // Get unit price facilities
        $unitFacilities = \App\Models\UnitPriceFacilitiesModel::distinct()
            ->pluck('name')
            ->unique()
            ->values();

        // Combine all facilities and remove duplicates
        $allFacilities = $fixedFacilities
            ->merge($unitFacilities)
            ->unique()
            ->sort()
            ->values();

        return $allFacilities;
    }

    private function applyFilters($query, $request)
    {
        // Search Filter (by hall name)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orderByRaw("CASE WHEN name LIKE ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END", 
                               [$searchTerm . '%', '% ' . $searchTerm . '%']);
        }

        // Price Filter
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Capacity Filter
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        // Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Location Filters
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        // NEW: Facilities Filter (for fixed and unit price facilities)
        if ($request->filled('facilities')) {
            $query->where(function ($q) use ($request) {
                foreach ($request->facilities as $facility) {
                    $q->orWhereHas('fixedfacilities', function ($fixedQuery) use ($facility) {
                        $fixedQuery->where('name', $facility);
                    })->orWhereHas('unitfacilities', function ($unitQuery) use ($facility) {
                        $unitQuery->where('name', $facility);
                    });
                }
            });
        }

        // Date and Time Availability Filter (keep existing)
        if ($request->filled('available_date') && $request->filled('start_time') && $request->filled('end_time')) {
            $query->whereDoesntHave('availability', function ($q) use ($request) {
                $q->where('date', $request->available_date)
                    ->where(function ($timeConflictQuery) use ($request) {
                        $timeConflictQuery->whereTime('start_time', '<=', $request->end_time)
                            ->whereTime('end_time', '>=', $request->start_time);
                    });
            });
        } elseif ($request->filled('available_date')) {
            $query->whereHas('availability', function ($q) use ($request) {
                $q->where('date', $request->available_date);

                if ($request->filled('start_time') && $request->filled('end_time')) {
                    $q->where(function ($timeQuery) use ($request) {
                        $timeQuery->whereTime('start_time', '<=', $request->start_time)
                            ->whereTime('end_time', '>=', $request->end_time);
                    });
                }
            });
        }
    }

    // opens customer dashboard and fetch data
    public function Load_Customer_Dashboard(HallModel $hall, ReservationModel $reservation)
    {
        // Get authenticated customer
        $customer = Auth::guard('customer')->user();
        // getting relevant all reservations with thier halls regarding above customer
        $reservations = ReservationModel::where('customer_id', $customer->id)
            ->with([
                'hall.availability', // Load hall's unavailable slots
                'hall.admin',        // Load hall's owner (admin)
                'payments',    // load with payments
            ])->orderBy('created_at', 'desc')->paginate(5);

        $reservations->getCollection()->transform(function ($reservation) 
        {
            $totalPaid = $reservation->payments->sum('amount');
            $reservation->total_paid = $totalPaid;
            $reservation->remaining = $reservation->charge - $totalPaid;
            $reservation->advance_amount = $reservation->charge * 0.1; // should be 1 lkh
            $reservation->is_advance_paid = $totalPaid >= $reservation->advance_amount;

            return $reservation;
        });

        // Calculate reservation statistics matching the actual workflow
        $total = (ReservationModel::where('customer_id', $customer->id))->count();

        // Pending Requests: reservations awaiting admin's approval only (status = 1)
        $pendingRequest = ReservationModel::where('customer_id', $customer->id)
            ->where('status', 1)->count();

        // Payment in Progress: all pending payment slips for the customer (payments.status = 1),
        // regardless of payment stage (Preliminary / Remainings / Cancellation)
        $paymentInProgress = Payments::whereHas('reservation', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->where('status', 1)->count();

        // Successful Reservations: reservation completed (status = 4), not cancelled
        $successful = ReservationModel::where('customer_id', $customer->id)
            ->where('status', 4)->count();

        // Cancelled: reservation cancelled (status = 5)
        $closed = ReservationModel::where('customer_id', $customer->id)
            ->where('status', 5)->count();

        // Reservation Rejected: reservation itself rejected by admin (status 6)
        $rejected = ReservationModel::where('customer_id', $customer->id)
            ->where('status', 6)->count();

        // Payment Rejected: any payment slip for the customer's reservations was rejected
        $paymentRejected = ReservationModel::where('customer_id', $customer->id)
            ->whereHas('payments', function ($query) {
                $query->where('status', 3);
            })->count();

        return view('CustomerDashboardPage', compact(
            'customer', 'reservations',
            'total', 'pendingRequest', 'paymentInProgress', 'successful', 'closed',
            'rejected', 'paymentRejected'
        ));
    }

    // update customer account details from customer dashboard
    public function udpate_customer_account_details(Request $request, CustomerModel $customer)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('customers_table')->ignore($customer->id)],
            'telephone_number' => 'required|string|max:20',
            'national_id' => ['required', 'string', Rule::unique('customers_table')->ignore($customer->id)]
        ]);


        // Update customer data
        $customer->first_name = $validated['first_name'];
        $customer->last_name = $validated['last_name'];
        $customer->email = $validated['email'];
        $customer->telephone_number = $validated['telephone_number'];
        $customer->national_id = $validated['national_id'];
        // update changes
        $customer->save();
        return redirect()->back()->with('success', 'Account details updated successfully!');
    }

    public function requestPasswordReset(Request $request)
    {
        Log::info("Starting password reset request.");

        // Validate input
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ]);
        Log::info('Request validated successfully. Request data:', $request->except(['password', 'password_confirmation', 'current_password']));

        $customer = Auth::guard('customer')->user();
        Log::info("Customer found: ID = {$customer->id}, Email = {$customer->email}");

        // Verify current password
        if (!Hash::check($request->current_password, $customer->password)) {
            Log::warning("Incorrect current password provided by customer. Customer ID: {$customer->id}");
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['The current password is incorrect.']]
            ], 422);
        }
        Log::info("Current password verified successfully for customer ID: {$customer->id}");

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::info("Generated OTP: $otp for customer ID: {$customer->id}");

        // Store new password temporarily and OTP
        $customer->email_verification_otp = $otp;
        $customer->temp_password = Hash::make($request->password);
        $customer->otp_expires_at = Carbon::now()->addMinutes(10);
        $customer->password_reset_expiry = Carbon::now()->addMinutes(15);
        $customer->save();
        Log::info("Temporary password and OTP saved for customer. Customer ID: {$customer->id}", [
            'email_verification_otp' => $otp,
            'otp_expires_at' => $customer->otp_expires_at,
            'password_reset_expiry' => $customer->password_reset_expiry
        ]);

        // Send OTP email
        try {

            $customer->notify(new SendCustomerOTP($otp));

            //send sms notifying reservation request was accepted.
            $message = 'Your reset password otp is ' . $otp;
            $recipients = ($customer->telephone_number);
            $smsService = new SmsServiceController($message, $recipients);
            $smsService->sendSms();

            Log::info("OTP sent to customer {$customer->email}: $otp");

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'customer_id' => $customer->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to customer {$customer->email}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    public function verifyPasswordReset(Request $request)
    {
        Log::info("Starting password reset verification for customer ID: " . $request->customer_id);
        Log::info("Raw request data:", $request->all());

        // Validate input
        $request->validate([
            'otp1' => 'required|digits:1',
            'otp2' => 'required|digits:1',
            'otp3' => 'required|digits:1',
            'otp4' => 'required|digits:1',
            'otp5' => 'required|digits:1',
            'otp6' => 'required|digits:1',
            'customer_id' => 'required|integer'
        ]);
        Log::info('OTP fields validated successfully.');

        // Concatenate OTP parts
        $otp = $request->otp1 . $request->otp2 . $request->otp3 .
            $request->otp4 . $request->otp5 . $request->otp6;
        Log::info("OTP entered by user: $otp");

        $customer = CustomerModel::find($request->customer_id);
        if (!$customer) {
            Log::warning("Customer not found for customer ID: {$request->customer_id}");
            return response()->json([
                'success' => false,
                'errors' => ['otp' => ['Invalid request.']]
            ], 422);
        }
        Log::info("Customer found: ID = {$customer->id}, Email = {$customer->email}");
        Log::info("Customer stored OTP: {$customer->email_verification_otp}");

        // Check if reset window expired
        if (Carbon::now()->gt($customer->password_reset_expiry)) {
            Log::warning("Password reset window expired for customer ID: {$customer->id}");
            return response()->json([
                'success' => false,
                'errors' => ['otp' => ['Password reset window expired. Please start over.']]
            ], 422);
        }
        Log::info("Password reset window is still valid for customer ID: {$customer->id}");

        // Verify OTP
        if ($customer->email_verification_otp !== $otp) {
            Log::warning("Invalid OTP entered. Customer ID: {$customer->id}, Entered OTP: $otp, Expected OTP: {$customer->email_verification_otp}");
            return response()->json([
                'success' => false,
                'errors' => ['otp' => ['Invalid OTP.']]
            ], 422);
        }
        Log::info("OTP verified successfully for customer ID: {$customer->id}");

        // Check OTP expiration
        if (Carbon::now()->gt($customer->otp_expires_at)) {
            Log::warning("OTP expired for customer ID: {$customer->id}");
            return response()->json([
                'success' => false,
                'errors' => ['otp' => ['OTP has expired.']]
            ], 422);
        }
        Log::info("OTP is still valid for customer ID: {$customer->id}");

        // Update password permanently
        $customer->password = $customer->temp_password;
        $customer->temp_password = null;
        $customer->email_verification_otp = null;
        $customer->otp_expires_at = null;
        $customer->password_reset_expiry = null;
        $customer->save();
        Log::info("Password updated for customer ID: {$customer->id}. Temporary data cleared.");

        // Log out the customer
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info("Customer logged out successfully. Session invalidated.");

        return response()->json([
            'success' => true,
            'redirect' => route('home_route'),
            'message' => 'Password updated successfully! Please login with your new password.'
        ]);
    }

    public function resendPasswordResetOTP(Request $request)
    {
        Log::info("Starting resend OTP request for customer ID: {$request->customer_id}");

        // Validate input
        $request->validate(['customer_id' => 'required|integer']);
        Log::info('Request validated successfully. Request data:', $request->all());

        $customer = CustomerModel::find($request->customer_id);
        if (!$customer) {
            Log::warning("Customer not found for customer ID: {$request->customer_id}");
            return response()->json(['success' => false, 'error' => 'Customer not found.'], 404);
        }
        Log::info("Customer found: ID = {$customer->id}, Email = {$customer->email}");

        // Check if reset window expired
        if (Carbon::now()->gt($customer->password_reset_expiry)) {
            Log::warning("Password reset window expired for customer ID: {$customer->id}");
            return response()->json(['success' => false, 'error' => 'Password reset window expired. Please start over.'], 410);
        }
        Log::info("Password reset window is still valid for customer ID: {$customer->id}");

        // Generate new OTP
        $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::info("Generated new OTP: $newOtp for customer ID: {$customer->id}");

        // Save new OTP and expiry
        $customer->email_verification_otp = $newOtp;
        $customer->otp_expires_at = Carbon::now()->addMinutes(10);
        $customer->save();
        Log::info("New OTP saved for customer ID: {$customer->id}. New OTP: $newOtp");

        try {
            $customer->notify(new SendCustomerOTP($newOtp));
            //send sms notifying reservation request was accepted.
            $message = 'Your reset password otp is ' . $newOtp;
            $recipients = ($customer->telephone_number);
            $smsService = new SmsServiceController($message, $recipients);
            $smsService->sendSms();
            Log::info("New OTP sent to customer {$customer->email}: $newOtp");

            return response()->json(['success' => true, 'message' => 'New OTP sent.']);
        } catch (\Exception $e) {
            Log::error("Failed to resend OTP to customer {$customer->email}: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to resend OTP. Please try again.'], 500);
        }
    }



    function ShowTestPage(HallModel $hall)
    {
        die($hall . 'testing');
        // $hall = HallModel::where('id', 1)
        //     ->with(['availability', 'fixedfacilities', 'unitfacilities', 'packages', 'reservations'])
        //     ->first();

        return view('test', compact('hall'));
    }

    /**
     * Show calendar UI for a given hall to customers.
     */
    public function showCalendar($hall_id)
    {
        $hall = HallModel::with(['availability'])->findOrFail($hall_id);

        return view('calendar', [
            'hall' => $hall,
            'availabilityRange' => [
                'start' => now()->format('Y-m-d'),
                'end' => now()->addDays(180)->format('Y-m-d')
            ]
        ]);
    }

    // ========== FORGOT PASSWORD FUNCTIONALITY FOR NON-LOGGED-IN USERS ==========

    /**
     * Handle forgot password request - Step 1: Validate email and phone
     */
    public function forgotPasswordRequest(Request $request)
    {
        try {
            Log::info("Starting forgot password request.");
            
            // Validate input
            $request->validate([
                'email' => 'required|email|exists:customers_table,email',
                'telephone_number' => 'required|string',
            ]);
            
            Log::info('Forgot password request validated successfully.');
            
            // Find customer by email
            $customer = CustomerModel::where('email', $request->email)->first();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.'
                ], 404);
            }
            
            // Verify phone number matches
            if ($customer->telephone_number !== $request->telephone_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'The phone number does not match our records.'
                ], 422);
            }
            
            Log::info("Customer found for forgot password: ID = {$customer->id}, Email = {$customer->email}");
            
            // Generate OTPs
            $email_otp = Str::random(6);
            $mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTPs and session info
            $customer->email_verification_otp = $email_otp;
            $customer->mobile_verification_otp = $mobile_otp;
            $customer->otp_expires_at = Carbon::now()->addMinutes(10);
            $customer->password_reset_expiry = Carbon::now()->addMinutes(30);
            $customer->save();
            
            // Store customer ID in session for verification
            $request->session()->put('forgot_password_customer_id', $customer->id);
            
            Log::info("OTPs generated for forgot password. Customer ID: {$customer->id}");
            
            // Send OTPs
            try {
                // Send email OTP
                $customer->notify(new SendCustomerOTP($email_otp));
                
                // Send SMS OTP
                $message = 'Password Reset Request for Public Facilities Reservation Portal.' . PHP_EOL . 
                          'Email OTP: ' . $email_otp . PHP_EOL . 
                          'Phone OTP: ' . $mobile_otp . PHP_EOL . 
                          'Valid for 10 minutes. Do not share.';
                $smsService = new SmsServiceController($message, $customer->telephone_number);
                $smsService->sendSms();
                
                Log::info("OTPs sent for forgot password. Customer ID: {$customer->id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Verification codes sent to your email and phone.',
                    'customer_id' => $customer->id,
                    'masked_email' => $this->maskEmail($customer->email),
                    'masked_phone' => $this->maskPhone($customer->telephone_number)
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to send OTPs for forgot password: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification codes. Please try again.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error("Forgot password request error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP for forgot password - Step 2: Verify both OTPs
     */
    public function forgotPasswordVerifyOTP(Request $request)
    {
        try {
            Log::info("Starting forgot password OTP verification.");
            
            // Validate input
            $request->validate([
                'email_otp' => 'required|string|size:6',
                'otp1' => 'required|digits:1',
                'otp2' => 'required|digits:1',
                'otp3' => 'required|digits:1',
                'otp4' => 'required|digits:1',
                'otp5' => 'required|digits:1',
                'otp6' => 'required|digits:1',
            ]);
            
            // Get customer from session
            $customer_id = session('forgot_password_customer_id');
            if (!$customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please start the process again.'
                ], 422);
            }
            
            $customer = CustomerModel::find($customer_id);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session. Please start the process again.'
                ], 422);
            }
            
            // Check if reset window expired
            if (Carbon::now()->gt($customer->password_reset_expiry)) {
                $request->session()->forget('forgot_password_customer_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Reset session expired. Please start over.'
                ], 422);
            }
            
            // Concatenate mobile OTP
            $mobile_otp = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4 . $request->otp5 . $request->otp6;
            
            Log::info("Verifying OTPs for forgot password. Customer ID: {$customer->id}");
            
            // Verify both OTPs
            if ($customer->email_verification_otp !== $request->email_otp || 
                $customer->mobile_verification_otp !== $mobile_otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification codes. Please check and try again.'
                ], 422);
            }
            
            // Check OTP expiration
            if (Carbon::now()->gt($customer->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification codes have expired.'
                ], 422);
            }
            
            // Mark OTP as verified by clearing them and using temp_password as verification flag
            $customer->email_verification_otp = null;
            $customer->mobile_verification_otp = null;
            $customer->otp_expires_at = null;
            $customer->temp_password = 'VERIFIED'; // Use existing temp_password field as verification flag
            $customer->save();
            
            Log::info("OTPs verified successfully for forgot password. Customer ID: {$customer->id}");
            
            return response()->json([
                'success' => true,
                'message' => 'Verification successful. You can now set a new password.',
                'customer_id' => $customer->id
            ]);
            
        } catch (\Exception $e) {
            Log::error("Forgot password OTP verification error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification.'
            ], 500);
        }
    }

    /**
     * Reset password - Step 3: Set new password
     */
    public function forgotPasswordReset(Request $request)
    {
        try {
            Log::info("Starting forgot password reset.");
            
            // Validate input
            $request->validate([
                'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ]);
            
            // Get customer from session
            $customer_id = session('forgot_password_customer_id');
            if (!$customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please start the process again.'
                ], 422);
            }
            
            $customer = CustomerModel::find($customer_id);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session. Please start the process again.'
                ], 422);
            }
            
            // Check if reset window expired
            if (Carbon::now()->gt($customer->password_reset_expiry)) {
                $request->session()->forget('forgot_password_customer_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Reset session expired. Please start over.'
                ], 422);
            }
            
            // Check if OTP was verified (using temp_password as verification flag)
            if ($customer->temp_password !== 'VERIFIED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your OTP first.'
                ], 422);
            }
            
            Log::info("Resetting password for customer ID: {$customer->id}");
            
            // Update password
            $customer->password = Hash::make($request->password);
            $customer->temp_password = null; // Clear verification flag
            $customer->password_reset_expiry = null;
            $customer->save();
            
            // Clear session
            $request->session()->forget('forgot_password_customer_id');
            
            Log::info("Password reset successfully for customer ID: {$customer->id}");
            
            // Send confirmation email
            try {
                $customer->notify(new SendCustomerOTP('Your password has been successfully reset.'));
            } catch (\Exception $e) {
                Log::warning("Failed to send password reset confirmation email: " . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! You can now login with your new password.',
                'redirect' => route('login_get_route')
            ]);
            
        } catch (\Exception $e) {
            Log::error("Forgot password reset error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting password.'
            ], 500);
        }
    }

    /**
     * Resend OTP for forgot password
     */
    public function forgotPasswordResendOTP(Request $request)
    {
        try {
            Log::info("Starting forgot password OTP resend.");
            
            // Get customer from session
            $customer_id = session('forgot_password_customer_id');
            if (!$customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please start the process again.'
                ], 422);
            }
            
            $customer = CustomerModel::find($customer_id);
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session. Please start the process again.'
                ], 422);
            }
            
            // Check if reset window expired
            if (Carbon::now()->gt($customer->password_reset_expiry)) {
                $request->session()->forget('forgot_password_customer_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Reset session expired. Please start over.'
                ], 422);
            }
            
            // Generate new OTPs
            $email_otp = Str::random(6);
            $mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Update customer with new OTPs
            $customer->email_verification_otp = $email_otp;
            $customer->mobile_verification_otp = $mobile_otp;
            $customer->otp_expires_at = Carbon::now()->addMinutes(10);
            $customer->save();
            
            Log::info("New OTPs generated for forgot password resend. Customer ID: {$customer->id}");
            
            // Send new OTPs
            try {
                // Send email OTP
                $customer->notify(new SendCustomerOTP($email_otp));
                
                // Send SMS OTP
                $message = 'New Password Reset Codes for Public Facilities Reservation Portal.' . PHP_EOL . 
                          'Email OTP: ' . $email_otp . PHP_EOL . 
                          'Phone OTP: ' . $mobile_otp . PHP_EOL . 
                          'Valid for 10 minutes. Do not share.';
                $smsService = new SmsServiceController($message, $customer->telephone_number);
                $smsService->sendSms();
                
                Log::info("New OTPs sent for forgot password. Customer ID: {$customer->id}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'New verification codes sent successfully.'
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to resend OTPs for forgot password: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send new verification codes. Please try again.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error("Forgot password OTP resend error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resending codes.'
            ], 500);
        }
    }

    /**
     * Helper method to mask email for privacy
     */
    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];
        
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 4)) . substr($name, -2);
        return $maskedName . '@' . $domain;
    }

    /**
     * Helper method to mask phone number for privacy
     */
    private function maskPhone($phone)
    {
        return substr($phone, 0, 3) . str_repeat('*', max(0, strlen($phone) - 6)) . substr($phone, -3);
    }

}
