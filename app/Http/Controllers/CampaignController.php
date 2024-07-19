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
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $campaigns = Campaign::where('user_id', $user_id)->where('is_active', 1)->where('is_archive', 0)->get();
            $data = [
                'title' => 'Campaign',
                'campaigns' => $campaigns,
            ];
            return view('campaign', $data);
        } else {
            return redirect(url('/'));
        }
    }

    function campaigncreate()
    {
        if (Auth::check()) {
            $data = [
                'title' => 'Create Campaign'
            ];
            return view('campaigncreate', $data);
        } else {
            return redirect(url('/'));
        }
    }

    function campaigninfo(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
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
        } else {
            return redirect(url('/'));
        }
    }

    function fromscratch(Request $request)
    {
        if (Auth::check()) {
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
        } else {
            return redirect(url('/'));
        }
    }

    function getCampaignDetails($campaign_id)
    {
        if (Auth::check()) {
            $data = [
                'campaign' => Campaign::where('id', $campaign_id)->first(),
            ];
            return view('campaignDetails', $data);
        } else {
            return redirect(url('/'));
        }
    }

    function changeCampaignStatus($campaign_id)
    {
        if (Auth::check()) {
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
        } else {
            return redirect(url('/'));
        }
    }

    function deleteCampaign($campaign_id)
    {
        if (Auth::check()) {
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
        } else {
            return redirect(url('/'));
        }
    }

    function archiveCampaign($campaign_id)
    {
        if (Auth::check()) {
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
        } else {
            return redirect(url('/'));
        }
    }

    function filterCampaign($filter, $search)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
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
        } else {
            return redirect(url('/'));
        }
    }

    function editCampaign($campaign_id)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $campaign = Campaign::where('user_id', $user_id)->where('id', $campaign_id)->first();
            $data = [
                'title' => 'Edit Campaign',
                'campaign' => $campaign,
            ];
            return view('editCampaign', $data);
        } else {
            return redirect(url('/'));
        }
    }

    function editCampaignInfo(Request $request, $campaign_id)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
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
        } else {
            return redirect(url('/'));
        }
    }

    function editCampaignSequence(Request $request, $campaign_id)
    {
        if (Auth::check()) {
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
                'campaign_id' => $campaign_id,
                'campaign_time' => Campaign::select('start_date')->where('id', $campaign_id)->first()->start_date,
                'img' => Campaign::select('img_path')->where('id', $campaign_id)->first()->img_path
            ];
            return view('editCampaignSequence', $data);
        } else {
            return redirect(url('/'));
        }
    }

    function updateCampaign(Request $request, $campaign_id)
    {
        if (Auth::check()) {
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
        } else {
            return redirect(url('/'));
        }
        return response()->json(['success' => false, 'properties' => 'User login first!']);
    }
}
