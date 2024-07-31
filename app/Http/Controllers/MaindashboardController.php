<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class MaindashboardController extends Controller
{
    function maindasboard(Request $request)
    {
        if (isset($request->all()['seat_id'])) {
            $seat_id = $request->all()['seat_id'];
            session(['seat_id' => $request->all()['seat_id']]);
        } elseif (session()->has('seat_id')) {
            $seat_id = session('seat_id');
        } else {
            return redirect(route('dashobardz'));
        }
        $user_id = Auth::user()->id;
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
                $lc = new LeadsController();
                $campaigns = Campaign::where('seat_id', $seat_id)->orderBy('is_active', 'desc')->get();
                foreach ($campaigns as $campaign) {
                    $campaign['lead_count'] = $lc->getLeadsCountByCampaign($user_id, $campaign->id);
                }
                $campaigns = $campaigns->sort(function ($a, $b) {
                    if ($a['is_active'] === $b['is_active']) {
                        return $b['lead_count'] <=> $a['lead_count'];
                    }
                    return $b['is_active'] <=> $a['is_active'];
                });
                $data = [
                    'title' => 'Account Dashboard',
                    'campaigns' => $campaigns,
                ];
                return view('main-dashboard', $data);
            } else {
                session(['add_account' => true]);
                return redirect(route('dash-settings'));
            }
        } else {
            session(['add_account' => true]);
            return redirect(route('dash-settings'));
        }
    }
}
