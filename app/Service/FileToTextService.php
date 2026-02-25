<?php

namespace App\Service;

use App\Ai\Agents\PdfTextAndImageSummarizer;
use App\Enum\MimesEnum;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Files;

class FileToTextService
{
    public function __construct(
        private string $filePath = '',
        private string $ext = '',
    ){}

    public function handleDocument(): string
    {
        $filePath = $this->filePath;

        try {
            return match ($this->ext) {
                'pdf'                               => $this->handlePdf(),
                'jpg', 'jpeg', 'png', 'tiff', 'bmp' => $this->summarizeImage(),
                'txt', 'md'                         => $this->summarizeText(file_get_contents($filePath)),
                'doc', 'docx', 'rtf'                => $this->handleOfficeDoc(),

                default => "Extension error"
            };
        } finally {
            if (is_string($filePath) && $filePath !== '' && file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    private function summarizeText($text): string
    {
        return PdfTextAndImageSummarizer::make()->prompt(
            prompt: $text
        );
    }

    private function summarizePdfImage(): string
    {
        return PdfTextAndImageSummarizer::make()->prompt(
            prompt:      '',
            attachments: [ Files\Document::fromPath($this->filePath) ],
        );
    }

    private function summarizeImage(): string
    {
        return PdfTextAndImageSummarizer::make()->prompt(
            prompt:      '',
            attachments: [ Files\Image::fromPath($this->filePath) ],
        );
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

    public function handlePdf(): string
    {
        $normalText = $this->extractTextFromPdf($this->filePath);
        if ($normalText) return $this->summarizeText(text:$normalText);

        return $this->summarizePdfImage();
    }

    public function handleOfficeDoc(): string
    {
        $cmd = sprintf('pandoc %s -t plain -o - 2>&1', escapeshellarg($this->filePath));
        exec($cmd, $output, $result_code);

        $textRaw = implode("\n", $output);
        $text = trim($textRaw);

        if ($result_code !== 0 || $text === '') return "Could not extract text.";

        return $this->summarizeText($text);
    }

    public function extractTextFromPdf(string $filePath): string|null
    {
        $cmd = sprintf('pdftotext %s - 2>&1', escapeshellarg($filePath));
        exec($cmd, $output, $result_code);

        $textRaw = implode("\n", $output);
        $text    = trim($textRaw);

        $isTextInPdf = $this->isTextInPdf(text: $text, result_code: $result_code);

        if ($isTextInPdf) return $text;

        return null;
    }

    private function isTextInPdf(string $text, int $result_code): bool
    {
        $normOneLine = trim(preg_replace('/\s+/u', ' ', $text));
        $tokens = $normOneLine === '' ? [] : preg_split('/\s+/u', $normOneLine);

        $short = 0;
        foreach ($tokens as $t) {
            if (mb_strlen($t, 'UTF-8') <= 2) $short++;
        }
        $shortRatio = count($tokens) ? ($short / count($tokens)) : 1;

        return ($result_code === 0)
            && (mb_strlen($normOneLine, 'UTF-8') >= 200)
            && ($shortRatio < 0.5);
    }
}
