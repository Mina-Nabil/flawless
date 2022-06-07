<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('BRCH_NAME');
        });

        DB::table('branches')->insert([
            "id"    =>  "1",
            "BRCH_NAME" => "Sheraton"
        ]);

        Schema::table('dash_users', function (Blueprint $table) {
            $table->foreignId('DASH_BRCH_ID')->nullable()->constrained('branches');
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->foreignId('CASH_BRCH_ID')->default(1)->constrained('branches');
        });

        Schema::table('visa_transactions', function (Blueprint $table) {
            $table->foreignId('VISA_BRCH_ID')->default(1)->constrained('branches');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('ATND_BRCH_ID')->default(1)->constrained('branches');
        });

        Schema::table('followups', function (Blueprint $table) {
            $table->foreignId('FLUP_BRCH_ID')->default(1)->constrained('branches');
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->foreignId('FDBK_BRCH_ID')->default(1)->constrained('branches');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('SSHN_BRCH_ID')->default(1)->constrained('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
