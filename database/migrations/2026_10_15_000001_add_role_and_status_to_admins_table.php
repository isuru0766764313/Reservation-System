<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds admin type (super vs organisation) and approval status columns
     * to support the super-admin approval workflow for new admin registrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins_table', function (Blueprint $table) {
            // 'super'    -> the single super admin (registers first, approves others)
            // 'organisation' -> normal admins requiring super admin approval
            $table->string('admin_type', 20)
                ->default('organisation')
                ->after('password');

            // Approval lifecycle driven by the super admin:
            // 'pending'  -> registration submitted, waiting for super admin approval
            // 'approved' -> super admin approved, OTP verification / sign-in allowed
            // 'rejected' -> super admin rejected the registration request
            $table->string('approval_status', 20)
                ->default('pending')
                ->after('admin_type');

            // Timestamp of when the super admin acted on the request (optional, useful for auditing)
            $table->timestamp('approved_at')->nullable()->after('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins_table', function (Blueprint $table) {
            $table->dropColumn(['admin_type', 'approval_status', 'approved_at']);
        });
    }
};
