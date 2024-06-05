<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;

class MaindashboardController extends Controller
{
    function maindasboard(Request $request)
    {
        if (Auth::check()) {
            if (isset($request->all()['seat_id'])) {
                $seat_id = $request->all()['seat_id'];
                session(['seat_id' => $request->all()['seat_id']]);
            } else {
                $seat_id = session('seat_id');
            }
            // $user_id = Auth::user()->id;
            $campaigns = Campaign::where('seat_id', $seat_id)->get();
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
