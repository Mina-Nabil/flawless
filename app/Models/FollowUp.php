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
    ];

    //followup functions
    public function cancelSession($comment = null)
    {
        $session = Session::findOrFail($this->FLUP_SSHN_ID);
        return $session->setAsCancelled($comment);
    }


    public function setCalled($state, $comment=null){
        $this->FLUP_STTS = $state;
        $this->FLUP_TEXT = $comment;
        $this->FLUP_DASH_ID = Auth::user()->id;
        $this->FLUP_CALL = (new DateTime())->format('Y-m-d H:i:s');
        return $this->save();
    }

    //Query 
    public static function getFollowupsData($state = null, $from = null, $to = null, $caller = null)
    {
        $query = self::with('caller', 'session.patient');
        if (!is_null($state) && $state != 'All') {
            $query = $query->where("FLUP_STTS", $state);
        }

        if (!is_null($caller) && $caller > 0) {
            $query = $query->where("FLUP_DASH_ID", $caller);
        }

        if (!is_null($from) && !is_null($to)) {
            $query = $query->whereBetween("FLUP_DATE", [
                $from, $to
            ]);
        }
        return $query->get();
    }

    public static function createFollowup($sessionID, $date, $comment = null)
    {
        return self::insert([
            "FLUP_SSHN_ID"  =>  $sessionID,
            "FLUP_DATE"     =>  $date,
            "FLUP_TEXT"     =>  $comment
        ]);
    }


    static function getUnconfirmedCount()
    {
        return self::where("FLUP_STTS", "NEW")->whereRaw("FLUP_DATE <= CURDATE()")->get()->count();
    }

    ///relations
    function caller()
    {
        return $this->belongsTo("App\Models\DashUser", "FLUP_DASH_ID");
    }

    function session()
    {
        return $this->belongsTo("App\Models\Session", "FLUP_SSHN_ID");
    }
}
