<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId("ATND_DCTR_ID")->constrained("dash_users");
            $table->foreignId("ATND_USER_ID")->nullable()->constrained("dash_users"); //confirmer
            $table->enum("ATND_STTS",["New", "Confirmed", "Cancelled"])->default('New');
            $table->text("ATND_CMNT")->nullable();
            $table->date("ATND_DATE");
            $table->tinyInteger("ATND_SHFT")->default(1);
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
        Schema::dropIfExists('attendance');
    }
}
