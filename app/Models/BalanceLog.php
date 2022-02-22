<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceLog extends Model
{
    protected $table = "balance_logger";
    protected $fillable = ["BLLG_IN", "BLLG_OUT", "BLLG_CMNT", "BLLG_TTLE", "BLLG_DASH_ID"];

    //relations
    public function user(){
        return $this->belongsTo(DashUser::class, "BLLG_DASH_ID");
    }
    public function patient(){
        return $this->belongsTo(Patient::class, "BLLG_PTNT_ID");
    }
}
