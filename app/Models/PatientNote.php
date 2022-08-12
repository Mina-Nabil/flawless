<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientNote extends Model
{
    protected $table = 'patient_notes';
    protected $fillable = ['PNOT_NOTE', 'PNOT_DASH_ID', 'PNOT_PTNT_ID'];
    protected $with = ['user'];
    //////relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, 'PNOT_DASH_ID');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PNOT_PTNT_ID');
    }
}
