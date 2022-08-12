<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_notes', function (Blueprint $table) {
            $table->id();
            $table->string("PNOT_NOTE");
            $table->foreignId('PNOT_DASH_ID')->constrained('dash_users');
            $table->foreignId('PNOT_PTNT_ID')->constrained('patients');
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
        Schema::dropIfExists('patient_notes');
    }
}
