<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BatchService;

class BatchController extends Controller
{
    public function __construct(protected BatchService $batchService) {}

    public function dispatchBatch(Request $request)
    {
        // Validar que 'jobNames' sea un array de strings

        $jobNames = json_decode($request->query('jobNames', '[]'));  // Decodificar el JSON de los trabajos
        $queue = $request->query('queue', 'default');  // Obtener la cola

        // Procesar el batch
        $batch = $this->batchService->dispatchBatch($jobNames, $queue);

        // Devolver la respuesta con el ID del batch
        return response()->json([
            'batchId' => $batch->id,
        ]);
    }


    public function cancelBatch(Request $request)
    {
        // Obtener el batchId de los parámetros GET
        $batchId = $request->query('batchId');  // También puedes usar $request->get('batchId')

        // Cancelar el batch
        $res = $this->batchService->cancelBatch($batchId);

        // Devolver respuesta
        return response()->json($res);
    }

    public function getBatches(Request $request)
    {
        $batchIds = json_decode($request->query('batchIds', '[]'));  // Decodificar el JSON de los IDs de batches

        // Obtener los datos del batch
        $batches = $this->batchService->getBatches($batchIds);

        // Devolver respuesta con los datos del batch
        return response()->json([
            'batches' => $batches,
        ]);
    }

    public function dispatchBatchWithFailure(Request $request)
    {
        $queue = $request->query('queue', 'default');  // Obtener la cola

        // Procesar el batch
        $batch = $this->batchService->dispatchBatchWithFailure($queue);

        // Devolver la respuesta con el ID del batch
        return response()->json([
            'batchId' => $batch->id,
        ]);
    }
}
