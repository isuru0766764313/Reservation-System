<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRescheduleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reschedule_requests_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('hall_id');
            // Requested new schedule details (NOT applied to the reservation until admin accepts)
            $table->date('new_date');
            $table->time('new_start_time');
            $table->time('new_end_time');
            $table->unsignedInteger('new_pre_arrange_time')->default(0);
            $table->unsignedInteger('new_post_arrange_time')->default(0);
            // Snapshot of the original schedule so the admin can compare old vs new
            $table->date('old_date');
            $table->time('old_start_time');
            $table->time('old_end_time');
            $table->unsignedInteger('old_pre_arrange_time')->default(0);
            $table->unsignedInteger('old_post_arrange_time')->default(0);
            // 1 = pending, 2 = accepted, 3 = rejected
            $table->tinyInteger('status')->default(1);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('reservation_id')->references('id')->on('reservations_table')->onDelete('cascade');
            $table->index(['reservation_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reschedule_requests_table');
    }
}
