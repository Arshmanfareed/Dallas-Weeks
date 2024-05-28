<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leads;
use League\Csv\Writer;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    function getLeadsByCampaign($id, $search)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
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
            }
            $leads = $leads->get();
            if (!$leads->isEmpty()) {
                return response()->json(['success' => true, 'leads' => $leads, 'campaign' => $campaign]);
            } else {
                return response()->json(['success' => false, 'message' => 'Leads not found!', 'campaign' => $campaign]);
            }
        } else {
            return redirect(url('/'));
        }
    }

    function sendLeadsToEmail(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $all = $request->all();
            $email = $all['email'];
            $campaign_id = $all['campaign_id'];
            if ($campaign_id != 'all') {
                $campaigns = Campaign::where('user_id', $user_id)->where('id', $campaign_id)->get();
            } else {
                $campaigns = Campaign::where('user_id', $user_id)->get();
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
        } else {
            return redirect(url('/'));
        }
    }
    function getLeadsCountByCampaign($campaign_id)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $leads = Leads::where('user_id', $user_id)->where('campaign_id', $campaign_id)->get();
            return response()->json(['success' => true, 'count' => count($leads)]);
        } else {
            return redirect(url('/'));
        }
    }
}
