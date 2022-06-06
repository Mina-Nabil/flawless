<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;

    function patients()
    {
        return $this->hasMany(Patient::class, "PTNT_LOCT_ID");
    }

    function UnlinkAnddelete()
    {
        try {
            $this->patients()->update([
                "PTNT_LOCT_ID" => NULL
            ]);
            $this->delete();
        } catch (Exception $e) {
            report($e);
        }
    }
}
