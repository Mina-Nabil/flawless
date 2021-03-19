<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "log";
    public $timestamps = true;

    protected $fillable = [
        'LOG_DASH_ID', 'LOG_TEXT'
    ];

    public function user(){
        return $this->belongsTo(DashUser::class, "LOG_DASH_ID");
    }
}
