<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\CampaignSchedule;
use App\Models\EmailSetting;
use App\Models\GlobalSetting;
use App\Models\LinkedinSetting;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    function campaign()
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaigns = Campaign::where('user_id', $user_id)->where('is_active', 1)->where('is_archive', 0)->get();
            $data = [
                'title' => 'Campaign',
                'campaigns' => $campaigns,
            ];
            return view('campaign', $data);
        }
    }
    function campaigncreate()
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $data = [
                'title' => 'Create Campaign'
            ];
            return view('campaigncreate', $data);
        }
    }
    function campaigninfo(Request $request)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $validated = $request->validate([
                'campaign_name' => 'required|string|max:255',
                'campaign_url' => 'required'
            ]);
            if ($validated) {
                $schedules = CampaignSchedule::where('user_id', $user_id)->orWhere('user_id', 0)->get();
                $all = $request->except('_token');
                $campaign_details = [];
                foreach ($all as $key => $value) {
                    $campaign_details[$key] = $value;
                }
                $data = [
                    'title' => 'Create Campaign Info',
                    'campaign_details' => $campaign_details,
                    'campaign_schedule' => $schedules
                ];
                return view('createcampaigninfo', $data);
            }
        }
    }
    function fromscratch(Request $request)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $all = $request->except('_token');
            $settings = [];
            foreach ($all as $key => $value) {
                $settings[$key] = $value;
            }
            $data = [
                'campaigns' => CampaignElement::where('is_conditional', '0')->get(),
                'conditional_campaigns' => CampaignElement::where('is_conditional', '1')->get(),
                'title' => 'Create Campaign Info',
                'settings' => $settings,
            ];
            return view('createcampaignfromscratch', $data);
        }
    }
    function getCampaignDetails($campaign_id)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $data = [
                'campaign' => Campaign::where('id', $campaign_id)->first(),
            ];
            return view('campaignDetails', $data);
        }
    }
    function changeCampaignStatus($campaign_id)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaign = Campaign::where('id', $campaign_id)->first();
            if ($campaign->is_active == 1) {
                $campaign->is_active = 0;
                $campaign->save();
                return response()->json(['success' => true, 'active' => $campaign->is_active]);
            } else {
                $campaign->is_active = 1;
                $campaign->save();
                return response()->json(['success' => true, 'active' => $campaign->is_active]);
            }
        }
    }
    function deleteCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaign = Campaign::where('id', $campaign_id)->first();
            if ($campaign) {
                LinkedinSetting::where('campaign_id', $campaign->id)->delete();
                GlobalSetting::where('campaign_id', $campaign->id)->delete();
                EmailSetting::where('campaign_id', $campaign->id)->delete();
                $elements = UpdatedCampaignElements::where('campaign_id', $campaign->id)->get();
                if ($elements) {
                    foreach ($elements as $element) {
                        UpdatedCampaignProperties::where('element_id', $element->id)->delete();
                        CampaignPath::where('current_element_id', $element->id)->delete();
                        $element->delete();
                    }
                }
                $campaign->delete();
                return response()->json(['success' => true]);
            }
            return response()->json(['error' => 'Campaign not found'], 404);
        }
    }
    function archiveCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaign = Campaign::where('id', $campaign_id)->first();
            if ($campaign->is_archive == 1) {
                $campaign->is_archive = 0;
                $campaign->save();
                return response()->json(['success' => true, 'archive' => $campaign->is_archive]);
            } else {
                $campaign->is_archive = 1;
                $campaign->save();
                return response()->json(['success' => true, 'archive' => $campaign->is_archive]);
            }
        }
    }
    function filterCampaign($filter)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaigns = null;
            if ($filter == 'active') {
                $campaigns = Campaign::where('user_id', $user_id)->where('is_active', 1)->where('is_archive', 0)->get();
                return response()->json(['success' => true, 'campaigns' => $campaigns]);
            } else if ($filter == 'inactive') {
                $campaigns = Campaign::where('user_id', $user_id)->where('is_active', 0)->where('is_archive', 0)->get();
                return response()->json(['success' => true, 'campaigns' => $campaigns]);
            } else if ($filter == 'archive') {
                $campaigns = Campaign::where('user_id', $user_id)->where('is_archive', 1)->get();
                return response()->json(['success' => true, 'campaigns' => $campaigns]);
            }
            return response()->json(['error' => 'Campaign not found'], 404);
        }
    }
}
