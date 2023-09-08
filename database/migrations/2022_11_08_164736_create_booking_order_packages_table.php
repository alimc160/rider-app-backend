<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingOrderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_order_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_order_id')
                ->constrained('booking_orders')
                ->cascadeOnUpdate();
            $table->string('type');
            $table->string('weight');
            $table->integer('no_of_items')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('booking_order_packages');
    }
}
