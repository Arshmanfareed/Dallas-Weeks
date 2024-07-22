<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    function team()
    {
        $data = [
            'title' => 'Team Dashboard'
        ];
        return view('team', $data);
    }
}
