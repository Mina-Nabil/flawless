<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricelistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricelists', function (Blueprint $table) {
            $table->id();
            $table->string("PRLS_NAME");
        });

        Schema::create("pricelist_items", function (Blueprint $table){
            $table->id();
            $table->foreignId("PRIT_PRLS_ID")->constrained('devices');
            $table->enum("PRLS_TYPE", ["Area", "Pulse", "Session"]);
            $table->foreignId("PRLS_DVIC_ID")->constrained('devices');
            $table->foreignId("PRLS_AREA_ID")->constrained('areas')->nullable();
            $table->double("PRLS_PRCE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricelist_item');
        Schema::dropIfExists('pricelists');
    }
}
