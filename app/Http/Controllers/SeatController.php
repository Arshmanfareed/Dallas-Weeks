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
use Exception;
use Illuminate\Support\Facades\Log;

class SeatController extends Controller
{
    public function get_seat_by_id($seat_id)
    {
        try {
            $user_id = Auth::user()->id;
            $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
            if (!is_null($seat)) {
                return response()->json(['success' => true, 'seat' => $seat]);
            }
            throw new Exception('Seat Not Found');
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 404);
        }
    }

    public function delete_seat($seat_id)
    {
        try {
            $user_id = Auth::user()->id;
            $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
            if (!is_null($seat)) {
                if (!empty($seat['account_id'])) {
                    $request = ['account_id' => $seat['account_id']];
                    $uc = new UnipileController();
                    $account = $uc->delete_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $campaigns = Campaign::where('seat_id', $seat->id)->get();
                        $campaign_ids = $campaigns->pluck('id')->toArray();
                        PhysicalPayment::where('product_id', $seat->id)->delete();
                        LinkedinSetting::whereIn('campaign_id', $campaign_ids)->delete();
                        LeadActions::whereIn('campaign_id', $campaign_ids)->delete();
                        Leads::whereIn('campaign_id', $campaign_ids)->delete();
                        ImportedLeads::whereIn('campaign_id', $campaign_ids)->delete();
                        GlobalSetting::whereIn('campaign_id', $campaign_ids)->delete();
                        EmailSetting::whereIn('campaign_id', $campaign_ids)->delete();
                        UpdatedCampaignProperties::whereIn('campaign_id', $campaign_ids)->delete();
                        CampaignPath::whereIn('campaign_id', $campaign_ids)->delete();
                        UpdatedCampaignElements::whereIn('campaign_id', $campaign_ids)->delete();
                        CampaignActions::whereIn('campaign_id', $campaign_ids)->delete();
                        Campaign::whereIn('id', $campaign_ids)->delete();
                    } else {
                        throw new Exception('Seat Deletion failed');
                    }
                }
                $seat->delete();
                return response()->json(['success' => true, 'seat' => $seat_id]);
            }
            throw new Exception('Seat Not Found');
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 404);
        }
    }

    public function update_name($seat_id, $seat_name)
    {
        try {
            $user_id = Auth::user()->id;
            $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
            if (!is_null($seat)) {
                $seat->username = $seat_name;
                if ($seat->save()) {
                    return response()->json(['success' => true, 'seat' => $seat]);
                }
                throw new Exception('Seat Updation Failed');
            }
            throw new Exception('Seat Not Found');
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 404);
        }
    }

    public function filterSeat($search)
    {
        try {
            $user_id = Auth::user()->id;
            $seats = SeatInfo::where('user_id', $user_id)
                ->when($search !== 'null', function ($query) use ($search) {
                    return $query->where('username', 'LIKE', '%' . $search . '%');
                })
                ->get();
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
            if (count($seats) > 0) {
                return response()->json(['success' => true, 'seats' => $seats]);
            }
            throw new Exception('Seats Not Found');
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['success' => false, 'errors' => $e->getMessage()], 404);
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
