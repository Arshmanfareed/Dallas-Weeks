<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class DasboardController extends Controller
{
    function dashboard()
    {
        session()->forget('seat_id');
        $user = Auth::user();
        $paymentStatus = PhysicalPayment::where('user_id', $user->id)->value('physical_payment_status');
        $seats = SeatInfo::where('user_id', $user->id)->get();
        $uc = new UnipileController();
        foreach ($seats as $seat) {
            if ($seat['account_id'] !== NULL) {
                $request = [
                    'account_id' => $seat['account_id'],
                ];
                $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
                if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                    $seat['connected'] = true;
                    $seat['account'] = $account->getData(true)['account'];
                } else {
                    $seat['connected'] = false;
                }
            } else {
                $seat['connected'] = false;
            }
        }
        $data = [
            'title' => 'Account Dashboard',
            'paymentStatus' => $paymentStatus,
            'seats' => $seats
        ];
        return view('dashboard-account', $data);
    }
}
