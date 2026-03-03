<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ClearStorageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $path
    ){}

    public function handle(): void
    {
        if (is_string($this->path) && $this->path !== '' && file_exists($this->path)) {
            unlink($this->path);
        }
    }
}
