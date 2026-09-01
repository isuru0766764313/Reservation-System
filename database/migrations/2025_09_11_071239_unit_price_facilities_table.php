<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UnitPriceFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_price_facilities_table', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls_table')->onDelete('cascade');
            $table->string('name')->nullable()->default(null);
            $table->decimal('charge', 10, 2)->nullable()->default(null); // per unit
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
        Schema::dropIfExists('unit_price_facilities_table');
    }
}
