<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUuidInBookingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->string('uuid')->unique()->nullable();
        });

        // add uuid on creating booking order
        DB::unprepared('
        CREATE TRIGGER trigger_add_uuid_on_add_booking_order BEFORE INSERT ON `booking_orders` FOR EACH ROW
            BEGIN
                set new.uuid = uuid();
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        DB::unprepared('DROP TRIGGER `trigger_add_uuid_on_add_booking_order`');
    }
}
