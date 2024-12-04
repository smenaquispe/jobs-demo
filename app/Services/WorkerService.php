<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class WorkerService
{
    public function getWorkersStatus()
    {
        $workers = Redis::hgetall('horizon:workers');
        dd($workers); // Muestra los datos directamente
        $workers = Redis::hgetall('horizon:workers');

        $workerStatuses = [];
        foreach ($workers as $id => $status) {
            $workerStatuses[] = [
                'id' => $id,
                'status' => json_decode($status, true) // Decode the JSON string to an array
            ];
        }

        return $workerStatuses;
    }

    public function getJobsInProgress()
    {
        // Definir las colas que quieres monitorear
        $queues = ['default', 'queue2', 'queue3']; // Puedes agregar más colas aquí
        $jobsInProgress = [];

        // Iterar sobre las colas y verificar las listas de Redis
        foreach ($queues as $queue) {
            $jobsInQueue = Redis::lrange('queues:' . $queue, 0, -1); // Obtener trabajos en la cola

            if (count($jobsInQueue) > 0) {
                foreach ($jobsInQueue as &$job) {
                    $job = json_decode($job, true);
                }

                $jobsInProgress[$queue] = $jobsInQueue; // Decodificar los trabajos en JSON
            }
        }

        return $jobsInProgress;
    }
}
