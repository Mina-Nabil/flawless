<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DayNote extends Model
{
    protected $with = ['user'];

    public static function loadNotes(Carbon $start_date, Carbon $end_date, $roomID = null)
    {
        $query = self::whereDate('ADNT_DATE', '>=', $start_date->format('Y-m-d'))
            ->where('ADNT_DATE', '<=', $end_date->format('Y-m-d'));
        if ($roomID) {
            $query->where(
                function ($query) use ($roomID) {
                    $query->where('ADNT_ROOM_ID', $roomID)
                        ->orWhereNull('ADNT_ROOM_ID');
                }
            );
        } else {
            $query->whereNull('ADNT_ROOM_ID');
        }
        return $query->get();
    }

    public function updateInfo(string $title, Carbon $date, $roomID = null, string $note = null)
    {
        $this->ADNT_TTLE = $title;
        $this->ADNT_DATE = $date->format('Y-m-d');
        $this->ADNT_NOTE = $note;
        $this->ADNT_ROOM_ID = $roomID;
        $this->ADNT_DASH_ID = Auth::user()->id;

        return $this->save();
    }


    public static function newNote(string $title, Carbon $date, $roomID = null, string $note = null): self
    {
        $newNote = new self;
        $newNote->ADNT_TTLE = $title;
        $newNote->ADNT_DATE = $date->format('Y-m-d');
        $newNote->ADNT_NOTE = $note;
        $newNote->ADNT_ROOM_ID = $roomID;
        $newNote->ADNT_DASH_ID = Auth::user()->id;

        $newNote->save();
        return $newNote->fresh();
    }


    ////relations
    public function user()
    {
        return $this->belongsTo(DashUser::class, "ADNT_DASH_ID");
    }
}
