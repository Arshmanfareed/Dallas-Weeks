<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class IntegrationController extends Controller
{
    function integration()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $data = [
            'title' => 'Integration'
        ];
        return view('integrations', $data);
    }
}
