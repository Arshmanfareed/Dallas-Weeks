<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use App\Models\CampaignSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    function campaign()
    {
        $user_id = Auth::user()->id;
        $campaigns = Campaign::where('user_id', $user_id)->get();
        $data = [
            'title' => 'Campaign',
            'campaigns' => $campaigns,
        ];
        return view('campaign', $data);
    }
    function campaigncreate()
    {
        $data = [
            'title' => 'Create Campaign'
        ];
        return view('campaigncreate', $data);
    }
    function campaigninfo(Request $request)
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_url' => 'required'
        ]);
        if ($validated) {
            $user_id = Auth::user()->id;
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
    function fromscratch(Request $request)
    {
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
