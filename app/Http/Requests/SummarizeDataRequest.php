<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SummarizeDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'document'    => ['required', 'file', 'mimes:pdf,txt,doc,docx,rtf,md,jpg,jpeg,png,tiff,bmp', 'max:2048'],
            'user_prompt' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Please upload a document to summarize.',
            'document.file'     => 'The uploaded item is not a valid file.',
            'document.mimes'    => 'Unsupported file type. Allowed formats: PDF, TXT, DOC, DOCX, RTF, MD, JPG, PNG, TIFF, BMP.',
            'document.max'      => 'The document may not be larger than 2 MB.',
        ];
    }
}
