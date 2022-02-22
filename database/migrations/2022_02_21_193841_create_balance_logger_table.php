<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceLoggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_logger', function (Blueprint $table) {
            $table->id();
            $table->string("BLLG_TTLE");
            $table->double("BLLG_IN")->default(0);
            $table->double("BLLG_OUT")->default(0);
            $table->foreignId('BLLG_DASH_ID')->constrained('dash_users');
            $table->foreignId('BLLG_PTNT_ID')->constrained('patients');
            $table->string("BLLG_CMNT")->nullable();
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
        Schema::dropIfExists('balance_logger');
    }
}
