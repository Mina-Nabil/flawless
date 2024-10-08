<?php

namespace App\Models;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowUp extends Model
{
    use SoftDeletes;

    protected $table = "followups";
    protected $dates = [
        'FLUP_DATE',
        "FLUP_LAST_SSHN"
    ];
    protected $fillable = [
        'FLUP_BRCH_ID',
        'FLUP_DATE',
        "FLUP_LAST_SSHN"
    ];

    public const NEW_STATE = "New";
    public const SATISFIED = "Satified";
    public const NOT_SATISFIED = "7azeen";
    public const NO_ANSWER = "No Answer";

    public static $STATES = [
        self::NEW_STATE,
        self::SATISFIED,
        self::NOT_SATISFIED,
        self::NO_ANSWER,
    ];

    public function setCalled($state, $comment = null)
    {
        $this->FLUP_STTS = $state;
        $this->FLUP_TEXT = $comment;
        $this->FLUP_DASH_ID = Auth::user()->id;
        $this->FLUP_CALL = (new DateTime())->format('Y-m-d H:i:s');
        return $this->save();
    }

    //Query 
    public static function getFollowupsData($branchID = 0, $state = null, $from = null, $to = null, $caller = null, $leadID = null)
    {
        $query = self::with('caller', 'patient', 'branch', 'lead');
        if (!is_null($state) && $state != 'All') {
            $query = $query->where("FLUP_STTS", $state);
        }

        if (!is_null($branchID) && $branchID > 0) {
            $query = $query->where("FLUP_BRCH_ID", $branchID);
        }

        if (!is_null($caller) && $caller > 0) {
            $query = $query->where("FLUP_DASH_ID", $caller);
        }

        if (!is_null($from) && !is_null($to)) {
            $query = $query->whereBetween("FLUP_DATE", [
                $from,
                $to
            ]);
        } else if(!$leadID) {
            $query = $query->whereRaw("FLUP_DATE <= CURDATE()");
        }

        if(!is_null($leadID)){
            $query = $query->where("FLUP_LEAD_ID", $leadID);
        }
        return $query->get();
    }

    public static function createFollowup($branchID, $patientID, $date, $comment = null, $leadID = null)
    {
        return self::insert([
            "FLUP_BRCH_ID"  =>  $branchID,
            "FLUP_PTNT_ID"  =>  $patientID,
            "FLUP_LEAD_ID"  =>  $leadID,
            "FLUP_DATE"     =>  $date,
            "FLUP_TEXT"     =>  $comment
        ]);
    }

    static function getUnconfirmedCount($branchID = 0)
    {
        $query = self::where("FLUP_STTS", "NEW")->whereRaw("FLUP_DATE <= CURDATE()");
        if ($branchID != 0) {
            $query = $query->where('FLUP_BRCH_ID', $branchID);
        }
        return $query->get()->count();
    }

    static function getLeadFollowupsCount($user_id = 0)
    {
        $query = self::join('leads', 'FLUP_LEAD_ID', '=', 'leads.id')
            ->where("FLUP_STTS", "NEW")
            ->whereRaw("FLUP_DATE <= CURDATE()")
            ->when($user_id, function ($q, $v) {
                $q->where("leads.LEAD_USER_ID", $v);
            });
        return $query->get()->count();
    }

    ///relations
    function caller()
    {
        return $this->belongsTo(DashUser::class, "FLUP_DASH_ID");
    }

    function patient()
    {
        return $this->belongsTo(Patient::class, "FLUP_PTNT_ID");
    }

    function lead()
    {
        return $this->belongsTo(Lead::class, "FLUP_LEAD_ID");
    }

    function branch()
    {
        return $this->belongsTo(Branch::class, "FLUP_BRCH_ID");
    }
}
