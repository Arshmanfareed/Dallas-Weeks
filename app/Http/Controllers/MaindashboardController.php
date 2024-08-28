<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;
use App\Models\SeatInfo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MaindashboardController extends Controller
{
    function maindasboard(Request $request)
    {
        try {
            $seat_id = $request->input('seat_id', session('seat_id'));
            if ($seat_id) {
                session(['seat_id' => $seat_id]);
            } else {
                throw new Exception('Seat Not Found');
            }
            $user_id = Auth::user()->id;
            $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
            if (!is_null($seat)) {
                $seat['active'] = false;
                $seat['connected'] = false;
                if (!empty($seat['account_id'])) {
                    $uc = new UnipileController();
                    $request = ['account_id' => $seat['account_id']];
                    $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $seat['active'] = true;
                        $seat['account'] = $account->getData(true)['account'];
                        session(['account' => $seat['account']]);
                    }
                    $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $account = $account->getData(true)['account'];
                        $profile_url = 'https://www.linkedin.com/in/' . $account['provider_id'];
                        $request = [
                            'account_id' => $seat['account_id'],
                            'profile_url' => $profile_url
                        ];
                        $account = $uc->view_profile(new \Illuminate\Http\Request($request));
                        if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                            $seat['account_profile'] = $account->getData(true)['user_profile'];
                            session(['account_profile' => $seat['account_profile']]);
                        }
                        $seat['connected'] = true;
                    }
                }
                if ($seat['connected'] && $seat['active']) {
                    $lc = new LeadsController();
                    $campaigns = Campaign::where('seat_id', $seat_id)->orderBy('is_active', 'desc')->limit(10)->get();
                    foreach ($campaigns as $campaign) {
                        $campaign['lead_count'] = $lc->getLeadsCountByCampaign($user_id, $campaign->id);
                    }
                    $request = [
                        'account_id' => $seat['account_id'],
                        'limit' => 10,
                    ];
                    $chats = $uc->list_all_chats(new \Illuminate\Http\Request($request));
                    if ($chats instanceof JsonResponse && !isset($chats->getData(true)['error'])) {
                        $chats = $chats->getData(true)['chats']['items'];
                    } else {
                        $chats = array();
                    }
                    $request = [
                        'account_id' => $seat['account_id'],
                        'limit' => 3,
                    ];
                    $relations = $uc->list_all_relations(new \Illuminate\Http\Request($request));
                    if ($relations instanceof JsonResponse && !isset($relations->getData(true)['error'])) {
                        $relations = $relations->getData(true)['relations']['items'];
                    } else {
                        $relations = array();
                    }
                    $data = [
                        'title' => 'Account Dashboard',
                        'campaigns' => $campaigns,
                        'seat' => $seat,
                        'chats' => $chats,
                        'relations' => $relations
                    ];
                    return view('main-dashboard', $data);
                }
                session(['add_account' => true]);
                return redirect(route('dash-settings'));
            }
            throw new Exception('Seat Not Found');
        } catch (Exception $e) {
            Log::info($e);
            return redirect(route('dashobardz'))->withErrors(['error' => $e->getMessage()]);
        }
    }
}
