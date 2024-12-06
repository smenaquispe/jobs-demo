<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class AutoReleaseJob implements ShouldQueue
{
    use Dispatchable, Queueable, Batchable;

    public $tries = 3;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('Job AutoReleaseJob started in queue: ' . $this->queue);
        sleep(3);
        $this->release(10);
    }
}
