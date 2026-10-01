<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDualOtpVerificationToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds email + mobile OTP verification columns to admins_table,
     * mirroring the customer dual-verification schema so admins must
     * verify BOTH email and mobile via OTP.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins_table', function (Blueprint $table) {
            $table->timestamp('mobile_verified_at')->nullable()->after('email_verified_at');
            $table->string('email_verification_otp')->nullable()->after('mobile_verified_at');
            $table->string('mobile_verification_otp')->nullable()->after('email_verification_otp');
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
            $table->dropColumn([
                'mobile_verified_at',
                'email_verification_otp',
                'mobile_verification_otp',
            ]);
        });
    }
}
