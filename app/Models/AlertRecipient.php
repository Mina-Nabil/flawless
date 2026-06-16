<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertRecipient extends Model
{
    protected $table = "alert_recipients";
    protected $fillable = ["ALRC_ALRT_ID", "ALRC_DASH_ID", "ALRC_READ_AT"];
    protected $casts = [
        "ALRC_READ_AT" => "datetime",
    ];

    //relations
    public function alert(): BelongsTo
    {
        return $this->belongsTo(AlertMessage::class, "ALRC_ALRT_ID");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, "ALRC_DASH_ID");
    }
}
