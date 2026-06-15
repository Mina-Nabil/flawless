<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientNameLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_name_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("PNML_PTNT_ID")->constrained("patients")->cascadeOnDelete();
            $table->string("PNML_FROM")->nullable();
            $table->string("PNML_TO");
            $table->foreignId("PNML_DASH_ID")->nullable()->constrained("dash_users")->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_name_logs');
    }
}
