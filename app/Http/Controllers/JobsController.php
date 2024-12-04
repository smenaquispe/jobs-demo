<?php

namespace App\Http\Controllers;

use App\Services\JobsService;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct(protected JobsService $jobsService) {}

    public function dispatchJobAInDefaultQueue(Request $request)
    {
        $this->jobsService->dispatchJobAInDefaultQueue();

        return response()->json(['message' => 'JobA dispatched successfully']);
    }

    public function dispatchJobAInQueue(Request $request)
    {
        $this->jobsService->dispatchJobAInQueue();

        return response()->json(['message' => 'JobB dispatched successfully']);
    }
}
