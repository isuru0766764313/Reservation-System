<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalancePaymentDateToReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations_table', function (Blueprint $table) {
            // Deadline by which the customer must pay the balance payment.
            // Must be on or before one week prior to the reservation date.
            $table->date('balancePaymentDate')->nullable()->default(null)->after('advancePaymentDate');
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
            $table->dropColumn('balancePaymentDate');
        });
    }
}
