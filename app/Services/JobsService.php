<?php

namespace App\Services;

use App\Jobs\JobA;
use App\Jobs\JobB;

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
}
