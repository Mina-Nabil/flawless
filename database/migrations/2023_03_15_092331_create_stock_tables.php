<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
   
        Schema::create('stock_types', function (Blueprint $table){
            $table->id();
            $table->string("STTP_NAME")->unique();
        });

        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string("STCK_NAME");
            $table->foreignId("STCK_STTP_ID")->constrained("stock_types");
            $table->boolean("STCK_SSHN")->default(1); //if stock item can be used in sessions
            $table->boolean("STCK_ACTV")->default(1); //if stock item can be used 3amatan
            $table->integer("STCK_CUNT")->default(0);
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->dateTime("STTR_DATE");
            $table->bigInteger("STTR_CODE");
            $table->foreignId("STTR_STCK_ID")->constrained("stock_items");
            $table->foreignId("STTR_DASH_ID")->constrained("dash_users");
            $table->integer("STTR_AMNT");
            $table->integer("STTR_BLNC");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_tables');
    }
}
