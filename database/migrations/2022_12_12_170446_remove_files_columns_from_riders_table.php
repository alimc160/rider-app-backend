<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFilesColumnsFromRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('licence');
            $table->dropColumn('selfie_picture');
            $table->dropColumn('cnic_front_pic');
            $table->dropColumn('cnic_back_pic');
            $table->dropColumn('contract');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riders', function (Blueprint $table) {
            //
        });
    }
}
