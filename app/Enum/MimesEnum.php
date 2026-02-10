<?php

namespace App\Enum;

enum MimesEnum: string
{
    // Pdf
    case PDF = 'application/pdf';

    // Text
    case TXT = 'text/plain';
    case MD_TEXT = 'text/markdown';
    case MD_X_MARKDOWN = 'text/x-markdown';

    // Images
    case JPG = 'image/jpeg';
    case PNG = 'image/png';
    case TIFF = 'image/tiff';
    case BMP = 'image/bmp';

    // Word, Rtf
    case DOC = 'application/msword';
    case DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case RTF_APP = 'application/rtf';
    case RTF_TEXT = 'text/rtf';

    private function short(): string
    {
        return match ($this) {
            self::PDF  => 'pdf',

            self::TXT  => 'txt',
            self::MD_TEXT, self::MD_X_MARKDOWN => 'md',

            self::JPG  => 'jpg',
            self::PNG  => 'png',
            self::TIFF => 'tiff',
            self::BMP  => 'bmp',

            self::DOC  => 'doc',
            self::DOCX => 'docx',
            self::RTF_APP, self::RTF_TEXT => 'rtf',
        };
    }

    public static function shortFromMime(string $mime): ?string
    {
        $mime = strtolower(trim($mime));
        foreach (self::cases() as $case) {
            if ($mime === $case->value) {
                return $case->short();
            }
        }
        return null;
    }
}
