<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins_table', function (Blueprint $table) 
        {            
            $table->id(); // Auto-incrementing primary key (BIGINT)
            $table->string('company_name'); // VARCHAR(255)            
            $table->string('telephone_number'); // String to store "+94XXXXXXXXX"
            $table->string('email')->unique(); // Unique email constraint
            $table->string('password'); // Will store hashed password
            $table->string('bank')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
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
        Schema::dropIfExists('admins_table');
    }
}
