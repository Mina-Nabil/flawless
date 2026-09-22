<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReplyToAlertRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alert_recipients', function (Blueprint $table) {
            $table->text('ALRC_RPLY')->nullable()->after('ALRC_READ_AT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alert_recipients', function (Blueprint $table) {
            $table->dropColumn('ALRC_RPLY');
        });
    }
}
