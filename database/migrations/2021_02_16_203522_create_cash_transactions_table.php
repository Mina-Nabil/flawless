<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string("CASH_DESC")->nullable();
            $table->double("CASH_IN")->default(0);
            $table->double("CASH_OUT")->default(0);
            $table->double("CASH_BLNC");
            $table->foreignId('CASH_DASH_ID')->constrained('dash_users');
            $table->string("CASH_CMNT")->nullable();
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
        Schema::dropIfExists('cash_transactions');
    }
}
