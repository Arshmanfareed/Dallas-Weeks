<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use App\Models\Campaign;
use App\Models\LinkedinSetting;
use App\Models\LeadActions;
use App\Models\Leads;
use App\Models\ImportedLeads;
use App\Models\GlobalSetting;
use App\Models\EmailSetting;
use App\Models\UpdatedCampaignProperties;
use App\Models\CampaignPath;
use App\Models\UpdatedCampaignElements;
use App\Models\CampaignActions;
use App\Models\PhysicalPayment;

class SeatController extends Controller
{
    public function get_seat_by_id($seat_id)
    {
        $user_id = Auth::user()->id;
        $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
        return response()->json(['success' => true, 'seat' => $seat]);
    }

    public function delete_seat($seat_id)
    {
        $user_id = Auth::user()->id;
        $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
        if ($seat['account_id'] !== NULL) {
            $request = [
                'account_id' => $seat['account_id'],
            ];
            $uc = new UnipileController();
            $account = $uc->delete_account(new \Illuminate\Http\Request($request));
            if ($account instanceof JsonResponse) {
                $campaigns = Campaign::where('seat_id', $seat->id)->get();
                PhysicalPayment::where('product_id', $seat->id)->delete();
                foreach ($campaigns as $campaign) {
                    LinkedinSetting::where('campaign_id', $campaign->id)->delete();
                    LeadActions::where('campaign_id', $campaign->id)->delete();
                    Leads::where('campaign_id', $campaign->id)->delete();
                    ImportedLeads::where('campaign_id', $campaign->id)->delete();
                    GlobalSetting::where('campaign_id', $campaign->id)->delete();
                    EmailSetting::where('campaign_id', $campaign->id)->delete();
                    UpdatedCampaignProperties::where('campaign_id', $campaign->id)->delete();
                    CampaignPath::where('campaign_id', $campaign->id)->delete();
                    UpdatedCampaignElements::where('campaign_id', $campaign->id)->delete();
                    CampaignActions::where('campaign_id', $campaign->id)->delete();
                }
                Campaign::where('seat_id', $seat_id)->delete();
                $seat->delete();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        } else {
            $seat->delete();
            return response()->json(['success' => true]);
        }
    }

    public function update_name($seat_id, $seat_name)
    {
        $user_id = Auth::user()->id;
        $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
        $seat->username = $seat_name;
        $seat->save();
        return response()->json(['success' => true]);
    }

    public function filterSeat($search)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $seats = SeatInfo::where('user_id', $user_id);
            if ($search != 'null') {
                $seats = $seats->where('username', 'LIKE', '%' . $search . '%');
            }
            $seats = $seats->get();
            foreach ($seats as $seat) {
                if ($seat['account_id'] !== NULL) {
                    $request = [
                        'account_id' => $seat['account_id'],
                    ];
                    $uc = new UnipileController();
                    $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse) {
                        $account = $account->getData(true);
                        if (!isset($account['error'])) {
                            $seat->connected = true;
                        } else {
                            $seat->connected = false;
                        }
                    } else {
                        $seat->connected = false;
                    }
                } else {
                    $seat->connected = false;
                }
            }
            if (count($seats) != 0) {
                return response()->json(['success' => true, 'seats' => $seats]);
            } else {
                return response()->json(['success' => false, 'seats' => 'Seat not Found']);
            }
        }
    }

    public function get_final_accounts()
    {
        $seats = SeatInfo::whereNotNull('account_id')->get();
        $final_accounts = [];
        $uc = new UnipileController();
        for ($i = 0; $i < count($seats); $i++) {
            $account_id = [
                'account_id' => $seats[$i]['account_id'],
            ];
            $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($account_id));
            $account = $account->getData(true);
            if (array_key_exists($account['account']['connection_params']['im']['id'], $final_accounts)) {
                $final_accounts[$account['account']['connection_params']['im']['id']][] = $seats[$i]['account_id'];
            } else {
                $final_accounts[$account['account']['connection_params']['im']['id']] = [];
                $final_accounts[$account['account']['connection_params']['im']['id']][] = $seats[$i]['account_id'];
            }
        }
        return $final_accounts;
    }
}
