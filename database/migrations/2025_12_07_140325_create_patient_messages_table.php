<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("PTMS_DVIC_ID")->constrained('devices');
            $table->foreignId("PTMS_AREA_ID")->nullable()->constrained('areas');
            $table->text("PTMS_MSSG");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_messages');
    }
}
