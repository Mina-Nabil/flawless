<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{
    protected $table = "attendance";
    protected $dates = [
        'ATND_DATE',
    ];

    public function setAttendance($type, $shifts = 1, $comment = null)
    {
        $this->ATND_STTS = $type;
        $this->ATND_USER_ID = Auth::user()->id;
        $this->ATND_SHFT = $shifts;
        if (!is_null($comment))
            $this->ATND_CMNT = $comment;
        return $this->save();
    }

    public static function createAttendance($branch, $doctor, $date, $comment = null, $shifts = 1)
    {
        $prevAttendance = self::hasAttendance($branch, $doctor, $date);
        if (is_null($prevAttendance)) {
            $insertArr = [
                "ATND_BRCH_ID"  =>  $branch ?? 1,
                "ATND_DCTR_ID"  =>  $doctor,
                "ATND_DATE"     =>  $date,
                "ATND_SHFT"     =>  $shifts,
                "ATND_CMNT"     =>  $comment,
            ];
            if (Auth::user()->isAdmin()) {
                $insertArr['ATND_STTS'] = 'Confirmed';
                $insertArr['ATND_USER_ID'] = Auth::user()->id;
            }
            return self::insert($insertArr); //returns 1 if inserted .. 0 if no
        } elseif (Auth::user()->isAdmin()) {
            $prevAttendance->ATND_STTS = "Confirmed";
            $prevAttendance->ATND_USER_ID = Auth::user()->id;
            $prevAttendance->ATND_CMNT = $comment;
            $prevAttendance->save();
        }
        return -1; //already has attendance
    }

    private static function hasAttendance($branch, $doctor, $date)
    {
        return self::where("ATND_DCTR_ID", $doctor)->where("ATND_DATE", $date)->where("ATND_BRCH_ID", $branch)->first();
    }

    static function getUnconfirmedCount($branch = 0)
    {
        $query = self::where("ATND_STTS", "NEW");
        if ($branch != 0) {
            $query = $query->where("ATND_BRCH_ID", $branch);
        }
        return $query->get()->count();
    }

    //Query 
    public static function getAttendanceData($branch = 0, $type = null, $from = null, $to = null, $doctor = null)
    {
        $query = Attendance::with('doctor', 'branch');
        if (!is_null($type))
            if ($type == 'NotCancelled') {
                $query = $query->where("ATND_STTS", "!=", "Cancelled");
            } elseif ($type == 'NotCancelled') {
                $query = $query->where("ATND_STTS", $type);
            }

        if (!is_null($branch) && $branch > 0) {
            $query = $query->where("ATND_BRCH_ID", $branch);
        }

        if (!is_null($doctor) && $doctor > 0) {
            $query = $query->where("ATND_DCTR_ID", $doctor);
        }

        if (!is_null($from) && !is_null($to)) {
            $query = $query->whereBetween("ATND_DATE", [
                $from, $to
            ]);
        }
        return $query->get();
    }

    ///relations
    function doctor()
    {
        return $this->belongsTo("App\Models\DashUser", "ATND_DCTR_ID");
    }

    function branch()
    {
        return $this->belongsTo(Branch::class, "ATND_BRCH_ID");
    }

    function accepter()
    {
        return $this->belongsTo("App\Models\DashUser", "ATND_USER_ID");
    }
}
