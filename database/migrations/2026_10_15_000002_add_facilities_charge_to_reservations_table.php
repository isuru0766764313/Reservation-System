<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacilitiesChargeToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations_table', function (Blueprint $table) {
            $table->decimal('facilities_charge', 10, 2)->default(0.00)->after('charge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations_table', function (Blueprint $table) {
            $table->dropColumn('facilities_charge');
        });
    }
}
