<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ALRC_ALRT_ID')->constrained('alert_messages')->cascadeOnDelete();
            $table->foreignId('ALRC_DASH_ID')->constrained('dash_users')->cascadeOnDelete();
            $table->dateTime('ALRC_READ_AT')->nullable();
            $table->timestamps();
            $table->unique(['ALRC_ALRT_ID', 'ALRC_DASH_ID']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alert_recipients');
    }
}
