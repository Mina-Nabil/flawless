<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cash extends Model
{
    protected $table = "cash_transactions";

    public function dash_user() {
        return $this->belongsTo("App\Models\DashUser", "CASH_DASH_ID");
    }

    public static function paidToday(){
        return DB::table('cash_transactions')->selectRaw("SUM(CASH_OUT) as paidToday")
                    ->whereRaw("Date(created_at) = CURDATE()")
                    ->get()->first()->paidToday ?? 0;
    }

    public static function collectedToday(){
        return DB::table('cash_transactions')->selectRaw("SUM(CASH_IN) as collectedToday")
                    ->whereRaw("Date(created_at) = CURDATE()")
                    ->get()->first()->collectedToday ?? 0;
    }

    public static function currentBalance(){
        return self::latest()->first()->CASH_BLNC ?? 0;
    }

    public static function yesterdayBalance(){
        return self::whereRaw("Date(created_at) < CURDATE()")->orderByDesc('id')->get()->first()->CASH_BLNC ?? 0;
    }

    public static function entry($desc, $in=0, $out=0, $comment=null){
        $latest = self::latest()->first();
        $balance = ($latest->CASH_BLNC ?? 0) + $in - $out;

        $newEntry = new self();
        $newEntry->CASH_IN = $in;
        $newEntry->CASH_OUT = $out;
        $newEntry->CASH_DESC = $desc;
        $newEntry->CASH_CMNT = $comment;
        $newEntry->CASH_BLNC = $balance;
        $newEntry->CASH_DASH_ID = Auth::id();
        $newEntry->save();
    }
}
