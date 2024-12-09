<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'status', 'output_format', 'converted_path'];

    public function updateStatus(string $status)
    {
        $this->status = $status;
        $this->save();
    }

    public function updateConertedPath(string $format)
    {
        $this->converted_path = $this->path . '.' . $format;
        $this->save();
    }

    public function updateOutputFormat(string $format)
    {
        $this->output_format = $format;
        $this->save();
    }
}
