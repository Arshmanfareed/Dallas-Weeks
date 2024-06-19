<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    function message()
    {
        $data = [
            'title' => 'Message'
        ];
        return view('message', $data);
    }
}
