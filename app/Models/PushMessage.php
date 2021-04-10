<?php

namespace App\Models;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class PushMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $from;
    public $toID;

    public function __construct($toID, $fromName, $message)
    {
        $this->toID = $toID;
        $this->from = $fromName;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return ['flawless-channel'];
    }

    public function broadcastAs()
    {
        return 'my-event-' . $this->toID;
    }
}
