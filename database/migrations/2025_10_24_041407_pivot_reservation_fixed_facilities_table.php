<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservation_fixed_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations_table')->onDelete('cascade');
            $table->foreignId('fixed_facility_id')->constrained('fixed_price_facilities_table');
            $table->timestamps();

            // Use a shorter unique constraint name
            $table->unique(['reservation_id', 'fixed_facility_id'], 'res_fixed_facilities_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation_fixed_facilities');
    }
};