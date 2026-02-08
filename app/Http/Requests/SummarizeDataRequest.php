<?php

namespace App\Http\Requests;

use App\Enum\AnswerLengthEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SummarizeDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'document'      => ['required', 'file', 'mimes:pdf,txt,doc,docx,rtf,md,jpg,jpeg,png,tiff,bmp', 'max:5120'],
            'answer_length' => ['required', new Enum(type: AnswerLengthEnum::class)],
        ];
    }
}
