<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

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
        $seat_id = session('seat_id');
        $user = Auth::user();
        $paymentStatus = PhysicalPayment::where('user_id', $user->id)->where('product_id', $seat_id)->value('physical_payment_status');
        $seat = SeatInfo::find($seat_id);
        $data = [
            'title' => 'Setting',
            'paymentStatus' => $paymentStatus,
            'seat_id' => $seat_id,
        ];
        if ($seat['account_id'] !== NULL) {
            $request = [
                'account_id' => $seat['account_id'],
            ];
            $uc = new UnipileController();
            $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
            if ($account instanceof JsonResponse) {
                $account = $account->getData(true);
                if (!isset($account['error'])) {
                    $seat['connected'] = true;
                } else {
                    $account = array();
                    $seat['connected'] = false;
                }
            } else {
                $account = array();
                $seat['connected'] = false;
            }
            $seatData = $seat ? $seat->toArray() : [];
            if ($seat['connected']) {
                $data['account'] = $account;
                return view('settings', compact('data', 'seatData'));
            } else {
                return view('settings', compact('data', 'seatData'));
            }
        } else {
            return view('settings', compact('data'));
        }
    }
}
