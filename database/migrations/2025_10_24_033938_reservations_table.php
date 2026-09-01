<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations_table', function (Blueprint $table)
        {
            $table->id();
            $table->string('ref_code', 20)->unique()->nullable();
            $table->foreignId('customer_id')->constrained('customers_table')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_tel');
            $table->foreignId('hall_id')->constrained('halls_table')->onDelete('cascade');
            $table->string('hall_name');
            $table->date('reservation_date');
            $table->date('advancePaymentDate')->nullable()->default(null);
            $table->date('cancellationExpiryDate')->nullable()->default(null);
            $table->date('rescheduledExpiryDate')->nullable()->default(null);
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('charge', 10, 2);
            $table->decimal('advanceAmount', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->nullable()->default(null);
            $table->decimal('discount_custom', 10, 2)->nullable()->default(null);
            $table->decimal('deposit', 10, 2)->nullable()->default(null);
            $table->enum('reservation_type', ['regular', 'package'])->default('regular');
            $table->foreignId('package_id')->nullable()->constrained('packages_table')->nullOnDelete();
            $table->unsignedTinyInteger('pre_arrange_time')->default(0);
            $table->unsignedTinyInteger('post_arrange_time')->default(0);
            $table->boolean('agree_terms')->default(false);
            $table->enum('status', range(1, 20))->default('1');
            $table->boolean('logged')->default(false);
            $table->boolean('advancePaid')->default(false);
            $table->boolean('user_cancelled')->default(false);
            $table->boolean('re_scheduled')->default(false);
            $table->boolean('accepted')->nullable()->default(null);
            $table->boolean('payment_status')->nullable()->default(null);
            //$table->boolean('closed')->nullable()->default(null);
            //$table->boolean('payment_rejected')->nullable()->default(null);
            $table->boolean('reserved')->nullable()->default(null);
            $table->string('receipt_path')->nullable()->default(null);
            $table->string('clearence_form')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations_table');
    }
}
