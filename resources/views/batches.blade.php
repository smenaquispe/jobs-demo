<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.11.0/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select, .btn {
            padding: 8px;
            font-size: 14px;
        }

        .btn {
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-add {
            background-color: #007bff;
            color: white;
        }

        .btn-dispatch {
            background-color: #28a745;
            color: white;
        }

        .btn-cancel {
            background-color: #ff4d4f;
            color: white;
        }

        .btn-remove {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            font-size: 12px;
        }

        .btn-disabled {
            background-color: #bfbfbf;
            cursor: not-allowed;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .progress-bar {
            background-color: #e5e5e5;
            height: 10px;
            position: relative;
        }

        .progress-bar-filled {
            background-color: #4caf50;
            height: 100%;
        }

        .bg-cancelled {
            background-color: #fde2e2;
        }
    </style>
</head>
<body>
    <div x-data="batchApp()" x-init="startPolling()">
        <h1 class="text-xl font-bold mb-4">Batch Manager</h1>

        <!-- Formulario de selección de jobs y queues -->
        <div class="form-group">
            <label for="jobSelect">Select Job:</label>
            <select x-model="selectedJob" id="jobSelect">
                <option value="JobA">JobA</option>
                <option value="JobB">JobB</option>
            </select>
        </div>

        <div class="form-group">
            <label for="queueSelect">Select Queue:</label>
            <select x-model="selectedQueue" id="queueSelect">
                <option value="default">default</option>
                <option value="queue2">queue2</option>
                <option value="queue3">queue3</option>
            </select>
        </div>

        <!-- Botón para añadir trabajos -->
        <button @click="addJobToList" class="btn btn-add">Add Job</button>

        <!-- Lista de trabajos seleccionados -->
        <div class="form-group">
            <ul>
                <template x-for="(job, index) in jobs" :key="index">
                    <li>
                        <span x-text="job"></span>
                        <button @click="removeJob(index)" class="btn btn-remove ml-2">Remove</button>
                    </li>
                </template>
            </ul>
        </div>

        <!-- Botón para despachar batch -->
        <button @click="dispatchBatch" class="btn btn-dispatch">Dispatch Batch</button>

        <!-- Tabla de batches -->
        <div x-show="batchProgress.length > 0">
            <table>
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Total Jobs</th>
                        <th>Processed Jobs</th>
                        <th>Progress</th>
                        <th>Pending Jobs</th>
                        <th>Cancelled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(batch, index) in batchProgress" :key="batch.id">
                        <tr :class="batch.cancelledAt ? 'bg-cancelled' : ''">
                            <td x-text="batch.id"></td>
                            <td x-text="batch.totalJobs"></td>
                            <td x-text="batch.processedJobs"></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-bar-filled" :style="`width: ${batch.progress}%`"></div>
                                </div>
                                <span x-text="`${batch.progress}%`"></span>
                            </td>
                            <td x-text="batch.pendingJobs"></td>
                            <td x-text="batch.cancelledAt ? 'Yes' : 'No'"></td>
                            <td>
                                <button 
                                    :class="batch.cancelledAt ? 'btn btn-disabled' : 'btn btn-cancel'"
                                    :disabled="batch.cancelledAt"
                                    @click="cancelBatch(batch.id)">
                                    Cancel
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function batchApp() {
            return {
                selectedJob: 'JobA',
                selectedQueue: 'default',
                jobs: [],
                batchIds: [],
                batchProgress: [],
                intervalId: null,  // ID del intervalo para la consulta continua

                // Agregar trabajo a la lista
                addJobToList() {
                    this.jobs.push(this.selectedJob);
                },

                // Eliminar trabajo de la lista
                removeJob(index) {
                    this.jobs.splice(index, 1);
                },

                // Despachar un batch
                async dispatchBatch() {
                    if (this.jobs.length === 0) {
                        alert('Please add at least one job.');
                        return;
                    }

                    const url = `/api/dispatch-batch?jobNames=${encodeURIComponent(JSON.stringify(this.jobs))}&queue=${encodeURIComponent(this.selectedQueue)}`;
                    try {
                        const response = await fetch(url, { method: 'GET' });
                        const data = await response.json();
                        this.fetchBatchProgress();
                        this.batchIds.push(data.batchId);  // Agregar el ID del batch a la lista
                        this.jobs = []; // Limpiar la lista de trabajos
                    } catch (error) {
                        console.error('Error dispatching batch:', error);
                    }
                },

                // Cancelar un batch
                async cancelBatch(batchId) {
                    try {
                        const response = await fetch(`/api/cancel-batch?batchId=${batchId}`, { method: 'GET' });
                        const data = await response.json();
                        console.log('Batch cancelled:', data);
                        this.fetchBatchProgress();
                    } catch (error) {
                        console.error('Error cancelling batch:', error);
                    }
                },

                startPolling() {
                    // Llama a la función de obtención de progreso inmediatamente
                    this.fetchBatchProgress();

                    // Establece un intervalo para actualizar los datos cada 5 segundos
                    setInterval(() => {
                        this.fetchBatchProgress();
                    }, 500); // 5000 ms = 5 segundos
                },

                // Obtener progreso de los batches
                async fetchBatchProgress() {
                    url = '/api/get-batches/?batchIds=' + encodeURIComponent(JSON.stringify(this.batchIds));
                    try {
                        const response = await fetch(url, { method: 'GET' });
                        const data = await response.json();
                        this.batchProgress = data.batches || [];
                    } catch (error) {
                        console.error('Error fetching batch progress:', error);
                    }
                }
            };
        }
    </script>
</body>
</html>
