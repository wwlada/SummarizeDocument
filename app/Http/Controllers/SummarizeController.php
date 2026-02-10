<?php

namespace App\Http\Controllers;

use App\Http\Requests\SummarizeDataRequest;
use App\Service\FileToTextService;
use Illuminate\Http\JsonResponse;

class SummarizeController extends Controller
{
    public function summarizeDocument(SummarizeDataRequest $request, FileToTextService $fileToTextService): JsonResponse
    {
        $text = $fileToTextService->filePath($request->validated('document'))->handleDocument();

        dd($text);

        return response()->json([]);
    }
}
