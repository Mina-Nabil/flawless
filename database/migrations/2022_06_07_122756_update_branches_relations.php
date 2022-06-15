<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBranchesRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('followups', function (Blueprint $table) {
            $table->foreignId('FLUP_BRCH_ID')->default(1)->constrained('branches');
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->foreignId('FDBK_BRCH_ID')->default(1)->constrained('branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('followups', function (Blueprint $table){
            $table->dropForeign('followups_flup_brch_id_foreign');
            $table->dropColumn('FLUP_BRCH_ID');
        });
      
        Schema::table('feedbacks', function (Blueprint $table){
            $table->dropForeign('feedbacks_fdbk_brch_id_foreign');
            $table->dropColumn('FDBK_BRCH_ID');
        });
      
    }
}
