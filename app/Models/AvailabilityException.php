<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AvailabilityException extends Model
{
    protected $table = 'exceptions';
    protected $with = ['doctor', 'creator', 'branch'];
    protected $fillable  = ['EXPT_DASH_ID', 'EXPT_DOCT_ID', 'EXPT_BRCH_ID', 'EXPT_DATE', 'EXPT_SHFT', 'EXPT_AVAL', 'EXPT_DESC',];
    //////static queries


    public static function getExceptions(Carbon $date, $shift, $isComing, int $doctorID = null, int $branchID = null)
    {
        $query = self::shift($date, $shift)->coming($isComing);

        if ($doctorID) {
            $query = $query->where('EXPT_DASH_ID', $doctorID);
        }
        if ($branchID) {
            $query = $query->where('EXPT_BRCH_ID', $branchID);
        }

        return $query->get();
    }

    public static function newException(int $doctor_id, int $branch_id,  Carbon $date, string $shift, string $desc = null, bool $is_available = false): self
    {
        return self::updateOrCreate([
            'EXPT_DOCT_ID'  =>  $doctor_id,
            'EXPT_BRCH_ID'  =>  $branch_id,
            'EXPT_DATE'  =>  $date->format('Y-m-d'),
            'EXPT_SHFT'  =>  $shift,
        ], [
            'EXPT_DESC'  =>  $desc,
            'EXPT_AVAL'  =>  $is_available,
            'EXPT_DASH_ID'  =>  Auth::user()->id
        ]);
    }

    public function scopeShift($query, Carbon $date, string $shift)
    {
        return $query->where('EXPT_DATE', $date->format('Y-m-d'))->where('EXPT_SHFT', $shift);
    }
    public function scopeComing($query, $isComing)
    {
        return $query->where('EXPT_AVAL', $isComing);
    }

    public function scopeUpcoming($query)
    {
        $today = new Carbon('now');
        return $query->where('EXPT_DATE', ">=", $today->format('Y-m-d'));
    }

    //////relations
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'EXPT_BRCH_ID');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, 'EXPT_DOCT_ID');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, 'EXPT_DASH_ID');
    }
}
