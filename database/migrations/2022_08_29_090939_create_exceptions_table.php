<?php

use App\Models\DoctorAvailability;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('EXPT_BRCH_ID')->constrained('branches');
            $table->foreignId('EXPT_DASH_ID')->constrained('dash_users');
            $table->foreignId('EXPT_DOCT_ID')->constrained('dash_users');
            $table->date('EXPT_DATE');
            $table->enum('EXPT_SHFT', DoctorAvailability::SHIFTS_ARR);
            $table->boolean('EXPT_AVAL')->default(false); //exception available or not available ? 
            $table->string('EXPT_DESC')->nullable();
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
        Schema::dropIfExists('exceptions');
    }
}
