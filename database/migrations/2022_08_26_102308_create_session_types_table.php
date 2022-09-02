<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_types', function (Blueprint $table) {
            $table->id();
            $table->string('SHTP_NAME')->unique();
            $table->integer('SHTP_DUR');
            $table->string('SHTP_DESC')->nullable();
            $table->boolean('SHTP_ACTV')->default(true);
        });

        Schema::create('session_types_doctors', function (Blueprint $table){
            $table->id();
            $table->foreignId('SHTD_SHTP_ID')->constrained('session_types');
            $table->foreignId('SHTD_DASH_ID')->constrained('dash_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_types_doctors');
        Schema::dropIfExists('session_types');
    }
}
