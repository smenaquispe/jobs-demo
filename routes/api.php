<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ChainController;

Route::get('dispatch-job', [JobsController::class, 'dispatchJobAInDefaultQueue']);
Route::get('dispatch-job-queue2', [JobsController::class, 'dispatchJobAInQueue']);

Route::get('jobs-in-progress', [WorkerController::class, 'getJobsInProgress']);
Route::get('workers-status', [WorkerController::class, 'getWorkersStatus']);
Route::get('dispatch-any-job', [JobsController::class, 'dispatchAnyJobOnAnyQueue']);
Route::get('dispatch-custom-job', [JobsController::class, 'dispatchCustomJob']);
Route::get('dispatch-failure-job', [JobsController::class, 'dispatchFailureJob']);
Route::get('dispatch-auto-release-job', [JobsController::class, 'dispatchAutoReleaseJob']);

// Batch
Route::get('dispatch-batch', [BatchController::class, 'dispatchBatch']);
Route::get('cancel-batch', [BatchController::class, 'cancelBatch']);
Route::get('get-batches', [BatchController::class, 'getBatches']);
Route::get('dispatch-batch-with-failure', [BatchController::class, 'dispatchBatchWithFailure']);
// Chain
Route::get('dispatch-chain', [ChainController::class, 'dispatchChain']);
Route::get('dispatch-chain-with-failure', [ChainController::class, 'dispatchChainWithFailure']);



Route::get('dispatch-in-remote-connection', [JobsController::class, 'dispatchInRemoteConnection']);
