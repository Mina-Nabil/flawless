<?php

use App\Models\DashUser;
use App\Models\Lead;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('leads');

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("LEAD_NAME");
            $table->string("LEAD_MOBN");
            $table->enum("LEAD_STTS", Lead::STATUSES);
            $table->foreignIdFor(DashUser::class, "LEAD_USER_ID")->constrained('dash_users');
            $table->string("LEAD_ADRS")->nullable();
            $table->string("LEAD_NOTE")->nullable();
            $table->string("LEAD_PRMO")->nullable();
            $table->unsignedBigInteger("LEAD_PTNT_ID")->nullable();
            $table->timestamps();
        });

        Schema::table("followups", function (Blueprint $table) {
            $table->unsignedBigInteger("FLUP_PTNT_ID")->nullable()->constrained("patients")->change();
            $table->foreignId("FLUP_LEAD_ID")->nullable()->constrained("leads");
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->string("PTNT_PRMO")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
