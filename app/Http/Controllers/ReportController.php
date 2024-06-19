<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    function report()
    {
        $data = [
            'title' => 'Report'
        ];
        return view('reports', $data);
    }
}
