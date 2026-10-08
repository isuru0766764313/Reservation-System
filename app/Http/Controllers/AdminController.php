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
        return view('home');
    }

    function Register(Request $request)
    {
        // If this email is already registered, bounce the visitor to the sign-in
        // panel with a friendly prompt instead of a generic validation redirect.
        if ($request->filled('email') && AdminModel::where('email', $request->email)->exists()) {
            return redirect()
                ->route('home_route')
                ->with('admin_registration_email_exists', true)
                ->withInput($request->only('email'));
        }

        $request->validate(['company_name' => 'required|max:50', 'telephone_number' => 'required', 'email' => 'required|email|unique:admins_table', 'password' => 'required', 'confirm_password' => 'required']);

        $email_otp = Str::random(6); // generate randome otp for email
        $mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);// generate randome otp for mobile
        // add row of data
        $data['company_name'] = $request->company_name;
        $data['telephone_number'] = $request->telephone_number;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);// Hash::make for encryption
        $data['email_verification_otp'] = $email_otp;
        $data['mobile_verification_otp'] = $mobile_otp;
        $data['otp_expires_at'] = Carbon::now()->addMinutes(10);

        $admin = AdminModel::create($data); // add raw of data to table

        // lets check creation of $admin is successfull or not
        if (!$admin) {
            //if  there is no $admin redirects to "home_route" with a error massage.
            return redirect(route('admin.registration.get.route'))->with('error_key_1', 'Registration is failed. Pls try again');
        } else {
            // send sms otp to admin at registration..
            $message = 'Welcome to Public Facilities Reservation System. Your OTP for registration is : ' . PHP_EOL . '"' . $mobile_otp . '" Do not share this code with others.';
            $recipients = ($request->telephone_number);
            $smsService = new SmsServiceController($message, $recipients);
            $smsService->sendSms();

            // Send OTP via email
            $admin->notify(new SendAdminOTP($data['email_verification_otp']));

            // Store admin ID in session for verification
            $request->session()->put('verify_admin_id', $admin->id);

            // Redirect to verification notice route
            return redirect()->route('admin.verification.notice')
                ->with([
                    'email' => $admin->email,
                    'telephone_number' => $admin->telephone_number
                ]);
        }
    }


    public function verifyOTP(Request $request)
    {
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

        $admin = AdminModel::find(session('verify_admin_id'));

        if (!$request->session()->has('verify_admin_id')) {
            return redirect()->route('home_route')->with('error', 'Session expired');
        }

        if (!$admin) {
            return redirect()->route('admin.registration.get.route')->with('error', 'Session expired. Please register again.');
        }

        if ($admin->email_verification_otp === $email_otp && $admin->mobile_verification_otp === $mobile_otp && Carbon::now()->lt($admin->otp_expires_at)) {

            $admin->update([
                'email_verified_at' => now(),
                'mobile_verified_at' => now(),
                'email_verification_otp' => null,
                'mobile_verification_otp' => null,
                'otp_expires_at' => null
            ]);

            $request->session()->forget('verify_admin_id');
            Auth::guard('admin')->login($admin);

            // Redirect to your specified route after verification
            return redirect()->route('home_route')->with('success', 'Email verified successfully!');
        } else {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

    }

    public function resendOTP(Request $request)
    {
        $admin = AdminModel::find(session('verify_admin_id'));

        if (!$admin) {
            return redirect()->route('admin.registration.get.route')->with('error', 'Session expired. Please register again.');
        }

        $new_email_otp = Str::random(6); // generate randome otp for email
        $new_mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);// generate randome otp for mobile

        $admin->update([
            'email_verification_otp' => $new_email_otp,
            'mobile_verification_otp' => $new_mobile_otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        //  re-send sms otp to admin at registration..
        $message = 'Welcome back to Public Facilities Reservation System. Please verify your account before sign in' . PHP_EOL . '"' . $new_mobile_otp . '" is your OTP code. Do not share with others.';
        $recipients = ($admin->telephone_number);
        $smsService = new SmsServiceController($message, $recipients);
        $smsService->sendSms();

        $admin->notify(new SendAdminOTP($new_email_otp));

        return back()->with('status', 'New OTP has been sent to your email and telephone number!');
    }



    function ShowLoginPage()
    {
        return view('home');
    }



    function Login(Request $request)
    {
        try {
            // Check if customer is logged in and logout to prevent cross-session conflicts
            if (Auth::guard('customer')->check()) {
                Auth::guard('customer')->logout();
            }

            // Check if admin is already logged in elsewhere
            if (Auth::guard('admin')->check()) {
                $current_admin = Auth::guard('admin')->user();
                // If trying to login as the same admin, just redirect
                if ($current_admin->email === $request->email) {
                    return redirect()->intended(route('admin.dashboard.route'))->with('success', 'You are already logged in!');
                } else {
                    // Different admin trying to login - invalidate current session
                    Auth::guard('admin')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
            }
            // Validate CSRF token
            $csrfToken = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');
            if (!hash_equals((string) session()->token(), (string) $csrfToken)) {
                return redirect()->back()->with('admin_error_key_2', 'CSRF token mismatch. Please try again.')->withInput();
            }
            // Validate inputs
            $request->validate(['email' => 'required|email', 'password' => 'required']);

            // Check if the email exists in the admins_table
            $adminExists = AdminModel::where('email', $request->email)->exists();

            if (!$adminExists) {
                return redirect()
                    ->back()
                    ->with('admin_error_key_2', 'Email is not available. Pls sign up')
                    ->withInput($request->only('email'));
            }

            $credentials = $request->only('email', 'password');

            if (Auth::guard('admin')->attempt($credentials)) {
                // Regenerate session to prevent fixation attacks
                $request->session()->regenerate();
                $admin = Auth::guard('admin')->user();
                // Check email verification status
                if (!$admin->email_verified_at) {
                    Auth::guard('admin')->logout();
                    $request->session()->put('verify_admin_id', $admin->id);
                    // Generate new OTPs
                    $new_email_otp = Str::random(6);
                    $new_mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    // Update admin with new OTPs
                    $admin->update(['email_verification_otp' => $new_email_otp, 'mobile_verification_otp' => $new_mobile_otp, 'otp_expires_at' => Carbon::now()->addMinutes(10)]);

                    // Send SMS OTP
                    $message = 'Welcome back to Public Facilities Reservation System. Please verify your account before sign in' . PHP_EOL . '"' . $new_mobile_otp . '" is your one-time entry code. Do not share with others.';
                    $recipients = ($admin->telephone_number);
                    $smsService = new SmsServiceController($message, $recipients);
                    $smsService->sendSms();

                    // Send email OTP
                    $admin->notify(new SendAdminOTP($new_email_otp));

                    // Redirect to verification notice
                    return redirect()->route('admin.verification.notice')
                        ->with([
                            'email' => $admin->email,
                            'telephone_number' => $admin->telephone_number
                        ]);
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
                ->whereHas('hall', function ($q) use ($adminId) {
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

                // Determine status using the current centralized status system (1-8)
                $statusMeta = \App\Http\Controllers\ReservationController::getReservationStatusMeta(
                    (int) $reservation->status
                );

                $events[] = [
                    'title' => $reservation->hall->name . ' - ' . $statusMeta['label'],
                    'start' => $reservation->reservation_date . 'T' . $reservation->start_time,
                    'end' => $reservation->reservation_date . 'T' . $reservation->end_time,
                    'backgroundColor' => $hallColor,
                    'borderColor' => $statusMeta['color'],
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'reservation_id' => $reservation->id,
                        'hall_name' => $reservation->hall->name,
                        'customer_name' => $reservation->customer_name,
                        'customer_email' => $reservation->customer->email ?? null,
                        'customer_phone' => $reservation->customer->telephone_number ?? null,
                        'reservation_date' => \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d'),
                        'time_slot' => $reservation->start_time . ' - ' . $reservation->end_time,
                        'status' => $statusMeta['label'],
                        'status_id' => (int) $reservation->status,
                        'status_color' => $statusMeta['color'],
                        'status_text_color' => $statusMeta['text_color']
                    ]
                ];
            }

            return response()->json($events);

        } catch (\Exception $e) {
            \Log::error('Admin calendar events error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load events'], 500);
        }
    }

    // ========== FORGOT PASSWORD FUNCTIONALITY FOR NON-LOGGED-IN ADMINS ==========

    /**
     * Handle forgot password request - Step 1: Validate email and phone
     */
    public function forgotPasswordRequest(Request $request)
    {
        try {
            Log::info("Starting admin forgot password request.");

            // Validate input
            $request->validate([
                'email' => 'required|email|exists:admins_table,email',
                'telephone_number' => 'required|string',
            ]);

            // Find admin by email
            $admin = AdminModel::where('email', $request->email)->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Verify phone number matches
            if ($admin->telephone_number !== $request->telephone_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'The phone number does not match our records.'
                ], 422);
            }

            Log::info("Admin found for forgot password: ID = {$admin->id}, Email = {$admin->email}");

            // Generate OTPs
            $email_otp = Str::random(6);
            $mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTPs and session info
            $admin->email_verification_otp = $email_otp;
            $admin->mobile_verification_otp = $mobile_otp;
            $admin->otp_expires_at = Carbon::now()->addMinutes(10);
            $admin->password_reset_expiry = Carbon::now()->addMinutes(30);
            $admin->save();

            // Store admin ID in session for verification
            $request->session()->put('forgot_password_admin_id', $admin->id);

            Log::info("OTPs generated for admin forgot password. Admin ID: {$admin->id}");

            // Send OTPs
            try {
                // Send email OTP
                $admin->notify(new SendAdminOTP($email_otp));

                // Send SMS OTP
                $message = 'Password Reset Request for Public Facilities Reservation System.' . PHP_EOL .
                    'Phone OTP: ' . $mobile_otp . PHP_EOL .
                    'Valid for 10 minutes. Do not share.';
                $smsService = new SmsServiceController($message, $admin->telephone_number);
                $smsService->sendSms();

                Log::info("OTPs sent for admin forgot password. Admin ID: {$admin->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Verification codes sent to your email and phone.',
                    'admin_id' => $admin->id,
                    'masked_email' => $this->maskEmail($admin->email),
                    'masked_phone' => $this->maskPhone($admin->telephone_number)
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to send OTPs for admin forgot password: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification codes. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error("Admin forgot password request error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP for admin forgot password - Step 2: Verify both OTPs
     */
    public function forgotPasswordVerifyOTP(Request $request)
    {
        try {
            Log::info("Starting admin forgot password OTP verification.");

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

            // Get admin from session
            $admin_id = session('forgot_password_admin_id');
            if (!$admin_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please start the process again.'
                ], 422);
            }

            $admin = AdminModel::find($admin_id);
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session. Please start the process again.'
                ], 422);
            }

            // Check if reset window expired
            if (Carbon::now()->gt($admin->password_reset_expiry)) {
                $request->session()->forget('forgot_password_admin_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Reset session expired. Please start over.'
                ], 422);
            }

            // Concatenate mobile OTP
            $mobile_otp = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4 . $request->otp5 . $request->otp6;

            Log::info("Verifying OTPs for admin forgot password. Admin ID: {$admin->id}");

            // Verify both OTPs
            if (
                $admin->email_verification_otp !== $request->email_otp ||
                $admin->mobile_verification_otp !== $mobile_otp
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification codes. Please check and try again.'
                ], 422);
            }

            // Check OTP expiration
            if (Carbon::now()->gt($admin->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification codes have expired.'
                ], 422);
            }

            // Mark OTP as verified by clearing them and using temp_password as verification flag
            $admin->email_verification_otp = null;
            $admin->mobile_verification_otp = null;
            $admin->otp_expires_at = null;
            $admin->temp_password = 'VERIFIED'; // Use temp_password field as verification flag
            $admin->save();

            Log::info("OTPs verified successfully for admin forgot password. Admin ID: {$admin->id}");

            return response()->json([
                'success' => true,
                'message' => 'Verification successful. You can now set a new password.',
                'admin_id' => $admin->id
            ]);

        } catch (\Exception $e) {
            Log::error("Admin forgot password OTP verification error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification.'
            ], 500);
        }
    }

    /**
     * Reset admin password - Step 3: Set new password
     */
    public function forgotPasswordReset(Request $request)
    {
        try {
            Log::info("Starting admin forgot password reset.");

            // Validate input
            $request->validate([
                'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ]);

            // Get admin from session
            $admin_id = session('forgot_password_admin_id');
            if (!$admin_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please start the process again.'
                ], 422);
            }

            $admin = AdminModel::find($admin_id);
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session. Please start the process again.'
                ], 422);
            }

            // Check if reset window expired
            if (Carbon::now()->gt($admin->password_reset_expiry)) {
                $request->session()->forget('forgot_password_admin_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Reset session expired. Please start over.'
                ], 422);
            }

            // Check if OTP was verified (using temp_password as verification flag)
            if ($admin->temp_password !== 'VERIFIED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your OTP first.'
                ], 422);
            }

            Log::info("Resetting password for admin ID: {$admin->id}");

            // Update password
            $admin->password = Hash::make($request->password);
            $admin->temp_password = null; // Clear verification flag
            $admin->password_reset_expiry = null;
            $admin->save();

            // Clear session
            $request->session()->forget('forgot_password_admin_id');

            Log::info("Password reset successfully for admin ID: {$admin->id}");

            // Send confirmation email
            try {
                $admin->notify(new SendAdminOTP('Your admin password has been successfully reset.'));
            } catch (\Exception $e) {
                Log::warning("Failed to send admin password reset confirmation email: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! You can now login with your new password.',
                'redirect' => route('home_route')
            ]);

        } catch (\Exception $e) {
            Log::error("Admin forgot password reset error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting password.'
            ], 500);
        }
    }

    /**
     * Resend OTP for admin forgot password
     */
    public function forgotPasswordResendOTP(Request $request)
    {
        try {
            Log::info("Starting admin forgot password OTP resend.");

            // Get admin from session
            $admin_id = session('forgot_password_admin_id');
            if (!$admin_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please start the process again.'
                ], 422);
            }

            $admin = AdminModel::find($admin_id);
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session. Please start the process again.'
                ], 422);
            }

            // Check if reset window expired
            if (Carbon::now()->gt($admin->password_reset_expiry)) {
                $request->session()->forget('forgot_password_admin_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Reset session expired. Please start over.'
                ], 422);
            }

            // Generate new OTPs
            $email_otp = Str::random(6);
            $mobile_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Update admin with new OTPs
            $admin->email_verification_otp = $email_otp;
            $admin->mobile_verification_otp = $mobile_otp;
            $admin->otp_expires_at = Carbon::now()->addMinutes(10);
            $admin->save();

            Log::info("New OTPs generated for admin forgot password resend. Admin ID: {$admin->id}");

            // Send new OTPs
            try {
                // Send email OTP
                $admin->notify(new SendAdminOTP($email_otp));

                // Send SMS OTP
                $message = 'New Password Reset Codes for Public Facilities Reservation System.' . PHP_EOL .
                    'Phone OTP: ' . $mobile_otp . PHP_EOL .
                    'Valid for 10 minutes. Do not share.';
                $smsService = new SmsServiceController($message, $admin->telephone_number);
                $smsService->sendSms();

                Log::info("New OTPs sent for admin forgot password. Admin ID: {$admin->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'New verification codes sent successfully.'
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to resend OTPs for admin forgot password: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send new verification codes. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error("Admin forgot password OTP resend error: " . $e->getMessage());
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
