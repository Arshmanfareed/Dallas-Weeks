<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    function team()
    {
        if (Auth::check()) {
            $data = [
                'title' => 'Team Dashboard'
            ];
            return view('team', $data);
        } else {
            return redirect(url('/'));
        }
    }
}
