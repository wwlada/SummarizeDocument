<?php

namespace App\Http\Controllers;

use App\Models\Analytics;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'lastTimeProcessed' => Analytics::latest()->first()->created_at,
            'avgResponseTime'   => Analytics::avg('response_time'),
        ]);
    }
}
