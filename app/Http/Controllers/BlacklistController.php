<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlacklistController extends Controller
{
    function blacklist()
    {
        try {
            $user_id = Auth::user()->id;
            $blacklist = Blacklist::where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
            $data = [
                'title' => 'Blacklist',
                'blacklist' => $blacklist,
            ];
            return view('blacklist', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
