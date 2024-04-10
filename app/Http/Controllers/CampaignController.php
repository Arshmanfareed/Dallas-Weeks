<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $all = $request->except('_token');
        $campaign_details = [];
        foreach ($all as $key => $value) {
            $campaign_details[$key] = $value;
        }
        $data = [
            'title' => 'Create Campaign Info',
            'campaign_details' => $campaign_details,
        ];
        return view('createcampaigninfo', $data);
    }
    function fromscratch(Request $request)
    {
        $all = $request->except('_token');
        $linkedin_setting = [];
        foreach ($all as $key => $value) {
            $linkedin_setting[$key] = $value;
        }
        $data = [
            'campaigns' => CampaignElement::all(),
            'title' => 'Create Campaign Info',
            'linkedin_setting' => $linkedin_setting,
        ];
        return view('createcampaignfromscratch', $data);
    }
}
