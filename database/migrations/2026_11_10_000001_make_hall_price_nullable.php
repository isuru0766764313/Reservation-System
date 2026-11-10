<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeHallPriceNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * Allow package-only halls to be created without a flat hourly price.
     * Application-level validation still requires `price` for
     * regular/both booking methods.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE halls_table MODIFY price DECIMAL(10, 2) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE halls_table MODIFY price DECIMAL(10, 2) NOT NULL');
    }
}
