<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerToPatientPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->foreignId("PTPK_USER_ID")->nullable()->after("PTPK_PRCE")->constrained("dash_users")->nullOnDelete();
            $table->dateTime("PTPK_DATE")->nullable()->after("PTPK_USER_ID");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId("PTPK_USER_ID");
            $table->dropColumn("PTPK_DATE");
        });
    }
}
