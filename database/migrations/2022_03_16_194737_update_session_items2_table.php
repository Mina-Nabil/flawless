<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSessionItems2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("session_items", function (Blueprint $table) {
            $table->integer("SHIT_CLTD_PCKG")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("session_items", function (Blueprint $table) {
            $table->integer("SHIT_CLTD_PCKG")->default(0);
        });
    }
}
