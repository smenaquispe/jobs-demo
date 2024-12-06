<?php

use App\Jobs\FailureJob;
use Illuminate\Support\Facades\Queue;

it('dispatches the job and retries on failure', function () {
    Queue::fake();
    FailureJob::dispatch();

    Queue::assertPushed(FailureJob::class, function (FailureJob $job) {
        return $job->tries === 3 && $job->backoff === 5;
    });
});

it('fails when the job is processed', function () {
    Queue::fake();

    $job = new FailureJob();

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Job failed');

    $job->handle();
});
