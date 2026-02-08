<?php

namespace App\Enum;

enum AnswerLengthEnum: string
{
    case SHORT  = 'short';
    case MEDIUM = 'medium';
    case LONG   = 'long';
    case MAX    = 'max';
}
