<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('name');
            $table->string('user_name')->nullable();
            $table->string('father_name');
            $table->string('cnic');
            $table->unsignedBigInteger('city_id');
            $table->string('phone_number');
            $table->string('email');
            $table->string('otp')->nullable();
            $table->unsignedBigInteger('verfication_attempts')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        DB::unprepared('
        CREATE TRIGGER trigger_add_uuid_on_add_rider BEFORE INSERT ON `riders` FOR EACH ROW
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
        Schema::dropIfExists('riders');
        DB::unprepared('DROP TRIGGER `trigger_add_uuid_on_add_rider`');

    }
}
