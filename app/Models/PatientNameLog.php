<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientNameLog extends Model
{
    protected $table = "patient_name_logs";
    protected $fillable = ["PNML_PTNT_ID", "PNML_FROM", "PNML_TO", "PNML_DASH_ID"];

    //relations
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, "PNML_PTNT_ID");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, "PNML_DASH_ID");
    }
}
