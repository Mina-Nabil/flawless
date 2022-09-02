<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SessionType extends Model
{
    protected $table = 'session_types';
    protected $with = ['doctors'];
    public $timestamps = false;

    /////static functions and scopes
    public static function newSessionType(string $name, int $duration, array $doctors = [], string $desc = null): self
    {
        $newSessionType = new self;
        $newSessionType->SHTP_NAME = $name;
        $newSessionType->SHTP_DUR = $duration;
        $newSessionType->SHTP_DESC = $desc;
        $newSessionType->save();
        $newSessionType->doctors()->sync($doctors);
        return $newSessionType;
    }


    ///////model actions
    public function updateInfo(string $name, int $duration, array $doctors = [], string $desc = null): bool
    {
        $this->SHTP_NAME = $name;
        $this->SHTP_DUR = $duration;
        $this->SHTP_DESC = $desc;
        $this->doctors()->sync($doctors);
        return $this->save();
    }

    public function setState(bool $newState) : bool 
    {
        $this->SHTP_ACTV = $newState;
        return $this->save();
    }


    ////relations
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(DashUser::class, 'session_types_doctors', 'SHTD_SHTP_ID', 'SHTD_DASH_ID');
    }
}
