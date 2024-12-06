<?php

namespace App\Services;

use App\Jobs\FailureJob;
use App\Jobs\JobA;
use Illuminate\Bus\Chain;
use Illuminate\Support\Facades\Bus;

class ChainService
{
    public function dispatchChain($jobNames, $queue)
    {
        $jobs = [];
        foreach ($jobNames as $jobName) {
            $jobs[] = app()->make('App\\Jobs\\' . $jobName);
        }

        $chain = Bus::chain($jobs)->onQueue($queue);

        $jobsToDispatch = $chain;

        $chain->dispatch();

        return $jobsToDispatch;
    }

    public function dispatchChainWithFailure($queue)
    {
        $jobs = [
            new JobA(),
            new FailureJob(),
            new JobA(),
        ];

        $chain = Bus::chain($jobs)->onQueue($queue);

        $jobsToDispatch = $chain;

        $chain->dispatch();

        return $jobsToDispatch;
    }
}
