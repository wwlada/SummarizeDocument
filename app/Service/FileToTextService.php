<?php

namespace App\Service;

use App\Ai\Agents\PdfTextAndImageSummarizer;
use App\DTO\responseDTO;
use App\Enum\MimesEnum;
use App\Models\Analytics;
use DateTime;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Laravel\Ai\Files;

class FileToTextService
{
    public function __construct(
        private string $filePath = '',
        private string $ext = '',
        private int    $inputTokens = 0,
        private int    $outputTokens = 0,
        private float  $startTime = 0,
    ){}

    public function handleDocument(): responseDTO
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

    private function summarizeText($text): responseDTO
    {
        $raw = PdfTextAndImageSummarizer::make()->prompt(
            prompt: $text
        );
        $this->tokensUsed(raw: $raw);
        $this->saveData();

        return responseDTO::aiResponse(
            response:          $this->decodeAiResponse($raw),
            inputTokens:       $this->inputTokens,
            outputTokens:      $this->outputTokens,
            avgResponseTime:   $this->getAvgResponseTime(),
            lastTimeProcessed: $this->lastTimeProcessed()
        );
    }

    private function summarizePdfImage(): responseDTO
    {
        $raw = PdfTextAndImageSummarizer::make()
            ->prompt(
                prompt:      '',
                attachments: [ Files\Document::fromPath($this->filePath) ],
        );
        $this->tokensUsed(raw: $raw);
        $this->saveData();

        return responseDTO::aiResponse(
            response:          $this->decodeAiResponse($raw),
            inputTokens:       $this->inputTokens,
            outputTokens:      $this->outputTokens,
            avgResponseTime:   $this->getAvgResponseTime(),
            lastTimeProcessed: $this->lastTimeProcessed()
        );
    }

    private function summarizeImage(): responseDTO
    {
        $raw = PdfTextAndImageSummarizer::make()->prompt(
            prompt:      '',
            attachments: [ Files\Image::fromPath($this->filePath) ],
        );
        $this->tokensUsed(raw: $raw);
        $this->saveData();

        return responseDTO::aiResponse(
            response:          $this->decodeAiResponse($raw),
            inputTokens:       $this->inputTokens,
            outputTokens:      $this->outputTokens,
            avgResponseTime:   $this->getAvgResponseTime(),
            lastTimeProcessed: $this->lastTimeProcessed()
        );
    }

    private function tokensUsed(object $raw): void
    {
        $this->inputTokens  += $raw->usage->promptTokens;
        $this->outputTokens += $raw->usage->completionTokens;
    }

    private function decodeAiResponse(string $response): array
    {
        $cleaned = preg_replace('/^"""\s*|\s*"""$/', '', trim($response));
        $decoded = json_decode($cleaned, true);

        if (!is_array($decoded) || !isset($decoded['language'], $decoded['body'])) {
            throw new \RuntimeException('Invalid AI response format');
        }

        return $decoded;
    }

    public function filePath(UploadedFile $document): FileToTextService
    {
        $startTime = microtime(true);
        $this->startTime = $startTime;

        $mime = $document->getMimeType();
        $this->ext = MimesEnum::shortFromMime($mime) ?? 'unknown';

        $path = $document->store(path:'rawDocuments', options:'local');
        $this->filePath = storage_path(path:'app/private/'.$path);

        $outDir = storage_path(path:'app/private/ocrImage');
        if (!is_dir($outDir)) { mkdir($outDir, permissions:0755, recursive:true); }

        return $this;
    }

    public function handlePdf(): responseDTO
    {
        $normalText = $this->extractTextFromPdf($this->filePath);
        if ($normalText) return $this->summarizeText(text:$normalText);

        return $this->summarizePdfImage();
    }

    public function handleOfficeDoc(): responseDTO|null
    {
        $cmd = sprintf('pandoc %s -t plain -o - 2>&1', escapeshellarg($this->filePath));
        exec($cmd, $output, $result_code);

        $textRaw = implode("\n", $output);
        $text    = trim($textRaw);

        if ($result_code !== 0 || $text === '') return null;

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

    private function saveData(): void
    {
        Analytics::create([
            'user_id'       => Auth::id(),
            'input_tokens'  => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'total_tokens'  => $this->inputTokens + $this->outputTokens,
            'response_time' => microtime(true) - $this->startTime,
        ]);
    }

    private function getAvgResponseTime(): float
    {
        return round(Analytics::avg('response_time'), 2);
    }

    private function lastTimeProcessed(): DateTime
    {
        return Analytics::latest()->first()->created_at;
    }
}
