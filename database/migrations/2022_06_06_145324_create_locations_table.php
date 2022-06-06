<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('LOCT_NAME')->unique();
        });


        Schema::table('patients', function (Blueprint $table){
            $table->foreignId('PTNT_LOCT_ID')->nullable()->constrained('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table){
            $table->dropForeign('patients_locations_PTNT_LOCT_ID');
            $table->dropColumn('PTNT_LOCT_ID');
        });
        Schema::dropIfExists('locations');
    }
}
