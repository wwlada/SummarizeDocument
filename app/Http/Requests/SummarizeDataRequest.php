<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SummarizeDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'document' => ['required', 'file', 'mimes:pdf,txt,doc,docx,rtf,md,jpg,jpeg,png,tiff,bmp', 'max:2048'],
        ];
    }
}
