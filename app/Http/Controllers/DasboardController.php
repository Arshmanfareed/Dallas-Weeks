<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;

class DasboardController extends Controller
{
    function dashboard()
    {
        if (Auth::check()) {
            session()->forget('seat_id');
            $user = Auth::user();
            $paymentStatus = PhysicalPayment::where('user_id', $user->id)->value('physical_payment_status');
            $seats = SeatInfo::where('user_id', $user->id)->get();
            $uc = new UnipileController();
            $accounts = $uc->get_accounts();
            $accounts = $accounts->getData(true)['accounts']['items'];
            foreach ($seats as $seat) {
                $connected = false;
                foreach ($accounts as $account) {
                    if ($seat['account_id'] == $account['id']) {
                        $connected = true;
                        break;
                    }
                }
                $seat['connected'] = $connected;
            }
            $data = [
                'title' => 'Account Dashboard',
                'paymentStatus' => $paymentStatus,
                'seats' => $seats
            ];
            return view('dashboard-account', $data);
        } else {
            return redirect(url('/'));
        }
    }
}
