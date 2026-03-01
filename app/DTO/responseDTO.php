<?php

namespace App\DTO;

use DateTime;

class responseDTO
{
    public function __construct(
        public array $response,
        public int   $inputTokens,
        public int   $outputTokens,
        public float $avgResponseTime,
        public DateTime  $lastTimeProcessed,
    ){}

    public static function aiResponse(
        array $response,
        int   $inputTokens,
        int   $outputTokens,
        float $avgResponseTime,
        DateTime $lastTimeProcessed
    ): responseDTO
    {
        return new self(
            response:          $response,
            inputTokens:       $inputTokens,
            outputTokens:      $outputTokens,
            avgResponseTime:   $avgResponseTime,
            lastTimeProcessed: $lastTimeProcessed
        );
    }
}
