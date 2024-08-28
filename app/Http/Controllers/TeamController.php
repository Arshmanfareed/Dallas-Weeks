<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    function team()
    {
        try {
            $data = [
                'title' => 'Team Dashboard'
            ];
            return view('team', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
