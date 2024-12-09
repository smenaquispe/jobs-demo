<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ConvertImageJob;
use App\Jobs\SendEmailJob;
use Throwable;

class ImageService
{
    public function convertImage(Image $image, string $format)
    {
        Bus::dispatch(new ConvertImageJob($image, $format));
    }

    public function assignJobsToQueue($images, string $format, string $queue = 'default')
    {
        $jobs = $images->map(function (Image $image) use ($format) {
            return new ConvertImageJob($image, $format);
        });

        $batch = Bus::batch($jobs)->onQueue($queue);
        return $batch;
    }

    public function convertImages($images, string $format, string $queue = 'default')
    {
        $batch = $this->assignJobsToQueue($images, $format, $queue);
        $batch->dispatch();
        return $batch;
    }

    public function convertImagesAndSendEmail($images, string $format)
    {
        $jobs = $images->map(function (Image $image) use ($format) {
            return new ConvertImageJob($image, $format);
        });

        // Encadena el envío del correo después de que el batch se complete
        Bus::chain([
            Bus::batch($jobs),
            new SendEmailJob($images, 'example@example.com'),
        ])->catch(function (Throwable $e) {
            dd($e);
        })->dispatch();
    }
}
