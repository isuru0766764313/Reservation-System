<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halls_table',function(Blueprint $table)
        {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins_table')->onDelete('cascade');

            // Core Attributes
            $table->string('name'); //hall name
            $table->string('type'); // hall type is always a one type
            $table->decimal('price', 10, 2); // 99999999.99 max
            $table->decimal('discount', 10, 2)->nullable()->default(null);
            $table->decimal('cancellation_fee', 10, 2)->nullable()->default(null);
            $table->decimal('deposit', 10, 2)->nullable()->default(null);// refundable
            $table->integer('capacity');
            $table->integer('max_pre_arrange_hours')->default(5);
            $table->integer('max_post_arrange_hours')->default(5);            
            $table->text('description');
            $table->string('address');
            $table->string('province');
            $table->string('district');
            $table->string('area');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->json('images')->nullable();
            $table->string('pdf')->nullable(); // terms and condition pdf
            $table->string('clearence_form')->nullable(); //  clearence form pdf
            $table->boolean('available')->nullable()->default(true);
            $table->string('booking_method')->default('both');
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
        Schema::dropIfExists('halls_table');
    }
}
