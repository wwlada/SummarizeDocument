<?php

namespace App\Http\Controllers;

use App\Http\Requests\SummarizeDataRequest;
use App\Service\FileToTextService;
use Illuminate\Http\JsonResponse;

class SummarizeController extends Controller
{
    public function summarizeDocument(SummarizeDataRequest $request, FileToTextService $fileToTextService,): JsonResponse
    {
        $aiSummary = $fileToTextService->filePath($request->validated('document'))->handleDocument();

        return response()->json([
            'aiSummary'         => $aiSummary->response,
            'avgResponseTime'   => $aiSummary->avgResponseTime,
            'lastTimeProcessed' => $aiSummary->lastTimeProcessed->format('Y-m-d H:i:s'),
        ]);
    }
}
