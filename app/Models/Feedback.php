<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Feedback extends Model
{
    protected $table = "feedbacks";
    protected $dates = [
        'FDBK_DATE',
    ];

    public function setCalled($state, $overall = null, $comment = null)
    {
        $this->FDBK_STTS = $state;
        $this->FDBK_TEXT = $comment;
        if ($state == "Called") {
            $this->FDBK_OVRL = $overall;
        }
        $this->FDBK_DASH_ID = Auth::user()->id;
        $this->FDBK_CALL = (new DateTime())->format('Y-m-d H:i:s');
        return $this->save();
    }


    //Query 
    public static function getFeedbackData($state = null, $from = null, $to = null, $caller = null)
    {
        $query = self::with('caller', 'session.patient');
        if (!is_null($state) && $state != 'All') {
            $query = $query->where("FDBK_STTS", $state);
        }

        if (!is_null($caller) && $caller > 0) {
            $query = $query->where("FDBK_DASH_ID", $caller);
        }

        if (!is_null($from) && !is_null($to)) {
            $query = $query->whereBetween("FDBK_DATE", [
                $from, $to
            ]);
        }
        return $query->get();
    }

    public static function createFeedback($sessionID, $date, $comment = null)
    {
        return self::insert([
            "FDBK_SSHN_ID"  =>  $sessionID,
            "FDBK_DATE"     =>  $date,
            "FDBK_TEXT"     =>  $comment
        ]);
    }

    static function getUnconfirmedCount()
    {
        return self::where("FDBK_STTS", "New")->whereRaw("FDBK_DATE <= CURDATE()")->get()->count();
    }

    ///relations
    function caller()
    {
        return $this->belongsTo("App\Models\DashUser", "FDBK_DASH_ID");
    }

    function session()
    {
        return $this->belongsTo("App\Models\Session", "FDBK_SSHN_ID");
    }
}
