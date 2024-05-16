<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leads;

class LeadsController extends Controller
{
    function leads()
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $leads = Leads::where('user_id', $user_id)->get();
            $campaigns = Campaign::where('user_id', $user_id)->get();
            $data = [
                'title' => 'Leads',
                'leads' => $leads,
                'campaigns' => $campaigns,
            ];
            return view('leads', $data);
        } else {
            return redirect(url('/'));
        }
    }

    function getLeadsByCampaign($id)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            if ($id == 'all') {
                $leads = Leads::where('user_id', $user_id)->get();
            } else {
                $leads = Leads::where('user_id', $user_id)->where('campaign_id', $id)->get();
            }
            if (!$leads->isEmpty()) {
                return response()->json(['success' => true, 'leads' => $leads]);
            } else {
                return response()->json(['success' => false, 'message' => 'Leads not found!']);
            }
        }
    }
}
