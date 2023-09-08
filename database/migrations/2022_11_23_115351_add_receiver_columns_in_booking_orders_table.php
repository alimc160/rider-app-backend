<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiverColumnsInBookingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->string('receiver_contact')->nullable();
            $table->string('receiver_cnic')->nullable();
            $table->string('receiver_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->dropColumn('receiver_contact');
            $table->dropColumn('receiver_cnic');
            $table->dropColumn('receiver_name');
        });
    }
}
