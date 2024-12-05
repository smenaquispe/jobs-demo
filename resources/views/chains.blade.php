<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chains Dispatch</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.11.0/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333333;
        }
        label {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: #4a5568;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 1rem;
        }
        select:focus, button:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .list-item button {
            background-color: transparent;
            color: #e53e3e;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .list-item button:hover {
            color: #c53030;
        }
        .response-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #e6fffa;
            color: #2c7a7b;
            border-radius: 8px;
            font-family: "Courier New", monospace;
        }
        .response-box pre {
            margin: 0;
            font-size: 0.9rem;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .error {
            color: #e53e3e;
            background-color: #fef2f2;
        }
    </style>
</head>
<body>

    <div x-data="chainApp()" class="container">
        <h1>Dispatch Chain</h1>

        <!-- Selector de Jobs -->
        <div>
            <label for="jobSelect">Select Job:</label>
            <select x-model="selectedJob" id="jobSelect">
                <option value="JobA">JobA</option>
                <option value="JobB">JobB</option>
            </select>
        </div>

        <!-- Selector de Queue -->
        <div>
            <label for="queueSelect">Select Queue:</label>
            <select x-model="selectedQueue" id="queueSelect">
                <option value="default">default</option>
                <option value="queue1">queue1</option>
                <option value="queue2">queue2</option>
                <option value="queue3">queue3</option>
            </select>
        </div>

        <!-- Agregar trabajo a la lista -->
        <div>
            <button @click="addJob">Add Job to List</button>
        </div>

        <!-- Lista de trabajos añadidos -->
        <div>
            <ul>
                <template x-for="(job, index) in jobs" :key="index">
                    <li class="list-item">
                        <span x-text="job"></span>
                        <button @click="removeJob(index)">Remove</button>
                    </li>
                </template>
            </ul>
        </div>

        <!-- Botón para despachar -->
        <div>
            <button @click="dispatchChain">Dispatch Chain</button>
        </div>

        <!-- Respuesta del servidor -->
        <div x-show="response" class="response-box" :class="{'error': response.startsWith('Error')}">
            <h2>Response:</h2>
            <pre x-text="response"></pre>
        </div>
    </div>

    <script>
        function chainApp() {
            return {
                selectedJob: 'JobA',
                selectedQueue: 'default',
                jobs: [], // Lista de trabajos seleccionados
                response: null, // Para almacenar la respuesta del servidor

                // Agregar trabajo a la lista
                addJob() {
                    this.jobs.push(this.selectedJob); // Solo añadir si no está ya en la lista
                },

                // Eliminar trabajo de la lista
                removeJob(index) {
                    this.jobs.splice(index, 1); // Eliminar trabajo de la lista por su índice
                },

                // Método para despachar el chain con la lista de trabajos
                async dispatchChain() {
                    if (this.jobs.length === 0) {
                        alert('Please add at least one job to the list.');
                        return;
                    }

                    const url = `/api/dispatch-chain?jobNames=${encodeURIComponent(JSON.stringify(this.jobs))}&queue=${encodeURIComponent(this.selectedQueue)}`;

                    try {
                        const res = await fetch(url, { method: 'GET' });
                        const data = await res.json();
                        this.response = JSON.stringify(data, null, 2); // Almacena la respuesta como JSON legible
                        console.log('Chain dispatched:', data);
                    } catch (error) {
                        console.error('Error dispatching chain:', error);
                        this.response = `Error: ${error.message}`;
                    }
                }
            };
        }
    </script>

</body>
</html>
