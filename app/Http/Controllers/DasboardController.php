<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DasboardController extends Controller
{
    function dashboard()
    {
        try {
            session()->forget(['seat_id', 'account', 'account_profile']);
            $user_id = Auth::user()->id;
            $paymentStatus = PhysicalPayment::where('user_id', $user_id)->value('physical_payment_status');
            $seats = SeatInfo::where('user_id', $user_id)->get();
            $uc = new UnipileController();
            foreach ($seats as $seat) {
                $seat['connected'] = false;
                $seat['active'] = false;
                if (!empty($seat['account_id'])) {
                    $request = ['account_id' => $seat['account_id']];
                    $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $seat['active'] = true;
                        $seat['account'] = $account->getData(true)['account'];
                    }
                    $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $seat['connected'] = true;
                        $seat['account_profile'] = $account->getData(true)['account'];
                    }
                }
            }
            $data = [
                'title' => 'Account Dashboard',
                'paymentStatus' => $paymentStatus,
                'seats' => $seats
            ];
            return view('dashboard-account', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
