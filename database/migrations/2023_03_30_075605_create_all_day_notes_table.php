<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllDayNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_notes', function (Blueprint $table) {
            $table->id();
            $table->string('ADNT_TTLE');
            $table->date('ADNT_DATE');
            $table->string('ADNT_NOTE')->nullable();
            $table->foreignId('ADNT_ROOM_ID')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('ADNT_DASH_ID')->constrained('dash_users');
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
        Schema::dropIfExists('all_day_notes');
    }
}
