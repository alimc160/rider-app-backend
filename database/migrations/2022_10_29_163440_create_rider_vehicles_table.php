<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')
                ->constrained('riders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->cascadeOnUpdate()
                ->onDelete('restrict');
            $table->string('registration_number');
            $table->string('image')->nullable();
            $table->string('color')->nullable();
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
        Schema::dropIfExists('rider_vehicles');
    }
}
