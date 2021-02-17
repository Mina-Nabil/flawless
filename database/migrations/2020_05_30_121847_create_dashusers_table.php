<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('dash_types', function (Blueprint $table){
            $table->id();
            $table->string('DHTP_NAME');
        });

        Schema::create('dash_users', function (Blueprint $table) {
            $table->id();
            $table->string('DASH_USNM');
            $table->string('DASH_FLNM');
            $table->string('DASH_PASS');
            $table->tinyInteger('DASH_ACTV')->default(1);
            $table->string('DASH_MOBN')->nullable();
            $table->string('DASH_IMGE')->nullable();
            $table->foreignId('DASH_TYPE_ID')->constrained("dash_types")->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('dash_users');
        Schema::dropIfExists('dash_types');
    }
}
