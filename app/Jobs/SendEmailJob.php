<?php

namespace App\Jobs;

use App\Exceptions\NoImagesCompleted;
use App\Models\Image;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendImagesEmail;
use Illuminate\Container\Attributes\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $images;
    protected $recipient;
    /**
     * Create a new job instance.
     */
    public function __construct($images, string $recipient)
    {
        $this->images = $images;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('Sending email with images to ' . $this->recipient);
        // comprobamos si la cantidad de imagenes en base de datos es igual a la cantidad de emails enviados
        if (!(Image::count() == count($this->images))) {
            throw new NoImagesCompleted(Image::count(), count($this->images));
        }

        // enviamos el email
        Mail::to($this->recipient)->send(new SendImagesEmail($this->images));
    }
}
