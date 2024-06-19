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
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use App\Models\ImportedLeads;
use App\Models\LeadActions;
use App\Models\Leads;
use App\Models\CampaignActions;

class CampaignController extends Controller
{
    function campaign()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $campaigns = Campaign::where('user_id', $user_id)
            ->where('seat_id', $seat->id)
            ->where('is_active', 1)
            ->where('is_archive', 0)
            ->get();
        $data = [
            'title' => 'Campaign',
            'campaigns' => $campaigns,
        ];
        return view('campaign', $data);
    }

    function campaigncreate()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $data = [
            'title' => 'Create Campaign'
        ];
        return view('campaigncreate', $data);
    }

    function campaigninfo(Request $request)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_url' => 'required'
        ]);
        $schedules = CampaignSchedule::where('user_id', $user_id)->orWhere('user_id', 0)->get();
        $campaign_details = $request->except('_token');
        $data = [
            'title' => 'Create Campaign Info',
            'campaign_details' => $campaign_details,
            'campaign_schedule' => $schedules
        ];
        return view('createcampaigninfo', $data);
    }

    function fromscratch(Request $request)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $settings = $request->except(['_token', 'seat']);
        $data = [
            'campaigns' => CampaignElement::where('is_conditional', '0')->get(),
            'conditional_campaigns' => CampaignElement::where('is_conditional', '1')->get(),
            'title' => 'Create Campaign Info',
            'settings' => $settings,
        ];
        return view('createcampaignfromscratch', $data);
    }

    function getCampaignDetails($campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $data = [
            'campaign' => Campaign::where('id', $campaign_id)->first(),
        ];
        return view('campaignDetails', $data);
    }

    function changeCampaignStatus($campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
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

    function deleteCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $campaign = Campaign::where('id', $campaign_id)->first();
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
        $campaign->delete();
        return response()->json(['success' => true]);
    }

    function archiveCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
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

    function filterCampaign($filter, $search)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $campaigns = Campaign::where('user_id', $user_id)->where('seat_id', $seat_id);
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

    function editCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $campaign = Campaign::where('user_id', $user_id)->where('id', $campaign_id)->first();
        $data = [
            'title' => 'Edit Campaign',
            'campaign' => $campaign,
        ];
        return view('editCampaign', $data);
    }

    function editCampaignInfo(Request $request, $campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_url' => 'required'
        ]);
        $email_settings = EmailSetting::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
        $linkedin_settings = LinkedinSetting::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
        $global_settings = GlobalSetting::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
        $schedules = CampaignSchedule::where('user_id', $user_id)->orWhere('user_id', 0)->get();
        $campaign_details = $request->except('_token');
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

    function editCampaignSequence(Request $request, $campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $settings = $request->except('_token');
        $data = [
            'campaigns' => CampaignElement::where('is_conditional', '0')->get(),
            'conditional_campaigns' => CampaignElement::where('is_conditional', '1')->get(),
            'title' => 'Edit Campaign Sequence',
            'settings' => $settings,
            'campaign_id' => $campaign_id,
            'campaign_time' => Campaign::select('start_date')->where('id', $campaign_id)->first()->start_date,
            'img' => Campaign::select('img_path')->where('id', $campaign_id)->first()->img_path
        ];
        return view('editCampaignSequence', $data);
    }

    function updateCampaign(Request $request, $campaign_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $all = $request->all();
        $settings = $all['settings'];
        $campaign = Campaign::where('id', $campaign_id)->first();
        $campaign->campaign_name = $settings['campaign_name'];
        unset($settings['campaign_name']);
        $campaign->campaign_type = $settings['campaign_type'];
        unset($settings['campaign_type']);
        if (!empty($settings['campaign_url'])) {
            $campaign->campaign_url = $settings['campaign_url'];
            unset($settings['campaign_url']);
        }
        if (!empty($settings['campaign_connection'])) {
            $campaign->campaign_connection = $settings['campaign_connection'];
            unset($settings['campaign_connection']);
        }
        $campaign->save();
        if ($campaign->id) {
            foreach ($settings as $key => $value) {
                if (str_contains($key, 'email_settings_')) {
                    $str_key = str_replace('email_settings_', '', $key);
                    $setting = EmailSetting::where('id', $str_key)->where('campaign_id', $campaign_id)->first();
                }
                if (str_contains($key, 'linkedin_settings_')) {
                    $str_key = str_replace('linkedin_settings_', '', $key);
                    $setting = LinkedinSetting::where('id', $str_key)->where('campaign_id', $campaign_id)->first();
                }
                if (str_contains($key, 'global_settings_')) {
                    $str_key = str_replace('global_settings_', '', $key);
                    $setting = GlobalSetting::where('id', $str_key)->where('campaign_id', $campaign_id)->first();
                }
                $setting->value = $value;
                $setting->save();
            }
            $request->session()->flash('success', 'Campaign succesfully updated!');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'properties' => 'User login first!']);
    }
}
