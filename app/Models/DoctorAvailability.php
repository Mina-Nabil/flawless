<?php

namespace App\Models;

use Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log as LaravelLog;

class DoctorAvailability extends Model
{
    protected $table = 'doctors_availability';
    public $timestamps = false;
    protected $with = ['branch', 'doctor'];
    const SHIFT_1 = 'Shift 1';
    const SHIFT_2 = 'Shift 2';
    const SHIFTS_ARR = [self::SHIFT_1, self::SHIFT_2];
    const DAY_SUN = 1;
    const DAY_MON = 2;
    const DAY_TUS = 3;
    const DAY_WED = 4;
    const DAY_THU = 5;
    const DAY_FRI = 6;
    const DAY_SAT = 7;
    const DAYS_ARR = [
        self::DAY_SUN => 'Sunday',
        self::DAY_MON => 'Monday',
        self::DAY_TUS => 'Tuesday',
        self::DAY_WED => 'Wednesday',
        self::DAY_THU => 'Thursday',
        self::DAY_FRI => 'Friday',
        self::DAY_SAT => 'Saturday'
    ];

    /////static functions and scopes
    /** 
     * @return array of available doctors - 'doctor' & 'availablity' bool & 'exception' bool
     */
    public static function getAvailableDoctors(Carbon $date, string $shift, int $doctorID = null, int $branchID = null)
    {
        $availability = self::getAvailability($date, $shift, $doctorID, $branchID);
        $comingExceptions = AvailabilityException::getExceptions($date, $shift, 1, $doctorID, $branchID);
        $nonComingExceptions = AvailabilityException::getExceptions($date, $shift, 0, $doctorID, $branchID);
        $availability->filter(function ($record) use ($nonComingExceptions) {
            foreach ($nonComingExceptions as $nonRecord) {
                if ($record->doctor->id == $nonRecord->doctor->id) {
                    return false;
                }
            }
            return true;
        });
        $availableDoctors = array();
        foreach ($availability as $availabilityRecord) {

            if ($availabilityRecord->doctor->id)
                array_push($availableDoctors, [
                    "doctor"        =>  $availabilityRecord->doctor,
                    "branch"        =>  $availabilityRecord->branch,
                    "availablity"   =>  true,
                    "exception"    =>  false
                ]);
        }
        foreach ($comingExceptions as $exceptionRecord) {

            array_push($availableDoctors, [
                "doctor"        =>  $exceptionRecord->doctor,
                "branch"        =>  $exceptionRecord->branch,
                "availablity"   =>  false,
                "exception"    =>  true
            ]);
        }

        return $availableDoctors;
    }

    public static function getAvailability(Carbon $date, string $shift, int $doctorID = null, int $branchID = null)
    {
        $query = self::day($date)->shift($shift);
        if ($doctorID) {
            $query = $query->where('DCAV_DASH_ID', $doctorID);
        }
        if ($branchID) {
            $query = $query->where('DCAV_BRCH_ID', $branchID);
        }

        return $query->get();
    }

    /**
     * @param int $day is the number representing day of week where Sat is 7 and Sunday is 1 
     */
    public static function newDoctorAvailability(int $branchID, int $doctorID, int $day, string $shift, string $note = null): self
    {
        $newDoctorAvailability = new self;
        $newDoctorAvailability->DCAV_DASH_ID = $doctorID;
        $newDoctorAvailability->DCAV_SHFT = $shift;
        $newDoctorAvailability->DVAC_DAY_OF_WEEK = $day;
        $newDoctorAvailability->DCAV_BRCH_ID = $branchID;
        $newDoctorAvailability->DCAV_NOTE = $note;
        $newDoctorAvailability->save();
        return $newDoctorAvailability;
    }

    public function scopeDay($query, Carbon $date)
    {
        return $query->where("DVAC_DAY_OF_WEEK", $date->dayOfWeek + 1);
    }

    public function scopeShift($query, $shift)
    {
        return $query->where("DCAV_SHFT", $shift);
    }


    ///////model actions
    public function updateInfo(int $branchID, int $doctorID, int $day, string $shift, string $note = null): bool
    {
        $this->DCAV_DASH_ID = $doctorID;
        $this->DCAV_SHFT = $shift;
        $this->DVAC_DAY_OF_WEEK = $day;
        $this->DCAV_BRCH_ID = $branchID;
        $this->DCAV_NOTE = $note;
        return $this->save();
    }

    public function getDVACDAYOFWEEKAttribute($value)
    {
        return self::DAYS_ARR[$value] ?? $value;
    }

    ////relations

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, 'DCAV_DASH_ID');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'DCAV_BRCH_ID');
    }
}
