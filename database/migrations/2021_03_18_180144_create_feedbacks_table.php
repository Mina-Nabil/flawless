<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->date('FDBK_DATE');
            $table->foreignId('FDBK_SSHN_ID')->constrained('sessions');
            $table->foreignId('FDBK_DASH_ID')->nullable()->constrained('sessions'); //caller
            $table->enum("FDBK_STTS", ["New", "Called", "Cancelled"])->default("New");
            $table->dateTime('FDBK_CALL')->nullable();
            $table->double('FDBK_OVRL')->nullable();
            // $table->double('FDBK_DCTR')->default(0);
            $table->text('FDBK_TEXT')->nullable(); //extra comment
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
        Schema::dropIfExists('feedbacks');
    }
}
