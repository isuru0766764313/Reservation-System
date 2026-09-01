<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages_table', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls_table')->onDelete('cascade');
            $table->string('name')->nullable()->default(null);
            $table->decimal('price', 10, 2)->nullable()->default(null);
            $table->decimal('hourly_rate', 10, 2)->nullable()->default(null);
            $table->decimal('discount', 10, 2)->nullable()->default(null);
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('maximum_hours', 10, 2)->nullable()->default(null);
            $table->json('fixed_price_facilities')->nullable();
            $table->json('unit_price_facilities')->nullable();
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
        Schema::dropIfExists('packages_table');
    }
}
