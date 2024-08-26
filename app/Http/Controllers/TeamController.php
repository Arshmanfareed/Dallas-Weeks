<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
