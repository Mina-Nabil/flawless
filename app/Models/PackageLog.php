<?php

namespace App\Models;

use App\Models\DashUser;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageLog extends Model
{
    protected $table = 'packages_logs';
    protected $fillable = ['PKLG_TTLE', 'PKLG_AMNT', 'PKLG_DASH_ID', 'PKLG_PTNT_ID', 'PKLG_CMNT', 'PKLG_SSHN_ID'];
    protected $with = ['user'];
    //////relations
    public function user():BelongsTo
    {
        return $this->belongsTo(DashUser::class, 'PKLG_DASH_ID');
    }

    public function patient():BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PKLG_PTNT_ID');
    }
}
