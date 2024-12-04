<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\WorkerController;

Route::get('dispatch-job', [JobsController::class, 'dispatchJobAInDefaultQueue']);
Route::get('dispatch-job-queue2', [JobsController::class, 'dispatchJobAInQueue']);

Route::get('jobs-in-progress', [WorkerController::class, 'getJobsInProgress']);
Route::get('workers-status', [WorkerController::class, 'getWorkersStatus']);
Route::get('dispatch-any-job', [JobsController::class, 'dispatchAnyJobOnAnyQueue']);
Route::get('dispatch-custom-job', [JobsController::class, 'dispatchCustomJob']);
