<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Visa extends Model
{
    protected $table = "visa_transactions";

    public function dash_user(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, "VISA_DASH_ID");
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, "VISA_BRCH_ID");
    }

    /**
     * @param $branchID 0 for all branches or brach ID
     * @return Collection
     */
    public static function filter($branchID, $startDate, $endDate)
    {
        $query = self::with("dash_user")->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id');
        if ($branchID != 0) {
            $query = $query->where('VISA_BRCH_ID', $branchID);
        }
        return $query->get();
    }

    public static function scopeToday($query, $branch = 0)
    {
        $query = $query->with("dash_user")->whereDate('created_at', Carbon::today())->orderByDesc('id');
        if ($branch != 0) {
            $query = $query->where('VISA_BRCH_ID', $branch);
        }
        return $query;
    }

    public static function scopeLatest300($query, $branch = 0)
    {
        $query = $query->with("dash_user")->orderByDesc('id')->limit(300);
        if ($branch != 0) {
            $query = $query->where('VISA_BRCH_ID', $branch);
        }
        return $query;
    }

    public static function paidToday($branch = 0)
    {
        $query = DB::table('visa_transactions')->selectRaw("SUM(VISA_OUT) as paidToday")
            ->whereRaw("Date(created_at) = CURDATE()");
        if ($branch != 0)
            $query = $query->where("VISA_BRCH_ID", $branch);
        return $query->get()->first()->paidToday ?? 0;
    }

    public static function collectedToday($branch = 0)
    {
        $query = DB::table('visa_transactions')->selectRaw("SUM(VISA_IN) as collectedToday")
            ->whereRaw("Date(created_at) = CURDATE()");
        if ($branch != 0)
            $query = $query->where("VISA_BRCH_ID", $branch);
        return $query->get()->first()->collectedToday ?? 0;
    }

    public static function currentBalance($branch = 0)
    {
        $query = self::orderBy("id", 'desc');
        if ($branch != 0)
            $query = $query->where("VISA_BRCH_ID", $branch);
        return $query->first()->VISA_BLNC ?? 0;
    }

    public static function yesterdayBalance($branch = 0)
    {
        $query = self::whereRaw("Date(created_at) < CURDATE()")->orderByDesc('id');
        if ($branch != 0)
            $query = $query->where("VISA_BRCH_ID", $branch);
        return $query->get()->first()->VISA_BLNC ?? 0;
    }

    public static function entry($branch, $desc, $in = 0, $out = 0, $comment = null)
    {
        $latest = self::where('VISA_BRCH_ID', $branch)->orderBy("id", 'desc')->first();
        $balance = ($latest->VISA_BLNC ?? 0) + $in - $out;

        $newEntry = new self();
        $newEntry->VISA_IN = $in;
        $newEntry->VISA_OUT = $out;
        $newEntry->VISA_DESC = $desc;
        $newEntry->VISA_BRCH_ID = $branch;
        $newEntry->VISA_CMNT = $comment;
        $newEntry->VISA_BLNC = $balance;
        $newEntry->VISA_DASH_ID = Auth::id();
        $newEntry->save();
    }
}
