<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Visa extends Model
{
    protected $table = "visa_transactions";

    public function dash_user() {
        return $this->belongsTo("App\Models\DashUser", "VISA_DASH_ID");
    }

    public static function paidToday(){
        return DB::table('visa_transactions')->selectRaw("SUM(VISA_OUT) as paidToday")
                    ->whereRaw("Date(created_at) = CURDATE()")
                    ->get()->first()->paidToday ?? 0;
    }

    public static function collectedToday(){
        return DB::table('visa_transactions')->selectRaw("SUM(VISA_IN) as collectedToday")
                    ->whereRaw("Date(created_at) = CURDATE()")
                    ->get()->first()->collectedToday ?? 0;
    }

    public static function currentBalance(){
        return self::orderBy("id", 'desc')->first()->VISA_BLNC ?? 0;
    }

    public static function yesterdayBalance(){
        return self::whereRaw("Date(created_at) < CURDATE()")->orderByDesc('id')->get()->first()->VISA_BLNC ?? 0;
    }

    public static function entry($desc, $in=0, $out=0, $comment=null){
        $latest = self::orderBy("id", 'desc')->first();
        $balance = ($latest->VISA_BLNC ?? 0) + $in - $out;

        $newEntry = new self();
        $newEntry->VISA_IN = $in;
        $newEntry->VISA_OUT = $out;
        $newEntry->VISA_DESC = $desc;
        $newEntry->VISA_CMNT = $comment;
        $newEntry->VISA_BLNC = $balance;
        $newEntry->VISA_DASH_ID = Auth::id();
        $newEntry->save();
    }
}
