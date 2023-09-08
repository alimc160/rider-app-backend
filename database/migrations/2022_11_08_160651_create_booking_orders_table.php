<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->foreignId('rider_id')
                ->nullable()
                ->constrained('riders')
                ->cascadeOnUpdate();
            $table->string('cargo_booking_id');
            $table->text('pickup_location');
            $table->double('pickup_lat');
            $table->double('pickup_long');
            $table->text('drop_off_location');
            $table->double('drop_off_lat');
            $table->double('drop_off_long');
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();
            $table->string('customer_cnic')->nullable();
            $table->string('notes')->nullable();
            $table->decimal('booking_amount')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('agent_contact')->nullable();
            $table->string('receiver_pic')->nullable();
            $table->enum('status', [
                    'pending',
                    'accepted',
                    'arrived_for_pickup',
                    'package_received',
                    'arrived_for_drop_off',
                    'package_delivered'
                ])->default('pending');
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
        Schema::dropIfExists('booking_orders');
    }
}
