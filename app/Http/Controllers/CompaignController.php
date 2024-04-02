<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaignController extends Controller
{
    function compaign()
    {
        $user_id = Auth::user()->id;
        $compaigns = Campaign::where('user_id', $user_id)->get();
        $data = [
            'title' => 'Compaign',
            'compaigns' => $compaigns,
        ];
        return view('compaign', $data);
    }
    function compaigncreate()
    {
        $data = [
            'title' => 'Create Compaign'
        ];
        return view('compaigncreate', $data);
    }
    function compaigninfo()
    {
        $data = [
            'title' => 'Create Compaign Info'
        ];
        return view('createcompaigninfo', $data);
    }
    function fromscratch(Request $request)
    {
        $all = $request->except('_token');
        $linkedin_setting = [];
        foreach ($all as $key => $value) {
            $linkedin_setting[$key] = $value;
        }
        $data = [
            'compaigns' => CampaignElement::all(),
            'title' => 'Create Compaign Info',
            'linkedin_setting' => $linkedin_setting,
        ];
        return view('createcompaignfromscratch', $data);
    }
}
