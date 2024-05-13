<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;

class MaindashboardController extends Controller
{
    function maindasboard()
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $campaigns = Campaign::where('user_id', $user_id)->get();
            $data = [
                'title' => 'Account Dashboard',
                'campaigns' => $campaigns,
            ];
            return view('main-dashboard', $data);
        } else {
            return redirect(url('/'));
        }
    }
}
