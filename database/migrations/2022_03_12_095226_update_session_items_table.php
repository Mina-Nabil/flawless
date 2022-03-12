<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSessionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("session_items", function (Blueprint $table) {
            $table->integer("SHIT_DCTR")->default(1);
        });
        Schema::table("sessions", function (Blueprint $table) {
            $table->double("SSHN_DCTR_TOTL")->default(0);
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
            $table->dropColumn("SHIT_DCTR");
        });
        Schema::table("sessions", function (Blueprint $table) {
            $table->dropColumn("SSHN_DCTR_TOTL");
        });
    }
}
