<?php

namespace App\Http\Controllers;

use App\Models\Analytics;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'lastTimeProcessed' => Analytics::latest()?->first()?->created_at,
            'avgResponseTime'   => round(Analytics::avg('response_time'), 2),
        ]);
    }
}
