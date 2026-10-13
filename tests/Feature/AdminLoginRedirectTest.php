<?php

namespace Tests\Feature;

use App\Models\AdminModel;
use App\Models\CustomerModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Regression tests for the post-login redirect of the admin sign-in form.
 *
 * Bug: Laravel stores the "intended" URL in a single session key shared by
 * every guard. When an unauthenticated visitor clicked a hall, the auth
 * middleware saved that customer URL (e.g. /customer/halls/3) as
 * "url.intended". A later admin login honoured it via redirect()->intended(),
 * bounced the admin to the customer-only hall page and the customer sign-in
 * panel slid up with "Please sign in to see the hall details." instead of the
 * admin dashboard.
 */
class AdminLoginRedirectTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A verified admin logging in must land on the admin dashboard even when a
     * stale customer URL is still stored as the intended URL.
     */
    public function test_admin_login_ignores_stale_customer_intended_url()
    {
        $admin = $this->createVerifiedAdmin('admin.stale.intended@example.com');

        $response = $this->withSession([
            '_token' => 'test-csrf-token',
            'url.intended' => 'http://localhost/customer/halls/3',
            'auth_redirected' => true,
        ])->post('/admin/login', [
            '_token' => 'test-csrf-token',
            'email' => $admin->email,
            'password' => 'Secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard.route'));

        // The stale cross-guard flags must be cleared.
        $response->assertSessionMissing('url.intended');
        $response->assertSessionMissing('auth_redirected');
    }

    /**
     * A genuine admin-area intended URL is still honoured after login.
     */
    public function test_admin_login_still_honours_admin_intended_url()
    {
        $admin = $this->createVerifiedAdmin('admin.intended@example.com');

        $response = $this->withSession([
            '_token' => 'test-csrf-token',
            'url.intended' => 'http://localhost/admin/calendar',
        ])->post('/admin/login', [
            '_token' => 'test-csrf-token',
            'email' => $admin->email,
            'password' => 'Secret123',
        ]);

        $response->assertRedirect('http://localhost/admin/calendar');
    }

    /**
     * The customer sign-in form must not be hijacked by a stale admin URL.
     */
    public function test_customer_login_ignores_stale_admin_intended_url()
    {
        $customer = CustomerModel::create([
            'profile_title' => 'Mr.',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'customer.intended@example.com',
            'telephone_number' => '0771234567',
            'national_id' => '123456789V',
            'password' => Hash::make('Secret123'),
            'type' => 'individual',
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
        ]);

        $response = $this->withSession([
            'url.intended' => 'http://localhost/admin/dashboard',
        ])->post('/customer/login', [
            'email' => $customer->email,
            'password' => 'Secret123',
        ]);

        $response->assertRedirect(route('load_venues_page'));
        $response->assertSessionMissing('url.intended');
    }

    private function createVerifiedAdmin($email)
    {
        return AdminModel::create([
            'company_name' => 'Test Company',
            'telephone_number' => '0771234567',
            'email' => $email,
            'password' => Hash::make('Secret123'),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
        ]);
    }
}
