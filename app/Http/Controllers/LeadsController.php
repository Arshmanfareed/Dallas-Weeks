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
use App\Models\CampaignPath;
use App\Models\EmailSetting;
use App\Models\GlobalSetting;
use App\Models\LinkedinSetting;
use Exception;

class LeadsController extends Controller
{
    function leads()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $campaigns = Campaign::where('user_id', $user_id)->where('seat_id', $seat_id)->where('is_archive', 0)->get();
        $leads = Leads::whereIn('campaign_id', $campaigns->pluck('id')->toArray())->get();
        $data = [
            'title' => 'Leads',
            'leads' => $leads,
            'campaigns' => $campaigns,
        ];
        return view('leads', $data);
    }

    function getLeadsByCampaign($id, $search)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $lead = null;
        $campaign = null;
        $email_setting = null;
        $linkedin_setting = null;
        $global_setting = null;
        if ($search != 'null' && $id != 'all') {
            $campaign = Campaign::where('user_id', $user_id)->where('id', $id)->first();
            $lead = Leads::where('user_id', $user_id)
                ->where(function ($query) use ($search) {
                    $query->where('contact', 'LIKE', '%' . $search . '%')
                        ->orWhere('title_company', 'LIKE', '%' . $search . '%');
                })
                ->where('campaign_id', $campaign->id)
                ->get();
            $email_setting = EmailSetting::where('campaign_id', $campaign->id)->get();
            $linkedin_setting = LinkedinSetting::where('campaign_id', $campaign->id)->get();
            $global_setting = GlobalSetting::where('campaign_id', $campaign->id)->get();
        } else if ($id != 'all') {
            $campaign = Campaign::where('user_id', $user_id)->where('id', $id)->first();
            $lead = Leads::where('user_id', $user_id)->where('campaign_id', $campaign->id)->get();
            $email_setting = EmailSetting::where('campaign_id', $campaign->id)->get();
            $linkedin_setting = LinkedinSetting::where('campaign_id', $campaign->id)->get();
            $global_setting = GlobalSetting::where('campaign_id', $campaign->id)->get();
        } else if ($search != 'null') {
            $campaign = Campaign::where('seat_id', $seat_id)->get();
            $lead = Leads::where('user_id', $user_id)
                ->where(function ($query) use ($search) {
                    $query->where('contact', 'LIKE', '%' . $search . '%')
                        ->orWhere('title_company', 'LIKE', '%' . $search . '%');
                })
                ->whereIn('campaign_id', $campaign->pluck('id')->toArray())
                ->get();
            $campaign = null;
        } else {
            $campaign = Campaign::where('seat_id', $seat_id)->get();
            $lead = Leads::where('user_id', $user_id)->whereIn('campaign_id', $campaign->pluck('id')->toArray())->get();
            $campaign = null;
        }
        $settings = [
            'email_setting' => $email_setting,
            'linkedin_setting' => $linkedin_setting,
            'global_setting' => $global_setting
        ];
        if (count($lead) > 0) {
            return response()->json(['success' => true, 'leads' => $lead, 'campaign' => $campaign, 'settings' => $settings]);
        } else {
            return response()->json(['success' => false, 'leads' => $lead, 'campaign' => $campaign, 'settings' => $settings]);
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

    function getLeadsCountByCampaign($user_id, $campaign_id)
    {
        $lead_count = Leads::where('user_id', $user_id)->where('campaign_id', $campaign_id)->count();
        return $lead_count;
    }

    function getViewProfileByCampaign($user_id, $campaign_id)
    {
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'view_profile%')->get();
        $view_action_count = LeadActions::whereIn('current_element_id', $campaign_elements->pluck('id')->toArray())->where('status', 'completed')->count();
        return $view_action_count;
    }

    function getInviteToConnectByCampaign($user_id, $campaign_id)
    {
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'invite_to_connect%')->get();
        $invite_action_count = LeadActions::whereIn('current_element_id', $campaign_elements->pluck('id')->toArray())->where('status', 'completed')->count();
        return $invite_action_count;
    }

    function getSentMessageByCampaign($user_id, $campaign_id)
    {
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'message%')->get();
        $message_count = LeadActions::whereIn('current_element_id', $campaign_elements->pluck('id')->toArray())->where('status', 'completed')->count();
        return $message_count;
    }

    function getSentEmailByCampaign($user_id, $campaign_id)
    {
        $campaign_elements = UpdatedCampaignElements::where('user_id', $user_id)->where('campaign_id', $campaign_id)->where('element_slug', 'like', 'email_message%')->get();
        $email_count = LeadActions::whereIn('current_element_id', $campaign_elements->pluck('id')->toArray())->where('status', 'completed')->count();
        return $email_count;
    }

    function duplicateUrl($url)
    {
        $seat_id = session('seat_id');
        $campaigns = Campaign::where('seat_id', $seat_id)->get();
        foreach ($campaigns as $campaign) {
            $leads = Leads::where('campaign_id', $campaign->id)->get();
            foreach ($leads as $lead) {
                if (stripos($lead->profileUrl, $url) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    function removeLeadPendingConnections($campaign, $url)
    {
        $campaigns = Campaign::where('seat_id', $campaign['seat_id'])->get();
        foreach ($campaigns as $campaign) {
            $campaign_elements = UpdatedCampaignElements::where('campaign_id', $campaign->id)->where('element_slug', 'like', 'invite_to_connect%')->get();
            foreach ($campaign_elements as $element) {
                $lead_actions = LeadActions::where('current_element_id', $element->id)->where('status', 'inprogress')->get();
                foreach ($lead_actions as $action) {
                    $lead = Leads::where('id', $action->lead_id)->first();
                    if (stripos($lead->profileUrl, $url) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function removeExistingLead($campaign, $url)
    {
        $campaigns = Campaign::where('seat_id', $campaign['seat_id'])->get();
        foreach ($campaigns as $campaign) {
            $leads = Leads::where('campaign_id', $campaign->id)->get();
            foreach ($leads as $lead) {
                if (stripos($lead->profileUrl, $url) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    function applySettings($campaign, $url)
    {
        // global_settings_include_leads_that_replied_to_your_messages
        try {
            $lsc = new LinkedinSettingController();
            $discover_new_leads_only = $lsc->get_value_of_setting($campaign->id, 'global_settings_discover_new_leads_only');
            if (($discover_new_leads_only && !$this->removeExistingLead($campaign, $url)) || !$discover_new_leads_only) {
                $should_remove_leads_pending = $lsc->get_value_of_setting($campaign->id, 'linkedin_settings_remove_leads_with_pending_connections');
                if (($should_remove_leads_pending && !$this->removeLeadPendingConnections($campaign, $url)) || !$should_remove_leads_pending) {
                    $seat = SeatInfo::where('id', $campaign->seat_id)->first();
                    $account_id = $seat['account_id'];
                    $uc = new UnipileController();
                    $profile = [
                        'account_id' => $account_id,
                        'profile_url' => $url,
                    ];
                    $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
                    if ($user_profile instanceof JsonResponse) {
                        $user_profile = $user_profile->getData(true);
                        if (!isset($user_profile['error'])) {
                            $user_profile = $user_profile['user_profile'];
                            $should_discover_lead_with_open_profile = $lsc->get_value_of_setting($campaign->id, 'linkedin_settings_discover_leads_with_open_profile_status_only');
                            if (($should_discover_lead_with_open_profile && isset($user_profile['can_send_inmail']) && $user_profile['is_premium']) || !$should_discover_lead_with_open_profile) {
                                $should_only_premium = $lsc->get_value_of_setting($campaign->id, 'linkedin_settings_discover_premium_linked_accounts_only');
                                if (($should_only_premium && $user_profile['is_premium']) || !$should_only_premium) {
                                    $lead = new Leads();
                                    $lead->is_active = 1;
                                    $lead->contact = '';
                                    $lead->title_company = '';
                                    $lead->send_connections = 'discovered';
                                    $lead->next_step = '';
                                    $lead->executed_time = date('H:i:s');
                                    $lead->campaign_id = $campaign->id;
                                    $lead->user_id = $campaign->user_id;
                                    $lead->created_at = now();
                                    $lead->updated_at = now();
                                    $lead->profileUrl = $url;
                                    if (isset($user_profile['first_name']) && isset($user_profile['last_name'])) {
                                        $name = $user_profile['first_name'] . ' ' . $user_profile['last_name'];
                                        $name = ucwords($name);
                                        $lead->title_company = $name;
                                    }
                                    if (isset($user_profile['name'])) {
                                        $name = $user_profile['name'];
                                        $lead->title_company = $name;
                                    }
                                    if ($lsc->get_value_of_setting($campaign->id, 'linkedin_settings_collect_contact_information')) {
                                        if (isset($user_profile['contact_info']['phones'][0])) {
                                            $contact = $user_profile['contact_info']['phones'][0];
                                            $lead->contact = $contact;
                                        }
                                        if (isset($user_profile['contact_info']['emails'][0])) {
                                            $email = $user_profile['contact_info']['emails'][0];
                                            $lead->email = $email;
                                        }
                                        if (isset($user_profile['adresses'][0])) {
                                            $address = $user_profile['adresses'][0];
                                            $lead->address = $address;
                                        }
                                        if (isset($user_profile['websites'][0])) {
                                            $website = $user_profile['websites'][0];
                                            $lead->website = $website;
                                        }
                                    }
                                    $lead->save();
                                    if (isset($lead->id)) {
                                        $lead_action = new LeadActions();
                                        $campaign_path = CampaignPath::where('campaign_id', $campaign->id)->orderBy('id')->first();
                                        $lead_action->current_element_id = 'step_1';
                                        $lead_action->next_true_element_id = $campaign_path->current_element_id;
                                        $lead_action->campaign_id = $campaign->id;
                                        $lead_action->next_false_element_id = '';
                                        $lead_action->created_at = now();
                                        $lead_action->updated_at = now();
                                        $lead_action->status = 'inprogress';
                                        $lead_action->lead_id = $lead->id;
                                        $lead_action->ending_time = now();
                                        $lead_action->save();
                                        if (isset($lead_action->id)) {
                                            return true;
                                        } else {
                                            $lead->delete();
                                            return false;
                                        }
                                    } else {
                                        return false;
                                    }
                                } else {
                                    return false;
                                }
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    function get_view_count($campaigns)
    {
        $past_time = now()->modify('-1 days')->format('Y-m-d');
        $views = UpdatedCampaignElements::whereIn('campaign_id', $campaigns->pluck('id')->toArray())->where('element_slug', 'like', 'view_profile%')->get();
        $views = LeadActions::whereIn('current_element_id', $views->pluck('id')->toArray())->whereDate('updated_at', '>=', $past_time)->where('status', 'completed')->get();
        $view_count = floor((80 - count($views)) / count($campaigns));
        if ($view_count > 0) {
            return $view_count;
        }
        return 0;
    }

    function get_invite_count($campaigns)
    {
        $past_time = now()->modify('-1 days')->format('Y-m-d');
        $invites = UpdatedCampaignElements::whereIn('campaign_id', $campaigns->pluck('id')->toArray())->where('element_slug', 'like', 'invite_to_connect%')->get();
        $invites = LeadActions::whereIn('current_element_id', $invites->pluck('id')->toArray())->whereDate('updated_at', '>=', $past_time)->where('status', 'completed')->get();
        $invite_count = floor((15 - count($invites)) / count($campaigns));
        if ($invite_count > 0) {
            return $invite_count;
        }
        return 0;
    }

    function get_message_count($campaigns)
    {
        $past_time = now()->modify('-1 days')->format('Y-m-d');
        $messages = UpdatedCampaignElements::whereIn('campaign_id', $campaigns->pluck('id')->toArray())->where('element_slug', 'like', 'message%')->get();
        $messages = LeadActions::whereIn('current_element_id', $messages->pluck('id')->toArray())->whereDate('updated_at', '>=', $past_time)->where('status', 'completed')->get();
        $message_count = floor((80 - count($messages)) / count($campaigns));
        if ($message_count > 0) {
            return $message_count;
        }
        return 0;
    }
}
