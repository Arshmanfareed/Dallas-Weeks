<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class RolespermissionController extends Controller
{
    function rolespermission()
    {
        try {
            $data = [
                'title' => 'Roles & Permission'
            ];
            return view('roles&permission', $data);
        } catch (Exception $e) {
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
