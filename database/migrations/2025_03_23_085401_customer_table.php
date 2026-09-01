<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers_table', function (Blueprint $table) 
        {            
            $table->id(); // Auto-incrementing primary key (BIGINT)
            $table->string('profile_title')->nullable();
            $table->string('first_name'); // VARCHAR(255)
            $table->string('last_name'); // VARCHAR(255)
            $table->string('email')->unique(); // Unique email constraint
            $table->string('telephone_number'); // String to store "+94XXXXXXXXX"
            $table->string('national_id')->unique(); // National ID as unique string
            $table->string('password'); // Will store hashed password
            $table->string('temp_password')->nullable();
            $table->string('type');
            $table->timestamp('email_verified_at')->nullable();// for sign up
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('email_verification_otp')->nullable();
            $table->string('mobile_verification_otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('password_reset_expiry')->nullable();
            $table->rememberToken();
            $table->timestamps(); // created_at & updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers_table');// drop if excist a table called "customer_table"
    }
}
