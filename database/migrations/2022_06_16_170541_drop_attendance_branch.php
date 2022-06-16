<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAttendanceBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table){
            $table->dropForeign('attendance_atnd_brch_id_foreign');
            $table->dropColumn('ATND_BRCH_ID');
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('ATND_BRCH_ID')->default(1)->constrained('branches');
        });
    }
}
