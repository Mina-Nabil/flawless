<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDndSetterToPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId("PTNT_DND_USER_ID")->nullable()->after("PTNT_DND_RSON")->constrained("dash_users")->nullOnDelete();
            $table->dateTime("PTNT_DND_AT")->nullable()->after("PTNT_DND_USER_ID");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropConstrainedForeignId("PTNT_DND_USER_ID");
            $table->dropColumn("PTNT_DND_AT");
        });
    }
}
