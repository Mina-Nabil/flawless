<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PatientNote extends Model
{
    use SoftDeletes;
    protected $table = 'patient_notes';
    protected $fillable = ['PNOT_NOTE', 'PNOT_DASH_ID', 'PNOT_PTNT_ID'];
    protected $with = ['user'];

    //////model functions
    public function deleteNote(): bool
    {
        try {
            /** @var DashUser */
            $loggedIn = Auth::user();
            if ($loggedIn->isOwner()) {
                return $this->forceDelete();
            } else
                return $this->delete();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

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
