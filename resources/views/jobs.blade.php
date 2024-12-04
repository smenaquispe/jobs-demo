<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine Jobs Demo</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.11.0/dist/cdn.min.js" defer></script>
</head>
<body>

    <style>
        .job {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: transform 0.2s ease-in-out;
        }
    
        .new-job {
            background-color: #d4edda; /* Verde claro */
        }
    
        .removed-job {
            background-color: #f8d7da; /* Rojo claro */
            opacity: 0.6;
            transition: opacity 1s ease-out;
        }
    
        .active-job {
            background-color: #cce5ff; /* Azul claro */
        }
    
        .job:hover {
            transform: scale(1.05);
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
        }
    
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    
        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
    
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
    
        tr:hover {
            background-color: #e2e8f0;
        }
    
        .button {
            display: inline-block;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: #4CAF50;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
    
        .button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
    
        .button-blue {
            background-color: #007bff;
        }
    
        .button-blue:hover {
            background-color: #0056b3;
        }
    
        .message {
            padding: 12px;
            margin-top: 20px;
            border-radius: 8px;
            background-color: #f1f5f9;
            color: #4B5563;
        }
    </style>
    
    <div x-data="jobDispatcher()" class="p-8">
        <h1 class="text-2xl font-semibold mb-4">Despachar Jobs en Diferentes Colas</h1>
        
        <!-- Selector para elegir la cola -->
        <div class="mb-4">
            <label for="queue" class="block text-sm font-medium text-gray-700">Selecciona una Cola</label>
            <select id="queue" x-model="selectedQueue" class="mt-2 p-2 border rounded-lg w-full">
                <option value="default">Default</option>
                <option value="queue2">Queue2</option>
                <option value="queue3">Queue3</option>
            </select>
        </div>
    
        <!-- Botón para despachar JobA en la cola seleccionada -->
        <button @click="dispatchJob('api/dispatch-any-job?job=JobA&queue=' + selectedQueue)" class="button button-blue">
            Despachar JobA
        </button>
        
        <!-- Botón para despachar JobB en la cola seleccionada -->
        <button @click="dispatchJob('api/dispatch-any-job?job=JobB&queue=' + selectedQueue)" class="button mt-4">
            Despachar JobB
        </button>
    
        <!-- Respuesta -->
        <div x-show="responseMessage" class="message mt-4">
            <p x-text="responseMessage"></p>
        </div>
    </div>

    <!-- Segundo bloque: despachar un Job personalizado -->
    <div x-data="customJobDispatcher()" class="p-8">
        <h1 class="text-2xl font-semibold mb-4">Despachar Job Personalizado</h1>
        
        <!-- Selector de cola -->
        <select x-model="selectedQueue" class="border p-2 rounded">
            <option value="default">Default</option>
            <option value="queue2">Queue2</option>
            <option value="queue3">Queue3</option>
        </select>
        
        <!-- Selector de tiempo -->
        <input x-model="jobTime" type="number" class="border p-2 rounded" placeholder="Tiempo (en segundos)" />

        <!-- Botón para despachar el job personalizado -->
        <button @click="dispatchCustomJob" class="button button-blue mt-4">
            Despachar Job Personalizado
        </button>

        <!-- Respuesta -->
        <div x-show="responseMessage" class="message mt-4">
            <p x-text="responseMessage"></p>
        </div>
    </div>
    
    
    <div x-data="jobsPeding()" class="p-4">
        <h2 class="text-lg font-semibold mb-4">Jobs Pendientes</h2>
    
        <template x-if="loading">
            <p class="text-gray-500">Cargando jobs...</p>
        </template>
    
        <template x-if="!loading && Object.keys(jobs).length > 0">
            <div>
                <template x-for="(queueJobs, queueName) in jobs" :key="queueName">
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-blue-700" x-text="'Cola: ' + queueName"></h3>
                        <ul class="list-none pl-4">
                            <template x-for="job in queueJobs" :key="job.id">
                                <li :class="['job', job.status]"
                                    x-text="`${job.id} (Intentos: ${job.attempts})`">
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>
        </template>
    
        <template x-if="!loading && Object.keys(jobs).length === 0">
            <p class="text-gray-500">No hay jobs pendientes actualmente.</p>
        </template>
    </div>
    
    <div x-data="jobsInProgress()" class="p-4">
        <h3 class="text-lg font-semibold mb-4">Jobs en ejecución</h3>
        <table class="shadow-lg rounded-lg">
            <thead>
                <tr class="bg-gray-100">
                    <th class="text-left px-6 py-3">ID</th>
                    <th class="text-left px-6 py-3">Cola</th>
                    <th class="text-left px-6 py-3">Nombre del Trabajo</th>
                    <th class="text-left px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="job in jobs" :key="job.id">
                    <tr>
                        <td class="px-6 py-4 text-gray-700" x-text="job.id"></td>
                        <td class="px-6 py-4 text-gray-700" x-text="job.queue"></td>
                        <td class="px-6 py-4 text-gray-700" x-text="job.name"></td>
                        <td class="px-6 py-4 text-gray-700" x-text="job.status"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    

    <script>
            function jobDispatcher() {
            return {
                selectedQueue: 'default', // Valor predeterminado de la cola seleccionada
                responseMessage: '',
                async dispatchJob(route) {
                    try {
                        const response = await fetch(route, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                        });

                        const data = await response.json();
                        this.responseMessage = data.message;
                    } catch (error) {
                        this.responseMessage = 'Error al despachar el job';
                    }
                }
            }
        }

        
        function customJobDispatcher() {
            return {
                responseMessage: '',
                selectedQueue: 'default', // Cola predeterminada
                jobTime: 0, // Tiempo por defecto

                async dispatchCustomJob() {
                    const route = `api/dispatch-custom-job?queue=${this.selectedQueue}&time=${this.jobTime}`;
                    try {
                        const response = await fetch(route, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                        });

                        const data = await response.json();
                        this.responseMessage = data.message;
                    } catch (error) {
                        this.responseMessage = 'Error al despachar el job personalizado';
                    }
                }
            };
        }

        function jobsPeding() {
            return {
                jobs: {}, // Objeto para almacenar los jobs actuales
                loading: true,
                previousJobs: {}, // Almacena los jobs en la iteración anterior
                async fetchJobs() {
                    try {
                        const response = await fetch('/api/jobs-in-progress');
                        if (response.ok) {
                            const newJobs = await response.json();

                            // Comparar con previousJobs para asignar estados
                            for (const queue in newJobs) {

                                if (!this.previousJobs[queue]) this.previousJobs[queue] = [];

                                const currentQueueJobs = newJobs[queue];
                                const previousQueueJobs = this.previousJobs[queue];

                                // Marcar nuevos jobs
                                currentQueueJobs.forEach(job => {
                                    const previousJob = previousQueueJobs.find(prev => prev.id === job.id);
                                    job.status = previousJob ? 'active-job' : 'new-job';
                                });

                                // Marcar jobs eliminados
                                previousQueueJobs.forEach(job => {
                                    const stillExists = currentQueueJobs.find(current => current.id === job.id);
                                    if (!stillExists) {
                                        job.status = 'removed-job';
                                        currentQueueJobs.push(job); // Mostrar momentáneamente el job eliminado
                                    }
                                });

                                // Actualizar el estado de la cola
                                newJobs[queue] = currentQueueJobs;
                            }

                            this.jobs = newJobs;
                            this.previousJobs = JSON.parse(JSON.stringify(newJobs)); // Copiar estado actual como referencia futura
                        } else {
                            console.error('Error al obtener los jobs.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                init() {
                    this.fetchJobs();
                    setInterval(() => this.fetchJobs(), 500);
                }
            };
        }

        function jobsInProgress() {
            return {
                jobs: [],
                async fetchJobs() {
                    try {
                        const response = await fetch('/horizon/api/jobs/pending');
                        if (response.ok) {
                            const data = await response.json();
                            this.jobs = data.jobs;
                        } else {
                            console.error('Error al obtener los trabajos pendientes.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                init() {
                    this.fetchJobs();
                    setInterval(() => this.fetchJobs(), 500);
                }
            };
        }

    </script>
</body>
</html>
