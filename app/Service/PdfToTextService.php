<?php

namespace App\Service;

class PdfToTextService
{
    public function handlePdf(string $filePath): string
    {

        dd($this->extractTextFromPdf($filePath));
        return $this->extractTextFromPdf($filePath);
    }




    public function extractTextFromPdf(string $filePath): string
    {
        $cmd = sprintf('pdftotext %s - 2>&1', escapeshellarg($filePath));
        exec($cmd, $output, $result_code);

        $textRaw = implode("\n", $output);
        $text = trim($textRaw);

        $isTextInPdf = $this->isTextInPdf(text: $text, result_code: $result_code);

        if ($isTextInPdf) {
            return $text;
        }


        dd('is not text in pdf');
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
