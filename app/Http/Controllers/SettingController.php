<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;

class SettingController extends Controller
{
    function settingrolespermission()
    {
        $data = [
            'title' => 'Setting'
        ];
        return view('setting', $data);
    }
    function setting()
    {
        if (Auth::check()) {
            if (session()->has('seat_id')) {
                $seat_id = session('seat_id');
                $user = Auth::user();
                $paymentStatus = PhysicalPayment::where('user_id', $user->id)->where('product_id', $seat_id)->value('physical_payment_status');
                $seat = SeatInfo::where('id', $seat_id)->first();
                $seatData = $seat ? $seat->toArray() : [];
                $data = [
                    'title' => 'Setting',
                    'paymentStatus' => $paymentStatus,
                    'seat_id' => $seat_id,
                ];
                return view('settings', compact('data', 'seatData'));
            } else {
                return redirect(route('dashobardz'));
            }
        } else {
            return redirect(url('/'));
        }
    }
}
