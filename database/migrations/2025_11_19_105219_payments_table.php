<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) 
        {
            $table->id();// Primary key
            $table->foreignId('reservation_id')->constrained('reservations_table')->onDelete('cascade');
            $table->string('payment_alias');
            $table->decimal('amount', 10, 2);
            $table->string('receipt_path')->nullable()->default(null);
            $table->enum('status', range(1, 20))->default('1');
            $table->text('remarks')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}