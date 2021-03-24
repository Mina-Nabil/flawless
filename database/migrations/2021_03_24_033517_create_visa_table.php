<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string("VISA_DESC")->nullable();
            $table->double("VISA_IN")->default(0);
            $table->double("VISA_OUT")->default(0);
            $table->double("VISA_BLNC");
            $table->foreignId('VISA_DASH_ID')->constrained('dash_users');
            $table->string("VISA_CMNT")->nullable();
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
        Schema::dropIfExists('visa_transactions');
    }
}
