<?php

namespace App\Jobs;

use App\Helpers\SmsHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;
    protected $mode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session, $mode)
    {
        $this->session = $session;
        $this->mode = $mode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SmsHandler::sendSessionMessage($this->session, $this->mode);
    }
}
