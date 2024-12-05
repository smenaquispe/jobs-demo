<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChainService;

class ChainController extends Controller
{
    public function __construct(protected ChainService $chainService) {}

    public function dispatchChain(Request $request)
    {
        // Validar que 'jobNames' sea un array de strings

        $jobNames = json_decode($request->query('jobNames', '[]'));  // Decodificar el JSON de los trabajos
        $queue = $request->query('queue', 'default');  // Obtener la cola

        // Procesar el chain
        $chain = $this->chainService->dispatchChain($jobNames, $queue);

        // Devolver la respuesta con el ID del chain
        return response()->json([
            'chain' => $chain,
        ]);
    }
}
