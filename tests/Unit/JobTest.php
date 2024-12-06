<?php

use App\Jobs\JobA;
use Illuminate\Support\Facades\Bus;

test('example', function () {
    expect(true)->toBeTrue();
});

it('can dispatch a job', function () {
    // Simula el bus de tareas
    Bus::fake();

    // Despacha el trabajo
    JobA::dispatch();

    // Verifica que el trabajo fue despachado
    Bus::assertDispatched(JobA::class);
});
