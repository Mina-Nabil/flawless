<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public $timestamps = false;

    function patients()
    {
        return $this->hasMany(Patient::class, "PTNT_CHNL_ID");
    }

    function UnlinkAnddelete()
    {
        try {
            $this->patients()->update([
                "PTNT_CHNL_ID" => NULL
            ]);
            $this->delete();
        } catch (Exception $e) {
        }
    }
}
