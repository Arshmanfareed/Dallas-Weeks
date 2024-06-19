<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlacklistController extends Controller
{
    function blacklist()
    {
        $user_id = Auth::user()->id;
        $blacklist = Blacklist::where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        $data = [
            'title' => 'Blacklist',
            'blacklist' => $blacklist,
        ];
        return view('blacklist', $data);
    }
}
