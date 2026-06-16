<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_messages', function (Blueprint $table) {
            $table->id();
            $table->text('ALRT_TEXT');
            $table->foreignId('ALRT_DASH_ID')->constrained('dash_users');
            $table->boolean('ALRT_ACTV')->default(1);
            $table->dateTime('ALRT_EXPR')->nullable();
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
        Schema::dropIfExists('alert_messages');
    }
}
