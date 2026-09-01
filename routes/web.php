<?php

use App\Http\Controllers\CustomerController;
use App\Models\CustomerModel;
use App\Models\AdminModel;
use App\Models\HallModel;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SmsServiceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;




Route::get('/send_sms', [SmsServiceController::class, 'sendSms']);




// Main routes

Route::get('/',[Controller::class, 'ShowHomePage'])->name('home_route');

/*****Customer Routes*****/


// Customer Public Routes
Route::prefix('customer')->group(function ()
{
    // Registration
    Route::get('/register', [CustomerController::class, 'ShowRegisterPage'])->name('registration_get_route');
    Route::post('/register', [CustomerController::class, 'Register'])->name('registration_post_route');
    
    // Login
    Route::get('/login', [CustomerController::class, 'ShowLoginPage'])->name('login_get_route');
    Route::post('/login', [CustomerController::class, 'Login'])->name('login_post_route');
    
    
    // OTP Verification
    Route::get('/verify-email', function ()
    {
        if (!session()->has('verify_customer_id'))
        {
            return redirect()->route('login_get_route');
        }
        $customer = CustomerModel::find(session('verify_customer_id'));
        return view('CustomerVerifyEmail',['email' => session('email', $customer->email)]);})->name('customer.verification.notice');
    
    Route::post('/verify-otp', [CustomerController::class, 'verifyOTP'])->name('customer.verification.verify');
    Route::post('/resend-otp', [CustomerController::class, 'resendOTP'])->name('customer.verification.resend');
    
    // Forgot Password Routes (for non-logged in users)
    Route::post('/forgot-password/request', [CustomerController::class, 'forgotPasswordRequest'])->name('customer.forgot.password.request');
    Route::post('/forgot-password/verify-otp', [CustomerController::class, 'forgotPasswordVerifyOTP'])->name('customer.forgot.password.verify.otp');
    Route::post('/forgot-password/reset', [CustomerController::class, 'forgotPasswordReset'])->name('customer.forgot.password.reset');
    Route::post('/forgot-password/resend-otp', [CustomerController::class, 'forgotPasswordResendOTP'])->name('customer.forgot.password.resend.otp');
});

// Protected Routes (Verified Customers Only)
Route::middleware(['auth:customer', 'verified.customer'])->prefix('customer')->group(function ()
{
    Route::get('/dashboard', [CustomerController::class, 'Load_Customer_Dashboard'])->name('load_customer_dashboard');
    Route::put('/update/{customer}', [CustomerController::class, 'udpate_customer_account_details'])->name('customer.details.update');
    Route::get('/findhalls', [CustomerController::class, 'Load_Venues_Page'])->name('load_venues_page');
    Route::get('/halls/{hall}', [HallController::class, 'show'])->name('halls.show');
    // Customer calendar page (shows calendar UI for a hall)
    Route::get('/calendar/{hall_id}', [CustomerController::class, 'showCalendar'])->name('customer.calendar');
    Route::get('/hall/availability/{date}', [HallController::class, 'getUnavailablePeriods']);
    // Returns daily availability summary (used by customer calendar to color days)
    Route::get('/hall/availability-range', [HallController::class, 'getUnavailablePeriodsRange']);

    // Regular booking
    Route::post('/hall/{hall}/reservation/regular', [ReservationController::class, 'reservation_request_sent_regular'])->name('reservation.request.regular.route');
    // Package booking  
    Route::post('/hall/{hall}/reservation/package', [ReservationController::class, 'reservation_request_sent_package'])->name('reservation.request.package.route');

    Route::get('/payment/{encryptedReservation}', [ReservationController::class, 'open_payment_page'])->name('payment.create');
    Route::post('/payment_email/{reservation}', [ReservationController::class, 'make_payment'])->name('payment.submit');
    Route::post('/logout', [CustomerController::class, 'Logout'])->name('logout_route');

    // customer password reset from dashboard
    Route::post('/password/reset-request', [CustomerController::class, 'requestPasswordReset'])->name('customer.password.reset.request');
    Route::post('/password/reset-verify', [CustomerController::class, 'verifyPasswordReset'])->name('customer.password.reset.verify');
    Route::post('/password/reset-resend-otp', [CustomerController::class, 'resendPasswordResetOTP'])->name('customer.password.reset.resend');

	    // cancellation of reservation by customer
	    Route::post('/reservation/cancel/{reservation}', [ReservationController::class, 'cancel_reservation_by_customer'])->name('customer.reservation.cancel');
    Route::patch('/reservation/reschedule/{reservation}', [ReservationController::class, 're_schedule_reservation_by_customer'])->name('customer.reservation.reschedule');


    Route::get('/test', [CustomerController::class, 'ShowTestPage'])->name('test_route');

});


/***********************************************************Admin routes***************************************************************************************************************************/


// admin Public Routes

Route::prefix('admin')->group(function()
{
    // Registration
    Route::get('/register', [AdminController::class, 'ShowRegisterPage'])->name('admin.registration.get.route');
    Route::post('/register', [AdminController::class, 'Register'])->name('admin.registration.post.route');
    // Login
    Route::get('/login', [AdminController::class, 'ShowLoginPage'])->name('admin.login.get.route');
    Route::post('/login', [AdminController::class, 'Login'])->name('admin.login.post.route');

    // OTP Verification
    Route::get('/verify-email', function ()
    {
        if (!session()->has('verify_admin_id'))
        {
            return redirect()->route('admin.login.get.route');
        }
        $admin = AdminModel::find(session('verify_admin_id'));
        return view('AdminVerifyEmail',['email' => session('email', $admin->email)]);})->name('admin.verification.notice');
    
    Route::post('/verify-otp', [AdminController::class, 'verifyOTP'])->name('admin.verification.verify');
    Route::post('/resend-otp', [AdminController::class, 'resendOTP'])->name('admin.verification.resend');
});

// Protected Routes (Verified Admins Only)
Route::middleware(['auth:admin', 'verified.admin'])->prefix('admin')->group(function ()
{
    // Consolidated admin dashboard route
    Route::get('/dashboard', [AdminDashboardController::class, 'admin_dashboard_fetch_data'])->name('admin.dashboard.route');    

    // Admin calendar routes
    Route::get('/calendar', [AdminController::class, 'showFullCalendar'])->name('admin.calendar.full');
    Route::get('/calendar/events', [AdminController::class, 'getCalendarEvents'])->name('admin.calendar.events');

    // enter bank detailes and save it
    Route::post('/insert/bank',[AdminDashboardController::class,'insert_bank'])->name('admin.updateBank');

    // insert hall details page opening
    Route::get('/creat_new_hall',[HallController::class, 'open_create_new_hall_page'])->name('open.insert.hall.data.page');
    //Route::get('/creat_new_hall', function () {$admin = Auth::guard('admin')->user();return view('InsertData', compact('admin')); })->name('open.insert.hall.data.page');
    // submit hall details
    Route::post('/creat_new_hall', [HallController::class, 'InsertHallData'])->name('insert.hall.data.route');
    // open hall update page
    Route::get('/halls/update/{hall}',[HallController::class,'open_hall_update_page'])->name('open.hall.update.page');

    // open terms and conditions
    Route::get('/hall/{hall}/terms', function (HallModel $hall) 
    {
        // Check if the hall has a PDF file
        if (!$hall->pdf){
            abort(404, 'File not found.');
        }

        // Construct the full path to the file within the storage disk
        $filePath = storage_path('app/public/' . $hall->pdf);

        // Check if the file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server.');
        }

        // Send the file to the browser for inline viewing
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    })->name('view.hall.terms');

    // open Clearence Form
    Route::get('/hall/{hall}/clerence-form', function (HallModel $hall) 
    {
        // Check if the hall has a PDF file
        if (!$hall->clearence_form){
            abort(404, 'File not found.');
        }

        // Construct the full path to the file within the storage disk
        $filePath = storage_path('app/public/' . $hall->clearence_form);

        // Check if the file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server.');
        }

        // Send the file to the browser for inline viewing
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    })->name('view.hall.clearence');


    // submit updated hall details
    Route::put('/halls/update/{hall}', [HallController::class, 'hall_update'])->name('halls.update');
    // Package Routes
    Route::post('/packages/store/{hall}', [HallController::class, 'packages_store'])->name('packages.store');
    Route::put('/packages/update/{hall}', [HallController::class, 'update'])->name('packages.update');
    Route::delete('/packages/delete/{package}', [HallController::class, 'destroy'])->name('packages.destroy');
    // delete hall details
    Route::delete('/halls/delete/{hall}', [AdminDashboardController::class, 'DeactivateHall'])->name('deactivate.hall');
    // check for ongoing (not yet passed) reservations of a hall
    Route::get('/halls/{hall}/ongoing-reservations', [AdminDashboardController::class, 'checkOngoingReservations'])->name('deactivate.hall.check');
    // reject or accept customer reservations
    Route::patch('/reservations/{reservation}/accept', [AdminDashboardController::class, 'accept_reservation_request'])->name('admin.reservations.accept');
    Route::patch('/reservations/{reservation}/reject', [AdminDashboardController::class, 'reject_reservation_request'])->name('admin.reservations.reject');
    // view the payment slip by from "PaymentConfirmation" email
    Route::get('/view-slip/{reservation}',[AdminDashboardController::class,'viewSlip'])->name('admin.view.slip');
    // accept the payment slip or not
    Route::patch('/slip/{reservation}/accept',[ReservationController::class,'acceptPayment'])->name('admin.slip.accept');
    Route::patch('/slip/{reservation}/reject',[ReservationController::class,'rejectPayment'])->name('admin.slip.reject');
    // accept/reject a single payment slip
    Route::patch('/slip/payment/{payment}/accept',[ReservationController::class,'acceptSinglePayment'])->name('admin.payment.accept');
    Route::patch('/slip/payment/{payment}/reject',[ReservationController::class,'rejectSinglePayment'])->name('admin.payment.reject');
    // reject the reservation itself (not payments)
    Route::patch('/reservations/{reservation}/reject-reservation',[ReservationController::class,'rejectReservation'])->name('admin.reservation.reject');
    // accept advance payment only (unlock remaining payment for customer)
    Route::patch('/slip/{reservation}/accept-advance',[ReservationController::class,'acceptAdvancePayment'])->name('admin.slip.acceptAdvance');
    Route::post('/logout', [AdminController::class, 'Logout'])->name('admin.logout.route');
});
