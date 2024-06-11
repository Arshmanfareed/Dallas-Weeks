<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RolespermissionController extends Controller
{
    function rolespermission()
    {
        $data = [
            'title' => 'Roles & Permission'
        ];
        return view('roles&permission', $data);
    }
}
