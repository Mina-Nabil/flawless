<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('SSHN_ROOM_ID')->nullable()->constrained('rooms');
            $table->string('SSHN_OPEN_NOTE')->nullable();
        });
        DB::statement("ALTER TABLE `sessions` CHANGE `SSHN_STTS` `SSHN_STTS` ENUM('New', 'Pending Payment', 'Done', 'Cancelled', 'Late Cancelled', 'No Show') NOT NULL DEFAULT 'New';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign('sessions_sshn_room_id_foreign');
            $table->dropColumn('SSHN_ROOM_ID');
            $table->dropColumn('SSHN_OPEN_NOTE');
        });
    }
}
