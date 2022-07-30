<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPayment extends Model
{
    public function dash_user(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, 'PTPY_DASH_ID');
    }
}
