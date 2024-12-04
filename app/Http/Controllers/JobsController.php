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

    public function dispatchAnyJobOnAnyQueue(Request $request)
    {
        $validated = $request->validate([
            'job' => 'required|string|in:JobA,JobB',
            'queue' => 'required|string|in:default,queue2,queue3', // Validar que la cola esté permitida
        ]);

        // Obtener los datos de la solicitud
        $job = $validated['job'];
        $queue = $validated['queue'];

        $this->jobsService->dispatchAnyJobOnAnyQueue($job, $queue);

        return response()->json(['message' => 'Job dispatched successfully']);
    }

    public function dispatchCustomJob(Request $request)
    {

        $validated = $request->validate([
            'queue' => 'required|string|in:default,queue2,queue3', // Validar que la cola esté permitida
            'time' => 'required|integer|min:1', // Validar que el tiempo sea un número positivo
        ]);

        $queue = $validated['queue'];
        $time = $validated['time'];

        $this->jobsService->dispatchCustomJob($queue, $time);

        return response()->json(['message' => 'Custom job dispatched successfully']);
    }
}
