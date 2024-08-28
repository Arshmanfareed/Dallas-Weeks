<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatEmail;
use App\Models\SeatInfo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    function settingrolespermission()
    {
        try {
            $data = [
                'title' => 'Setting'
            ];
            return view('setting', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function setting()
    {
        $seat_id = session('seat_id');
        $user_id = Auth::user()->id;
        $paymentStatus = PhysicalPayment::where('user_id', $user_id)->where('product_id', $seat_id)->value('physical_payment_status');
        $seat = SeatInfo::find($seat_id);
        $data = [
            'title' => 'Setting',
            'paymentStatus' => $paymentStatus,
            'seat_id' => $seat_id,
        ];
        $uc = new UnipileController();
        $emails = SeatEmail::where('user_id', $user_id)->where('seat_id', $seat_id)->get();
        foreach ($emails as $email) {
            $request = ['account_id' => $email['email_id']];
            $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
            if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                $account = $account->getData(true);
                $email['account'] = $account['account'];
            } else {
                unset($email);
            }
            $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
            if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                $account = $account->getData(true);
                $email['profile'] = $account['account'];
            } else {
                unset($email);
            }
        }
        $data['emails'] = $emails;
        return view('settings', $data);
    }
}
