<?php

use App\Models\FollowUp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("FLUP_SSHN_ID")->constrained("sessions");
            $table->foreignId("FLUP_DASH_ID")->nullable()->constrained("dash_users"); //caller
            $table->enum("FLUP_STTS",FollowUp::$STATES)->default("New");
            $table->date("FLUP_DATE");
            $table->dateTime("FLUP_CALL")->nullable(); //last call
            $table->text("FLUP_TEXT")->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('followups');
    }
}
