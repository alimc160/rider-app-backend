<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderSelfiePictureStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_selfie_picture_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')
                ->constrained('riders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('status',['pending','in_progress','approved','cancelled'])
                ->default('pending');
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
        Schema::dropIfExists('rider_selfie_picture_statuses');
    }
}
