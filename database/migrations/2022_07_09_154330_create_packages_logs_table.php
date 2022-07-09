<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages_logs', function (Blueprint $table) {
            $table->id();
            $table->string("PKLG_TTLE");
            $table->double("PKLG_AMNT");
            $table->foreignId('PKLG_DASH_ID')->constrained('dash_users');
            $table->foreignId('PKLG_PTNT_ID')->constrained('patients');
            $table->string("PKLG_CMNT")->nullable();
            $table->foreignId("PKLG_SSHN_ID")->nullable()->constrained('sessions');
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
        Schema::dropIfExists('packages_logs');
    }
}
