<?php

namespace App\Service;

use App\Enum\MimesEnum;
use Illuminate\Http\UploadedFile;

class FileToTextService extends PdfToTextService
{
    public function __construct(
        private string $filePath = '',
        private string $ext = '',
    ){}

    public function handleDocument(): string
    {
        $prefix = 'job_'.uniqid();

        if ($this->ext === "pdf") { return $this->handlePdf(filePath:$this->filePath); }






        return "didnt cover that extension";
    }

    public function filePath(UploadedFile $document): FileToTextService
    {
        $mime = $document->getMimeType();
        $this->ext = MimesEnum::shortFromMime($mime) ?? 'unknown';

        $path = $document->store(path:'rawDocuments', options:'local');
        $this->filePath = storage_path(path:'app/private/'.$path);

        $outDir = storage_path(path:'app/private/ocrImage');
        if (!is_dir($outDir)) { mkdir($outDir, permissions:0755, recursive:true); }

        return $this;
    }
}
