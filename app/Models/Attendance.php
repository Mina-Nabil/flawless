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

    public function setAttendance($type, $comment = null)
    {
        $this->ATND_STTS = $type;
        $this->ATND_USER_ID = Auth::user()->id;
        if (!is_null($comment))
            $this->ATND_CMNT = $comment;
        $this->save();
    }

    public static function createAttendance($doctor, $date, $comment = null)
    {
        $prevAttendance = self::hasAttendance($doctor, $date);
        if (is_null($prevAttendance)) {
            $insertArr = [
                "ATND_DCTR_ID"  =>  $doctor,
                "ATND_DATE"     =>  $date,
                "ATND_CMNT"     =>  $comment,
            ];
            if (Auth::user()->isAdmin()){
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

    private static function hasAttendance($doctor, $date)
    {
        return self::where("ATND_DCTR_ID", $doctor)->where("ATND_DATE", $date)->first();
    }

    static function getUnconfirmedCount()
    {
        return self::where("ATND_STTS", "NEW")->get()->count();
    }

    //Query 
    public static function getAttendanceData($type = null, $from = null, $to = null, $doctor = null)
    {
        $query = Attendance::with('doctor');
        if (!is_null($type) && $type!='All') {
            $query = $query->where("ATND_STTS", $type);
        }

        if (!is_null($doctor) && $doctor>0) {
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

    function accepter()
    {
        return $this->belongsTo("App\Models\DashUser", "ATND_USER_ID");
    }
}
