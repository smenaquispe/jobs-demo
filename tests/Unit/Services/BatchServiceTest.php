<?php

use App\Services\BatchService;
use App\Jobs\JobA;
use App\Jobs\FailureJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Bus::fake();
    Queue::fake();
    $this->service = app(BatchService::class);
});

it('dispatches a batch of jobs', function () {
    $jobNames = ['JobA', 'JobA'];
    $queue = 'default';

    $batch = $this->service->dispatchBatch($jobNames, $queue);

    expect($batch)->toBeInstanceOf(Batch::class);

    Bus::assertBatchCount(1);
});

it('cancels an existing batch', function () {
    // Fake a batch and retrieve its ID
    $batch = Bus::batch([])->dispatch();
    $batchId = $batch->id;

    // Cancel the batch
    $response = $this->service->cancelBatch($batchId);

    expect($response['message'])->toBe('Batch cancelled');
    expect($response['batchId'])->toBe($batchId);

    expect($batch->cancelled())->toBeTrue();
});

it('handles cancellation of a non-existing batch', function () {
    $nonExistentBatchId = 'fake-id';

    $response = $this->service->cancelBatch($nonExistentBatchId);

    expect($response['message'])->toBe('Batch not found');
    expect($response['batchId'])->toBe($nonExistentBatchId);
});

it('retrieves multiple batches', function () {
    // Fake some batches
    $batch1 = Bus::batch([])->dispatch();
    $batch2 = Bus::batch([])->dispatch();
    $batchIds = [$batch1->id, $batch2->id];

    $batches = $this->service->getBatches($batchIds);

    expect($batches)->toHaveCount(2);
    expect($batches[0]->id)->toBe($batch1->id);
    expect($batches[1]->id)->toBe($batch2->id);
});

it('dispatches a batch including a failing job', function () {
    $queue = 'default';

    $batch = $this->service->dispatchBatchWithFailure($queue);

    expect($batch)->toBeInstanceOf(Batch::class);
});
