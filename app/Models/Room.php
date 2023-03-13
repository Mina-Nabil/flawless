<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $table = 'rooms';
    public $timestamps = false;
    protected $with = ['branch'];

    ///////static queries
    public static function newRoom(int $branchID, string $name, string $desc = null): self
    {
        $newRoom = new self;
        $newRoom->ROOM_BRCH_ID = $branchID;
        $newRoom->ROOM_NAME = $name;
        $newRoom->ROOM_DESC = $desc;
        $newRoom->save();
        return $newRoom;
    }

    public function scopeByBranch($query, $branchID)
    {
        if ($branchID < 1) {
            return $query;
        } else {
            return $query->where('ROOM_BRCH_ID', $branchID);
        }
    }

    /////////model actions
    public function updateInfo(int $branchID, string $name, string $desc = null): bool
    {
        $this->ROOM_BRCH_ID = $branchID;
        $this->ROOM_NAME = $name;
        $this->ROOM_DESC = $desc;
        return $this->save();
    }

    public function toggleState(): bool
    {
        $this->ROOM_ACTV = !$this->ROOM_ACTV;
        return $this->save();
    }

    /////////relations
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'ROOM_BRCH_ID');
    }
}
