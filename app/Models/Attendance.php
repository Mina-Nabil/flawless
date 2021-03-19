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

    public static function createAttendance($doctor, $date, $isConfirmed = false)
    {
        $prevAttendance = self::hasAttendance($doctor, $date);
        if (is_null($prevAttendance)) {
            $insertArr = [  
                "ATND_DCTR_ID"  =>  $doctor,
                "ATND_DATE"     =>  $date
            ];
            if ($isConfirmed)
                $insertArr['ATND_STTS'] = 'Confirmed';
            return self::insert($insertArr); //returns 1 if inserted .. 0 if no
        } elseif ($isConfirmed && Auth::user()->isAdmin()) {
            $prevAttendance->ATND_STTS = "Confirmed";
            $prevAttendance->ATND_USER_ID = Auth::user()->id;
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
