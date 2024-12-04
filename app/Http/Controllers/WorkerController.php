<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WorkerService;

class WorkerController extends Controller
{
    public function __construct(protected WorkerService $workerService) {}

    public function getWorkersStatus(Request $request)
    {
        return response()->json($this->workerService->getWorkersStatus());
    }

    public function getJobsInProgress(Request $request)
    {
        return response()->json($this->workerService->getJobsInProgress());
    }
}
