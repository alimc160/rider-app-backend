<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInProgressOptionInRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE riders CHANGE COLUMN status status ENUM('pending','approved','cancelled','blocked','in_progress') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE riders CHANGE COLUMN status status ENUM('pending','approved','cancelled','blocked') NOT NULL DEFAULT 'pending'");
    }
}
