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
    function filterCampaign($filter, $search)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaigns = Campaign::where('user_id', $user_id);
            if ($search != 'null') {
                $campaigns = $campaigns->where('campaign_name', 'LIKE', '%' . $search . '%');
            }
            if ($filter == 'active') {
                $campaigns = $campaigns->where('is_active', 1)->where('is_archive', 0)->get();
            } else if ($filter == 'inactive') {
                $campaigns = $campaigns->where('is_active', 0)->where('is_archive', 0)->get();
            } else if ($filter == 'archive') {
                $campaigns = $campaigns->where('is_archive', 1)->get();
            }
            if (count($campaigns) != 0) {
                return response()->json(['success' => true, 'campaigns' => $campaigns]);
            } else {
                return response()->json(['success' => false, 'campaigns' => 'Campaign not Found']);
            }
        }
    }
    function editCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaign = Campaign::where('user_id', $user_id)->where('id', $campaign_id)->first();
            $data = [
                'title' => 'Edit Campaign',
                'campaign' => $campaign,
            ];
            return view('editCampaign', $data);
        }
    }
    function editCampaignInfo(Request $request, $campaign_id)
    {
        $user_id = Auth::user()->id;
        if ($user_id) {
            $validated = $request->validate([
                'campaign_name' => 'required|string|max:255',
                'campaign_url' => 'required'
            ]);
            if ($validated) {
                $email_settings = EmailSetting::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
                $linkedin_settings = LinkedinSetting::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
                $global_settings = GlobalSetting::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
                $schedules = CampaignSchedule::where('user_id', $user_id)->orWhere('user_id', 0)->get();
                $all = $request->except('_token');
                $campaign_details = [];
                foreach ($all as $key => $value) {
                    $campaign_details[$key] = $value;
                }
                $data = [
                    'title' => 'Create Campaign Info',
                    'email_settings' => $email_settings,
                    'linkedin_settings' => $linkedin_settings,
                    'global_settings' => $global_settings,
                    'campaign_details' => $campaign_details,
                    'campaign_schedule' => $schedules,
                    'campaign_id' => $campaign_id
                ];
                return view('editCampaignInfo', $data);
            }
        }
    }
    function editCampaignSequence(Request $request, $campaign_id)
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
                'title' => 'Edit Campaign Sequence',
                'settings' => $settings,
                'campaign_id' => $campaign_id
            ];
            return view('editCampaignSequence', $data);
        }
    }
    function saveUpdates()
    {
        
    }
}
