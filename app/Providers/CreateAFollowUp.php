<?php

namespace App\Providers;

use App\Providers\SessionClosed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateAFollowUp
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SessionClosed  $event
     * @return void
     */
    public function handle(SessionClosed $event)
    {
        $event->session->loadMissing("patient");
        $event->session->patient->createAFollowup();
    }
}
