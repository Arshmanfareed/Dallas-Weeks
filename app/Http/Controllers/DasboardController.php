<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DasboardController extends Controller
{
    function dashboard()
    {
        $data = [
            'title' => 'Account Dashboard'
        ];

        $user = auth()->user();

        if ($user) {
            return view('dashboard-account', $data);
        } else {
            return redirect('/');
        }
    }
}
