<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForgotPasswordFieldsToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins_table', function (Blueprint $table) {
            $table->string('temp_password')->nullable()->after('password');
            $table->timestamp('password_reset_expiry')->nullable()->after('otp_expires_at');
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
            $table->dropColumn(['temp_password', 'password_reset_expiry']);
        });
    }
}
