<?php

use App\Jobs\ConvertImageJob;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\NotImagePathFoundException;
use App\Jobs\JobA;
use App\Jobs\SendEmailJob;
use App\Mail\SendImagesEmail;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Bus::fake();
    Queue::fake();
    Mail::fake();
    Storage::fake('local');
    $this->service = app(ImageService::class);
});

it('can convert an image', function () {
    $imagePath = 'images/test_image.jpg';
    Storage::put($imagePath, 'dummy content'); // Escribir algo en el archivo

    $image = Image::factory()->create([
        'path' => Storage::path($imagePath), // Ruta real del archivo simulado
    ]);

    $this->service->convertImage($image, 'jpeg');

    Bus::assertDispatched(ConvertImageJob::class);

    Bus::assertDispatched(ConvertImageJob::class, function ($job) use ($image) {
        // Ejecutar el job manualmente para simular la lógica del job
        $job->handle();

        $this->assertEquals('converted', $image->status);
        $this->assertEquals($image->path . '.jpeg', $image->converted_path);
        $this->assertEquals('jpeg', $image->output_format);

        return true;
    });

    Storage::delete($imagePath);
});

it('can assign jobs to a queue', function () {
    $images = Image::factory()->count(3)->create();

    $batch = $this->service->assignJobsToQueue($images, 'jpeg', 'image_conversion_queue');

    $batch->jobs->each(function ($job) {
        $job->onQueue('image_conversion_queue'); // Asigna la cola explícitamente
    });

    $batch = $batch->dispatch();
    Bus::assertBatched(function ($batch) use ($images) {
        return (count($batch->jobs) == 3);
    });

    $this->assertEquals('image_conversion_queue', $batch->queue);
});


it('can`t convert if an path is not found', function () {
    $images = Image::factory()->count(3)->create();

    [$job, $batch] = (new ConvertImageJob($images[0], 'jpeg'))->withFakeBatch();

    $this->expectException(NotImagePathFoundException::class);
    $job->handle();
});

it('can convert images', function () {
    $imagePaths = ['images/test_image1.jpg', 'images/test_image2.jpg'];
    foreach ($imagePaths as $path) {
        Storage::put($path, 'dummy content');
    }

    $images = collect($imagePaths)->map(function ($path) {
        return Image::factory()->create([
            'path' => Storage::path($path),
        ]);
    });

    $format = 'jpeg';

    $this->service->convertImagesAndSendEmail($images, $format);

    Bus::assertChained([
        Bus::chainedBatch(function (PendingBatch $batch) {
            return $batch->jobs->count() === 2;
        }),
        new SendEmailJob($images, 'example@example.com'),
    ]);


    // Limpia los archivos de prueba
    foreach ($imagePaths as $path) {
        Storage::delete($path);
    }
});

it('can send emails', function () {

    $imagePaths = ['images/test_image1.jpg', 'images/test_image2.jpg'];
    foreach ($imagePaths as $path) {
        Storage::put($path, 'dummy content');
    }

    $images = collect($imagePaths)->map(function ($path) {
        return Image::factory()->create([
            'path' => Storage::path($path),
        ]);
    });

    app()->call([new SendEmailJob($images->toArray(), "example@example.com"), 'handle']);
    Mail::assertSent(SendImagesEmail::class, 'example@example.com');
});
