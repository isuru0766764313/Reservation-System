<?php

namespace App\Http\Controllers;


use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Registered;
use App\Models\CustomerModel;   
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Services\ESMSWS;//sms api
use App\Http\Controllers\SmsServiceController;
use App\Notifications\SendAdminOTP;

class AdminController extends Controller
{    
    function ShowRegisterPage()
    {
        return view ('home');
    }
    
    function Register(Request $request)
    {
        $request->validate(['company_name'=>'required|max:50', 'telephone_number'=> 'required','email'=> 'required|email|unique:admins_table','password'=>'required','confirm_password'=>'required']);
        $otp = Str::random(6); // generate randome otp
        // add row of data
        $data['company_name']=$request->company_name;
        $data['telephone_number']=$request->telephone_number;
        $data['email']=$request->email;
        $data['password']= Hash::make($request->password);// Hash::make for encryption
        $data['verification_otp'] = $otp;
        $data['otp_expires_at'] = Carbon::now()->addMinutes(10);

        $admin = AdminModel::create($data); // add raw of data to table

        // lets check creation of $customer is successfull or not
        if(!$admin)
        {
            //if  there is no $customer redirects to "home_route" with a error massage.
            return redirect(route('admin.registration.get.route'))->with('error_key_1','Registration is failed. Pls try again');
        }
        else
        {
            // send sms otp to customer at registration..
            $message = 'Welcome to Public Facilities Reservation Portal.'. PHP_EOL .'"' . $otp . '" is your one-time entry otp code. Do not share with others.';
            $recipients = ($request->telephone_number);
            $smsService = new SmsServiceController($message,$recipients);
            $smsService->sendSms();

            // Send OTP via email
            $admin->notify(new SendAdminOTP($data['verification_otp']));

            // Store customer ID in session for verification
            $request->session()->put('verify_admin_id', $admin->id);

            // Redirect to verification notice route
            return redirect()->route('admin.verification.notice')->with('email', $admin->email);
        }
    }


    public function verifyOTP(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        
        $admin = AdminModel::find(session('verify_admin_id'));

        if (!$request->session()->has('verify_admin_id'))
        {
            return redirect()->route('home_route')->with('error', 'Session expired');
        }
        
        if (!$admin)
        {
            return redirect()->route('admin.registration.get.route')->with('error', 'Session expired. Please register again.');
        }
        
        if ($admin->verification_otp === $request->otp && Carbon::now()->lt($admin->otp_expires_at))
        {
            
            $admin->update([
                'email_verified_at' => now(),
                'verification_otp' => null,
                'otp_expires_at' => null
            ]);
            
            $request->session()->forget('verify_admin_id');
            Auth::guard('admin')->login($admin);
            
            // Redirect to your specified route after verification
            return redirect()->route('home_route')->with('success', 'Email verified successfully!');
        }else
        {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }       
        
    }

    public function resendOTP(Request $request)
    {
        $admin = AdminModel::find(session('verify_admin_id'));
        
        if (!$admin) {
            return redirect()->route('admin.registration.get.route')->with('error', 'Session expired. Please register again.');
        }
        
        $newOtp = Str::random(6);
        $admin->update([
            'verification_otp' => $newOtp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        //  re-send sms otp to customer at registration..
            $message = 'Welcome back to Public Facilities Reservation Portal. Please verify your account before sign in'. PHP_EOL .'"' . $newOtp . '" is your one-time entry code. Do not share with others.';
            $recipients = ($request->telephone_number);
            $smsService = new SmsServiceController($message,$recipients);
            $smsService->sendSms();
        
        $admin->notify(new SendAdminOTP($newOtp));
        
        return back()->with('status', 'New OTP has been sent to your email!');
    }



    function ShowLoginPage()
    {
        return view('home');
    }



    function Login(Request $request)
    {
        try
        {
            // Check if customer is logged in and logout to prevent cross-session conflicts
            if (Auth::guard('customer')->check())
            {
                Auth::guard('customer')->logout();
            }

            // Check if admin is already logged in elsewhere
            if (Auth::guard('admin')->check())
            {
                $current_admin = Auth::guard('admin')->user();
                // If trying to login as the same admin, just redirect
                if ($current_admin->email === $request->email)
                {
                    return redirect()->intended(route('admin.dashboard.route'))->with('success', 'You are already logged in!');
                }
                else
                {
                    // Different admin trying to login - invalidate current session
                    Auth::guard('admin')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }
            // Validate CSRF token
            $csrfToken = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');
            if (!hash_equals((string) session()->token(), (string) $csrfToken))
            {
                return redirect()->back()->with('admin_error_key_2', 'CSRF token mismatch. Please try again.')->withInput();
            }
            // Validate inputs
            $request->validate(['email' => 'required|email','password' => 'required']);

            // Check if the email exists in the admins_table
            $adminExists = AdminModel::where('email', $request->email)->exists();

            if (!$adminExists) {
                return redirect()
                    ->back()
                    ->with('admin_error_key_2', 'Email is not available. Pls sign up')
                    ->withInput($request->only('email'));
            }

            $credentials = $request->only('email', 'password');

            if (Auth::guard('admin')->attempt($credentials))
            {
                // Regenerate session to prevent fixation attacks
                $request->session()->regenerate();
                $admin = Auth::guard('admin')->user();
                // Check email verification status
                if (!$admin->email_verified_at)
                {
                    Auth::guard('admin')->logout();
                    $request->session()->put('verify_admin_id', $admin->id);
                    // Generate new OTPs
                    $new_email_otp = Str::random(6);
                    $new_mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    // Update admin with new OTPs
                    $admin->update(['email_verification_otp' => $new_email_otp,'mobile_verification_otp' => $new_mobile_otp,'otp_expires_at' => Carbon::now()->addMinutes(10)]);

                    // Send SMS OTP
                    $message = 'Welcome back to Public Facilities Reservation Portal. Please verify your account before sign in' . PHP_EOL . '"' . $new_mobile_otp . '" is your one-time entry code. Do not share with others.';
                    $recipients = ($admin->telephone_number);
                    $smsService = new SmsServiceController($message, $recipients);
                    $smsService->sendSms();
                
                // Send email OTP
                $admin->notify(new SendAdminOTP($new_email_otp));
                
                // Redirect to verification notice
                return redirect()->route('admin.verification.notice');
            } else {
                return redirect()->intended(route('admin.dashboard.route'))
                    ->with('success', 'Login successful!');
            }
        } else {
            return redirect()
                ->back()
                ->with('admin_error_key_2', 'Your login details are incorrect. Please try again')
                ->withInput($request->only('email'));
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()
            ->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        \Log::error('Admin login error: ' . $e->getMessage());
        return redirect()
            ->back()
            ->with('admin_error_key_2', 'An error occurred during login. Please try again.');
    }
}

    function Logout(Request $request)
    {
        // Logout only the admin guard
        Auth::guard('admin')->logout();

        // Remove customer-specific session data
        $request->session()->forget('verify_admin_id');

        // Invalidate only admin's session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect(route('home_route'));
    }

    /**
     * Show full calendar view page for admin
     */
    public function showFullCalendar()
    {
        $admin = Auth::guard('admin')->user();
        $halls = \App\Models\HallModel::where('admin_id', $admin->id)->get();
        
        return view('admin-calendar', compact('admin', 'halls'));
    }

    /**
     * Get calendar events (reservations) for admin's halls
     */
    public function getCalendarEvents(Request $request)
    {
        try {
            $request->validate([
                'admin_id' => 'required|exists:admins_table,id',
                'start' => 'required|date',
                'end' => 'required|date',
                'hall_id' => 'sometimes|exists:halls_table,id'
            ]);

            $adminId = $request->admin_id;
            $start = $request->start;
            $end = $request->end;

            // Build query for reservations
            $query = \App\Models\ReservationModel::with(['hall', 'customer'])
                ->whereHas('hall', function($q) use ($adminId) {
                    $q->where('admin_id', $adminId);
                })
                ->whereBetween('reservation_date', [$start, $end]);

            // Filter by specific hall if provided
            if ($request->has('hall_id') && $request->hall_id !== 'all') {
                $query->where('hall_id', $request->hall_id);
            }

            $reservations = $query->get();

            // Format events for FullCalendar
            $events = [];
            foreach ($reservations as $reservation) {
                // Generate color based on hall (consistent color per hall)
                $hallColor = '#' . substr(md5($reservation->hall_id), 0, 6);
                
                // Determine status
                if ($reservation->reserved) {
                    $status = 'Confirmed';
                    $statusClass = 'success';
                    $borderColor = '#28a745';
                } elseif ($reservation->accepted === true) {
                    $status = 'Accepted';
                    $statusClass = 'info';
                    $borderColor = '#17a2b8';
                } elseif ($reservation->accepted === false || $reservation->reserved === false) {
                    $status = 'Rejected';
                    $statusClass = 'danger';
                    $borderColor = '#dc3545';
                } else {
                    $status = 'Pending';
                    $statusClass = 'warning';
                    $borderColor = '#ffc107';
                }

                $events[] = [
                    'title' => $reservation->hall->name . ' - ' . $status,
                    'start' => $reservation->reservation_date . 'T' . $reservation->start_time,
                    'end' => $reservation->reservation_date . 'T' . $reservation->end_time,
                    'backgroundColor' => $hallColor,
                    'borderColor' => $borderColor,
                    'extendedProps' => [
                        'reservation_id' => $reservation->id,
                        'hall_name' => $reservation->hall->name,
                        'customer_name' => $reservation->customer_name,
                        'customer_email' => $reservation->customer->email ?? null,
                        'customer_phone' => $reservation->customer->telephone_number ?? null,
                        'reservation_date' => \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d'),
                        'time_slot' => $reservation->start_time . ' - ' . $reservation->end_time,
                        'status' => $status,
                        'status_class' => $statusClass
                    ]
                ];
            }

            return response()->json($events);

        } catch (\Exception $e) {
            \Log::error('Admin calendar events error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load events'], 500);
        }
    }
    
}
