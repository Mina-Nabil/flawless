<?php

use App\Models\DoctorAvailability;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors_availability', function (Blueprint $table) {
            $table->id();
            $table->integer('DVAC_DAY_OF_WEEK');
            $table->enum('DCAV_SHFT', DoctorAvailability::SHIFTS_ARR);
            $table->foreignId('DCAV_DASH_ID')->constrained('dash_users');
            $table->foreignId('DCAV_BRCH_ID')->constrained('branches');
            $table->text('DCAV_NOTE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_availability');
    }
}
