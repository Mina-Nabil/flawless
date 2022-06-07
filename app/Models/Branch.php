<?php

namespace App\Models;

use App\Models\Attendance;
use App\Models\Cash;
use App\Models\DashUser;
use App\Models\Session;
use App\Models\Visa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Branch extends Model
{

    public $timestamps = false;

    /////static query    
    public static function getCurrentBranch(): int
    {
        /** @var DashUser */
        $loggedUser = Auth::user();
        if ($loggedUser)
            return $loggedUser->getBranchValue();
        else return null;
    }

    /////relations
    public function cash(): HasMany
    {
        return $this->hasMany(Cash::class, "CASH_BRCH_ID");
    }
    public function visa(): HasMany
    {
        return $this->hasMany(Visa::class, "VISA_BRCH_ID");
    }
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, "ATND_BRCH_ID");
    }
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, "SSHN_BRCH_ID");
    }
    public function users(): HasMany
    {
        return $this->hasMany(DashUser::class, "DASH_BRCH_ID");
    }
}
