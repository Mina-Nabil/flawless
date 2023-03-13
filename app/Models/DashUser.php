<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashUser extends Authenticatable
{
    use Notifiable;
    protected $table = "dash_users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'DASH_USNM', 'DASH_FLNM', 'DASH_PASS', 'DASH_IMGE', 'DASH_TYPE_ID',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    ////static functions
    /**
     * @return true if available
     */
    public function checkUserAvailablity(Carbon $start_time, Carbon $end_time)
    {
        $query = DB::table('sessions')
            ->where('SSHN_DCTR_ID', $this->id)
            ->whereDate('SSHN_DATE', $start_time->format('Y-m-d'))
            ->where(function ($query) use ($start_time, $end_time) {
                $query->where(function ($query) use ($start_time) {
                    $query->where('SSHN_STRT_TIME', "<=", $start_time->format('H:i'))
                        ->where('SSHN_END_TIME', ">", $start_time->format('H:i'));
                })->orWhere(function ($query) use ($end_time) {
                    $query->where('SSHN_STRT_TIME', "<", $end_time->format('H:i'))
                        ->where('SSHN_END_TIME', ">=", $end_time->format('H:i'));
                });
            })
            ->selectRaw('COUNT(*) as sessions_count');
            
            return $query->first()->sessions_count == 0;
    }

    /////model functions     
    public function getAuthPassword()
    {
        return $this->DASH_PASS;
    }

    /**
     * @return int Branch ID or zero for universal users
     */
    public function getBranchValue(): int
    {
        return $this->DASH_BRCH_ID ?? (session('branch', 0));
    }

    ///////relations
    public function dash_types()
    {
        return $this->hasOne("App\Models\DashType", 'id', 'DASH_TYPE_ID');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'DASH_BRCH_ID');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(DoctorAvailability::class, "DCAV_DASH_ID");
    }

    public function session_types(): BelongsToMany
    {
        return $this->belongsToMany(SessionType::class, 'session_types_doctors', 'SHTD_DASH_ID', 'SHTD_SHTP_ID');
    }

    public function isMultiBranch()
    {
        return $this->DASH_BRCH_ID == NULL;
    }

    public function canAdmin()
    {
        return ($this->DASH_TYPE_ID == 1 || $this->DASH_TYPE_ID == 3);
    }

    public function isAdmin()
    {
        return ($this->DASH_TYPE_ID == 1);
    }

    public function isDoctor()
    {
        return ($this->DASH_TYPE_ID == 2);
    }

    public function isOwner()
    {
        return ($this->DASH_TYPE_ID == 3);
    }

    public function toggle()
    {
        $this->DASH_ACTV = ($this->DASH_ACTV + 1) % 2;
        $this->save();
    }

    public static function owners()
    {
        return self::where("DASH_TYPE_ID", 3)->get();
    }

    public static function admins()
    {
        return self::where("DASH_TYPE_ID", 1)->get();
    }

    public static function doctors()
    {
        return self::where("DASH_TYPE_ID", 2)->get();
    }
}
