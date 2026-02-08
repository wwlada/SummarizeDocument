<?php

namespace App\Http\Controllers;

use App\Http\Requests\SummarizeDataRequest;

class SummarizeController extends Controller
{
    public function summarizeDocument(SummarizeDataRequest $request)
    {
        dd($request->validated());

        return response()->json([]);
    }
}
