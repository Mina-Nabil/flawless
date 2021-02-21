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
            $table->string("PRLS_NAME")->unique();
            $table->tinyInteger("PRLS_DFLT")->default(0);
        });

        Schema::create("pricelist_items", function (Blueprint $table){
            $table->id();
            $table->foreignId("PLIT_PRLS_ID")->constrained('pricelists');
            $table->enum("PLIT_TYPE", ["Area", "Pulse", "Session"]);
            $table->foreignId("PLIT_DVIC_ID")->constrained('devices');
            $table->foreignId("PLIT_AREA_ID")->nullable()->constrained('areas');
            $table->double("PLIT_PRCE");
        });

        Schema::table("patients", function (Blueprint $table){
            $table->foreignId("PTNT_PRLS_ID")->nullable()->constrained("pricelists");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("patients", function (Blueprint $table){
            $table->dropForeign("patients_ptnt_prls_id_foreign");
            $table->dropColumn("PTNT_PRLS_ID");
        });
        Schema::dropIfExists('pricelist_items');
        Schema::dropIfExists('pricelists');
    }
}
