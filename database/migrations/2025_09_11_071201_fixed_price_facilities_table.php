<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixedPriceFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_price_facilities_table', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls_table')->onDelete('cascade');
            $table->string('name')->nullable()->default(null);
            $table->decimal('charge', 10, 2)->nullable()->default(null);
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
        Schema::dropIfExists('fixed_price_facilities_table');
    }
}
