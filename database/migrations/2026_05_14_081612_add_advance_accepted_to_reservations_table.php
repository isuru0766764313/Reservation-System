<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvanceAcceptedToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations_table', function (Blueprint $table) {
            $table->boolean('advance_accepted')->nullable()->default(null)->after('advancePaid');
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
            $table->dropColumn('advance_accepted');
        });
    }
}
