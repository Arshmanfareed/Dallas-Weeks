<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class IntegrationController extends Controller
{
    function integration()
    {
        if (Auth::check()) {
            if (session()->has('seat_id')) {
                $seat_id = session('seat_id');
                $seat = SeatInfo::where('id', $seat_id)->first();
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
                    if ($seat['connected']) {
                        $data = [
                            'title' => 'Integration'
                        ];
                        return view('integrations', $data);
                    } else {
                        session(['add_account' => true]);
                        return redirect(route('dash-settings'));
                    }
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
