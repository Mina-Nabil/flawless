<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("SSHN_PTNT_ID")->constrained("patients");
            $table->foreignId("SSHN_DCTR_ID")->nullable()->constrained("dash_users");    //DOCTOR 
            $table->foreignId("SSHN_OPEN_ID")->constrained("dash_users");    //Opened by
            $table->enum("SSHN_STTS", ["New", "Pending Payment", "Done", "Cancelled"])->default("New");
            $table->enum("SSHN_PYMT_TYPE", ["Cash", "Visa"]);
            $table->date("SSHN_DATE");
            $table->time("SSHN_STRT_TIME");
            $table->time("SSHN_END_TIME");
            $table->double("SSHN_TOTL")->default(0);        //plus
            $table->double("SSHN_PAID")->default(0);        //minus
            $table->double("SSHN_DISC")->default(0);        //minus
            $table->double("SSHN_PTNT_BLNC")->default(0);   //minus
            $table->double("SSHN_ACPT_ID")->nullable()->constrained("dash_users");
            $table->text("SSHN_TEXT")->nullable();
            $table->tinyInteger("SSHN_CMSH")->default(1);
            $table->timestamps();
        });

        Schema::create('session_items', function (Blueprint $table){
            $table->id();
            $table->foreignId("SHIT_SSHN_ID")->constrained("sessions");
            $table->foreignId("SHIT_PLIT_ID")->constrained("pricelist_items");
            $table->double("SHIT_PRCE");
            $table->string("SHIT_NOTE")->nullable();
            $table->integer("SHIT_QNTY")->default(1);
            $table->double("SHIT_TOTL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_items');
        Schema::dropIfExists('sessions');
    }
}
