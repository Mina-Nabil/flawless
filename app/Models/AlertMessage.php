<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlertMessage extends Model
{
    protected $table = "alert_messages";
    protected $fillable = ["ALRT_TEXT", "ALRT_DASH_ID", "ALRT_ACTV", "ALRT_EXPR"];
    protected $casts = [
        "ALRT_ACTV" => "boolean",
        "ALRT_EXPR" => "datetime",
    ];

    //relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, "ALRT_DASH_ID");
    }

    public function recipientRows(): HasMany
    {
        return $this->hasMany(AlertRecipient::class, "ALRC_ALRT_ID");
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(DashUser::class, "alert_recipients", "ALRC_ALRT_ID", "ALRC_DASH_ID")
            ->withPivot("ALRC_READ_AT")
            ->withTimestamps();
    }

    /**
     * Active, non-expired alerts that the given user has not yet confirmed reading.
     * Used by the bottom banner on every page.
     */
    public static function unreadFor($userId)
    {
        return self::where("ALRT_ACTV", 1)
            ->where(function ($q) {
                $q->whereNull("ALRT_EXPR")->orWhere("ALRT_EXPR", ">=", now());
            })
            ->whereHas("recipientRows", function ($q) use ($userId) {
                $q->where("ALRC_DASH_ID", $userId)->whereNull("ALRC_READ_AT");
            })
            ->orderByDesc("id")
            ->get();
    }
}
