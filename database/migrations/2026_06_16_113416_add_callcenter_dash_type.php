<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCallcenterDashType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dash_types')->updateOrInsert(
            ['id' => 4],
            ['DHTP_NAME' => 'callcenter']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dash_types')->where('id', 4)->where('DHTP_NAME', 'callcenter')->delete();
    }
}
