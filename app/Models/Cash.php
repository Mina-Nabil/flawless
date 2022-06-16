<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cash extends Model
{
    protected $table = "cash_transactions";

    public function dash_user(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, "CASH_DASH_ID");
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, "CASH_BRCH_ID");
    }

    /**
     * @param $branchID 0 for all branches or brach ID
     * @return Collection
     */
    public static function filter($branchID, $startDate, $endDate)
    {
        $query = self::with("dash_user", "branch")->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id');
        if ($branchID != 0) {
            $query = $query->where('CASH_BRCH_ID', $branchID);
        }
        return $query->get();
    }

    public static function scopeToday($query, $branch = 0)
    {
        $query = $query->with("dash_user", "branch")->whereDate('created_at', Carbon::today())->orderByDesc('id');
        if ($branch != 0) {
            $query = $query->where('CASH_BRCH_ID', $branch);
        }
        return $query;
    }

    public static function scopeLatest300($query, $branch = 0)
    {
        $query = $query->with("dash_user", "branch")->orderByDesc('id')->limit(300);
        if ($branch != 0) {
            $query = $query->where('CASH_BRCH_ID', $branch);
        }
        return $query;
    }

    public static function paidToday($branch = 0)
    {
        $query = DB::table('cash_transactions')->selectRaw("SUM(CASH_OUT) as paidToday")
            ->whereRaw("Date(created_at) = CURDATE()");
        if ($branch)
            $query = $query->where("CASH_BRCH_ID", $branch);

        return $query->get()->first()->paidToday ?? 0;
    }

    public static function collectedToday($branch = 0)
    {
        $query = DB::table('cash_transactions')->selectRaw("SUM(CASH_IN) as collectedToday")
            ->whereRaw("Date(created_at) = CURDATE()");
        if ($branch)
            $query = $query->where("CASH_BRCH_ID", $branch);
        return $query->get()->first()->collectedToday ?? 0;
    }

    public static function currentBalance($branch = 0)
    {
        $query = self::orderBy("id", 'desc');
        if ($branch){
            $query = $query->where("CASH_BRCH_ID", $branch);
            $balance = $query->first()->CASH_BLNC ?? 0;
        } else {
            $balance = 0;
            foreach (Branch::get() as $branch) {
                $tmpQuery = clone $query;
                $balance += ($tmpQuery->where("CASH_BRCH_ID", $branch->id)->first()->CASH_BLNC) ?? 0;
            }
        }
        return $balance;
    }

    public static function yesterdayBalance($branch = 0)
    {
        $query = self::whereRaw("Date(created_at) < CURDATE()")->orderByDesc('id');
        if ($branch) {
            $query = $query->where("CASH_BRCH_ID", $branch);
            $balance = $query->get()->first()->CASH_BLNC ?? 0;
        } else {
            $balance = 0;
            foreach (Branch::all() as $branch) {
                $balance += $query->where("CASH_BRCH_ID", $branch->id)->get()->first()->CASH_BLNC ?? 0;
            }
        }
        return  $balance;
    }

    public static function entry($branch, $desc, $in = 0, $out = 0, $comment = null)
    {
        $latest = self::where('CASH_BRCH_ID', $branch)->orderBy("id", 'desc')->first();
        $balance = ($latest ? ($latest->CASH_BLNC ?? 0) : 0) + $in - $out;

        $newEntry = new self();
        $newEntry->CASH_IN = $in;
        $newEntry->CASH_OUT = $out;
        $newEntry->CASH_DESC = $desc;
        $newEntry->CASH_BRCH_ID = $branch;
        $newEntry->CASH_CMNT = $comment;
        $newEntry->CASH_BLNC = $balance;
        $newEntry->CASH_DASH_ID = Auth::id();
        $newEntry->save();
    }
}
