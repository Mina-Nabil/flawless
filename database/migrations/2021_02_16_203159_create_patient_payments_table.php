<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('PTPY_PTNT_ID')->constrained('patients');
            $table->foreignId('PTPY_DASH_ID')->constrained('dash_users');
            $table->double('PTPY_PAID');
            $table->double('PTPY_BLNC');
            $table->string('PTPY_CMNT')->nullable();
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
        Schema::dropIfExists('patient_payments');
    }
}
