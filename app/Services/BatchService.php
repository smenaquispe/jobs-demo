<?php

namespace App\Services;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class BatchService
{
    public function dispatchBatch($jobNames, $queue)
    {
        $jobs = [];
        foreach ($jobNames as $jobName) {
            $jobs[] = app()->make('App\\Jobs\\' . $jobName);
        }

        $batch = Bus::batch($jobs)->onQueue($queue)->dispatch();
        return $batch;
    }

    public function cancelBatch($batchId)
    {
        $batch = Bus::findBatch($batchId);
        if (!$batch) {
            return ['message' => 'Batch not found', 'batchId' => $batchId];
        }
        $batch->cancel();
        if ($batch->cancelled()) {
            return ['message' => 'Batch cancelled', 'batchId' => $batchId];
        }
        return ['message' => 'Batch not cancelled', 'batchId' => $batchId];
    }

    public function getBatches($batchIds)
    {
        $batches = [];
        foreach ($batchIds as $batchId) {
            $batch = Bus::findBatch($batchId);
            $batches[] = $batch;
        }
        return $batches;
    }
}
