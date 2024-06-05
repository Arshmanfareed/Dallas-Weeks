<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;

class IntegrationController extends Controller
{
    function integration()
    {
        if (Auth::check()) {
            if (session()->has('seat_id')) {
                $seat_id = session('seat_id');
                // $user_id = Auth::user()->id;
                $seat = SeatInfo::where('id', $seat_id)->first();
                if ($seat->account_id != NULL) {
                    $data = [
                        'title' => 'Integration'
                    ];
                    return view('integrations', $data);
                } else {
                    session(['add_account' => true]);
                    return redirect(route('dash-settings'));
                }
            } else {
                return redirect(route('dashobardz'));
            }
        } else {
            return redirect(url('/'));
        }
    }
}
