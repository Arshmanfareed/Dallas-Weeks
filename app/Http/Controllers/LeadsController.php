<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leads;
use League\Csv\Writer;
use App\Exports\LeadsExport;
use App\Models\LeadActions;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\SeatInfo;
use App\Models\UpdatedCampaignElements;
use Illuminate\Http\JsonResponse;

class LeadsController extends Controller
{
    function leads()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $campaigns = Campaign::where('seat_id', $seat_id)->get();
        $final_leads = [];
        foreach ($campaigns as $campaign) {
            $leads = Leads::where('campaign_id', $campaign->id)->get();
            foreach ($leads as $lead) {
                $final_leads[] = $lead;
            }
        }
        $data = [
            'title' => 'Leads',
            'leads' => $final_leads,
            'campaigns' => $campaigns,
        ];
        return view('leads', $data);
    }

    function getLeadsByCampaign($id, $search)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        $leads = Leads::where('user_id', $user_id);
        $campaign = null;
        if ($search != 'null') {
            $leads = $leads->where(function ($query) use ($search) {
                $query->where('contact', 'LIKE', '%' . $search . '%')
                    ->orWhere('title_company', 'LIKE', '%' . $search . '%');
            });
        }
        if ($id != 'all') {
            $leads = $leads->where('campaign_id', $id);
            $campaign = Campaign::where('user_id', $user_id)->where('id', $id)->get();
        } else {
            $campaigns = Campaign::where('seat_id', $seat_id)->get();
            $final_leads = [];
            foreach ($campaigns as $campaign) {
                $leads = Leads::where('campaign_id', $campaign->id)->get();
                foreach ($leads as $lead) {
                    $final_leads[] = $lead;
                }
            }
            $leads = $final_leads;
            return response()->json(['success' => true, 'leads' => $leads, 'campaign' => $campaigns]);
        }
        $leads = $leads->get();
        if (!$leads->isEmpty()) {
            return response()->json(['success' => true, 'leads' => $leads, 'campaign' => $campaign]);
        } else {
            return response()->json(['success' => false, 'message' => 'Leads not found!', 'campaign' => $campaign]);
        }
    }

    function sendLeadsToEmail(Request $request)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $all = $request->all();
        $email = $all['email'];
        $campaign_id = $all['campaign_id'];
        if ($campaign_id != 'all') {
            $campaigns = Campaign::where('user_id', $user_id)->where('seat_id', $seat_id)->where('id', $campaign_id)->get();
        } else {
            $campaigns = Campaign::where('user_id', $user_id)->where('seat_id', $seat_id)->get();
        }
        if (!$campaigns->isEmpty()) {
            foreach ($campaigns as $campaign) {
                $fileName = 'leads_' . time() . '_' . Str::random(10) . '.csv';
                $uploadDir = 'uploads/';
                $uploadFilePath = $uploadDir . $fileName;
                $csv = Writer::createFromFileObject(new \SplTempFileObject());

                $leads = Leads::where('user_id', $user_id)->where('campaign_id', $campaign->id)->get();
                $csv->insertOne(['Sr. #', 'Campaign Id', 'Campaign Name', 'Status', 'Contact', 'Title Company', 'Send Connections', 'Next Step', 'Executed Time']);

                if (!$leads->isEmpty()) {
                    $count = 1;
                    foreach ($leads as $lead) {
                        $csv->insertOne([
                            $count++,
                            $campaign->id,
                            $campaign->campaign_name,
                            $lead->is_active == '1' ? 'Active' : 'Not Active',
                            $lead->contact,
                            $lead->title_company,
                            $lead->send_connections == '1' ? 'Connected' : 'Disconnected',
                            $lead->next_step,
                            $lead->executed_time
                        ]);
                    }
                } else {
                    $csv->insertOne(['No Lead Found', '', '', '', '', '', '', '', '']);
                }
                $csvContent = $csv->getContent();
                Storage::put($uploadFilePath, $csvContent);
                $filePaths[] = $uploadFilePath;
            }
            Mail::send([], [], function ($message) use ($email, $filePaths) {
                $message->to($email)
                    ->subject('Your Leads CSVs');
                $count = 1;
                foreach ($filePaths as $filePath) {
                    $message->attach(Storage::path($filePath), [
                        'as' => 'Attachment # ' . $count++,
                        'mime' => 'text/csv',
                    ]);
                }
            });
        }
        return response()->json(['success' => true]);
    }

    function getLeadsCountByCampaign($campaign_id)
    {
        $user_id = Auth::user()->id;
        $leads = Leads::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
        return response()->json(['success' => true, 'count' => count($leads)]);
    }

    function getViewProfileByCampaign($campaign_id)
    {
        $count = 0;
        $user_id = Auth::user()->id;
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'view_profile%')->get();
        foreach ($campaign_elements as $element) {
            $leads = LeadActions::where('current_element_id', $element->id)->where('status', 'completed')->get();
            $count += count($leads);
        }
        return response()->json(['success' => true, 'count' => $count]);
    }

    function getInviteToConnectByCampaign($campaign_id)
    {
        $count = 0;
        $user_id = Auth::user()->id;
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'invite_to_connect%')->get();
        foreach ($campaign_elements as $element) {
            $leads = LeadActions::where('current_element_id', $element->id)->where('status', 'completed')->get();
            $count += count($leads);
        }
        return response()->json(['success' => true, 'count' => $count]);
    }
    
    function getSentMessageByCampaign($campaign_id)
    {
        $count = 0;
        $user_id = Auth::user()->id;
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'message%')->get();
        foreach ($campaign_elements as $element) {
            $leads = LeadActions::where('current_element_id', $element->id)->where('status', 'completed')->get();
            $count += count($leads);
        }
        return response()->json(['success' => true, 'count' => $count]);
    }
    
    function getSentEmailByCampaign($campaign_id)
    {
        $count = 0;
        $user_id = Auth::user()->id;
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'email_message%')->get();
        foreach ($campaign_elements as $element) {
            $leads = LeadActions::where('current_element_id', $element->id)->where('status', 'completed')->get();
            $count += count($leads);
        }
        return response()->json(['success' => true, 'count' => $count]);
    }
}
