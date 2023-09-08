<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderCnicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_cnics', function (Blueprint $table) {
            $table->string('front_pic')->nullable();
            $table->string('back_pic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rider_cnics',function (Blueprint $table){
           $table->dropColumn('front_pic');
           $table->dropColumn('back_pic');
        });
    }
}
