<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnInRiderVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_vehicles', function (Blueprint $table) {
            $table->enum('status',['pending','in_progress','approved','cancelled'])
                ->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rider_vehicles', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
