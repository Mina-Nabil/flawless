<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("followups", function (Blueprint $table){
            $table->dropForeign("followups_flup_sshn_id_foreign");
            $table->dropColumn("FLUP_SSHN_ID");
            $table->foreignId("FLUP_PTNT_ID")->constrained("patients");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("followups", function (Blueprint $table){
            $table->dropForeign("followups_flup_ptnt_id_foreign");
            $table->dropColumn("FLUP_PTNT_ID");
            $table->foreignId("FLUP_SSHN_ID")->constrained("sessions");
        });
    }
}
