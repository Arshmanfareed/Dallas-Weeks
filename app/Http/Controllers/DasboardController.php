<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;

class DasboardController extends Controller
{
    // function dashboard()
    // {
    //     if (Auth::check()) {
    //         $data = [
    //             'title' => 'Account Dashboard'
    //         ];
    //         return view('dashboard-account', $data);
    //     } else {
    //         return redirect(url('/'));
    //     }
    // }
    function dashboard()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $paymentStatus = PhysicalPayment::where('user_id', $user->id)->value('physical_payment_status');
            $data = [
                'title' => 'Account Dashboard',
                'paymentStatus' => $paymentStatus
            ];
            return view('dashboard-account', $data);
        } else {
            return redirect(url('/'));
        }
    }
}
