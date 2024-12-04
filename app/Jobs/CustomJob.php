<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CustomJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $time)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('CustomJob started in queue: ' . $this->queue);
        sleep($this->time);
        info('CustomJob executed successfully in queue: ' . $this->queue);
    }
}
