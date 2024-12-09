<?php

namespace App\Jobs;

use App\Exceptions\NotImagePathFoundException;
use App\Models\Image;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class ConvertImageJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable;

    protected $image;
    protected $format;

    public $tries = 3;
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(Image $image, string $format)
    {
        $this->image = $image;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!file_exists($this->image->path)) {
            $this->image->updateStatus('failed');
            throw new NotImagePathFoundException($this->image->path);
        }

        $this->image->updateStatus('converted');
        $this->image->updateConertedPath($this->format);
        $this->image->updateOutputFormat($this->format);
    }
}
