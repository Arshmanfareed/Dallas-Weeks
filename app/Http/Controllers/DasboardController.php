<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DasboardController extends Controller
{
    function dashboard()
    {
        if (Auth::check()) {
            $data = [
                'title' => 'Account Dashboard'
            ];
            return view('dashboard-account', $data);
        } else {
            return redirect(url('/'));
        }
    }
}
