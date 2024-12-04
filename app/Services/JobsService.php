<?php

namespace App\Services;

use App\Jobs\JobA;
use App\Jobs\JobB;
use App\Jobs\CustomJob;
use Illuminate\Contracts\Queue\Job;

class JobsService
{
    public function dispatchJobAInDefaultQueue()
    {
        dispatch(new JobA())->onQueue('default');
    }

    public function dispatchJobAInQueue()
    {
        dispatch(new JobB())->onQueue('queue2');
    }

    public function dispatchAnyJobOnAnyQueue(string $jobName, string $queue)
    {
        $job = app()->make('App\\Jobs\\' . $jobName);
        dispatch($job)->onQueue($queue);
    }

    public function dispatchCustomJob(string $queue, int $time)
    {
        dispatch(new CustomJob($time))->onQueue($queue);
    }
}
