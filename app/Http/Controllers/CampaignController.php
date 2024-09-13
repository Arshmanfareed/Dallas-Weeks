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
use Exception;
use App\Models\Roles;
use App\Models\Teams;
use App\Models\AssignedSeats;
use App\Models\SeatEmail;
use Illuminate\Support\Facades\Log;
use App\Models\Role_Permission;
use App\Models\Permissions;

class CampaignController extends Controller
{
    function campaign()
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();

            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $lc = new LeadsController();
            $campaigns = Campaign::where('seat_id', $seat_id)->where('is_active', 1)->where('is_archive', 0)->get();
            foreach ($campaigns as $campaign) {
                $campaign['lead_count'] = $lc->getLeadsCountByCampaign($user->id, $campaign->id);
                $campaign['view_action_count'] = $lc->getViewProfileByCampaign($user->id, $campaign->id);
                $campaign['invite_action_count'] = $lc->getInviteToConnectByCampaign($user->id, $campaign->id);
                $campaign['message_count'] = $lc->getSentMessageByCampaign($user->id, $campaign->id);
                $campaign['email_action_count'] = $lc->getSentEmailByCampaign($user->id, $campaign->id);
            }
            $campaigns = $campaigns->sortByDesc('lead_count')->values();
            $data['title'] = 'Campaign';
            $data['campaigns'] = $campaigns;
            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            return view('campaign', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function campaigncreate()
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();

            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['title'] = 'Create Campaign';
            return view('campaigncreate', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function campaigninfo(Request $request)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);


            $validated = $request->validate([
                'campaign_name' => 'required|string|max:255',
                'campaign_url' => 'required'
            ]);
            if ($validated) {
                $all = $request->except('_token');
                $uc = new UnipileController();
                $schedules = CampaignSchedule::where('user_id', $user->id)->orWhere('user_id', 0)->get();
                $emails = SeatEmail::where('user_id', $user->id)->where('seat_id', $seat_id)->get();
                foreach ($emails as $key => $email) {
                    $request = ['account_id' => $email['email_id']];
                    $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $account = $account->getData(true);
                        $email['account'] = $account['account'];
                    } else {
                        unset($emails[$key]);
                        continue;
                    }
                    $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $account = $account->getData(true);
                        $email['profile'] = $account['account'];
                    } else {
                        unset($emails[$key]);
                        continue;
                    }
                }
                if ($all['campaign_type'] == 'linkedin' && strpos($all['campaign_url'], 'https://www.linkedin.com/search/results/people') === false) {
                    return redirect()->back()->withErrors(['campaign_url' => 'Invalid URL for LinkedIn search']);
                } else if ($all['campaign_type'] == 'sales_navigator' && strpos($all['campaign_url'], 'https://www.linkedin.com/sales/search/people') === false) {
                    return redirect()->back()->withErrors(['campaign_url' => 'Invalid URL for Sales Navigator search']);
                } else if ($all['campaign_type'] == 'leads_list' && strpos($all['campaign_url'], 'https://www.linkedin.com/sales/lists/people') === false) {
                    return redirect()->back()->withErrors(['campaign_url' => 'Invalid URL for Lead List search']);
                } else if ($all['campaign_type'] == 'post_engagement') {
                    preg_match('/activity-([0-9]+)/', $all['campaign_url'], $matches);
                    if (strpos($all['campaign_url'], 'https://www.linkedin.com/posts') === false) {
                        return redirect()->back()->withErrors(['campaign_url' => 'Invalid URL for Posts']);
                    } else if (!isset($matches[1])) {
                        return redirect()->back()->withErrors(['campaign_url' => 'Post must be activity']);
                    }
                }
                $campaign_details = [];
                foreach ($all as $key => $value) {
                    $campaign_details[$key] = $value;
                }
                $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
                $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
                $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
                $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
                $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
                $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                $data['title'] = 'Create Campaign Info';
                $data['campaign_details'] = $campaign_details;
                $data['campaign_schedule'] = $schedules;
                $data['emails'] = $emails;
                return view('createcampaigninfo', $data);
            }
            /* If the user does not have permission, throw an exception */
            throw new Exception('You can not add campaigns', 403);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function fromscratch(Request $request)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $all = $request->except('_token');
            $settings = [];
            foreach ($all as $key => $value) {
                $settings[$key] = $value;
            }
            $data['campaigns'] = CampaignElement::where('is_conditional', '0')->get();
            $data['conditional_campaigns'] = CampaignElement::where('is_conditional', '1')->get();
            $data['title'] = 'Create Campaign Info';
            $data['settings'] = $settings;
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            return view('createcampaignfromscratch', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function getCampaignDetails($campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['campaign'] = Campaign::where('id', $campaign_id)->first();
            return view('campaignDetails', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function changeCampaignStatus($campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
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
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function deleteCampaign($campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
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
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function archiveCampaign($campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
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
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function filterCampaign($filter, $search)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $lc = new LeadsController();
            $user_id = Auth::user()->id;
            $campaigns = Campaign::where('user_id', $user_id)->where('seat_id', session('seat_id'));
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
                foreach ($campaigns as $campaign) {
                    $campaign['lead_count'] = $lc->getLeadsCountByCampaign($user_id, $campaign->id);
                    $campaign['view_action_count'] = $lc->getViewProfileByCampaign($user_id, $campaign->id);
                    $campaign['invite_action_count'] = $lc->getInviteToConnectByCampaign($user_id, $campaign->id);
                    $campaign['message_count'] = $lc->getSentMessageByCampaign($user_id, $campaign->id);
                    $campaign['email_action_count'] = $lc->getSentEmailByCampaign($user_id, $campaign->id);
                }
                $campaigns = $campaigns->sortByDesc('lead_count')->values();
                return response()->json(['success' => true, 'campaigns' => $campaigns]);
            } else {
                return response()->json(['success' => false, 'campaigns' => 'Campaign not Found']);
            }
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function editCampaign($campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $user_id = Auth::user()->id;
            $campaign = Campaign::where('user_id', $user_id)->where('id', $campaign_id)->first();
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['title'] = 'Edit Campaign';
            $data['campaign'] = $campaign;
            return view('editCampaign', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function editCampaignInfo(Request $request, $campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
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
                $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
                $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
                $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
                $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
                $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                $data['title'] = 'Create Campaign Info';
                $data['email_settings'] = $email_settings;
                $data['linkedin_settings'] = $linkedin_settings;
                $data['global_settings'] = $global_settings;
                $data['campaign_details'] = $campaign_details;
                $data['campaign_schedule'] = $schedules;
                $data['campaign_id'] = $campaign_id;
                return view('editCampaignInfo', $data);
            }
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function editCampaignSequence(Request $request, $campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $all = $request->except('_token');
            $settings = [];
            foreach ($all as $key => $value) {
                $settings[$key] = $value;
            }
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['campaigns'] = CampaignElement::where('is_conditional', '0')->get();
            $data['conditional_campaigns'] = CampaignElement::where('is_conditional', '1')->get();
            $data['title'] = 'Edit Campaign Sequence';
            $data['settings'] = $settings;
            $data['campaign_id'] = $campaign_id;
            $data['campaign_time'] = Campaign::select('start_date')->where('id', $campaign_id)->first()->start_date;
            $data['img'] = Campaign::select('img_path')->where('id', $campaign_id)->first()->img_path;
            return view('editCampaignSequence', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function updateCampaign(Request $request, $campaign_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
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
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Check if the role has a given permission and return the access level.
     *
     * @param int $role_id The role ID to check.
     * @param string $permission_slug The permission slug to check.
     * @return bool|string True for full access, 'view_only' for view-only access, false for no access.
     */
    private function checkPermission($role_id, $permission_slug)
    {
        /* Fetch the permission for the given slug */
        $permission = Permissions::where('permission_slug', 'like', '%' . $permission_slug . '%')->first();

        if (!$permission) {
            return false;
        }

        /* Fetch the role permission relation */
        $rolePermission = Role_Permission::where('permission_id', $permission->id)
            ->where('role_id', $role_id)
            ->first();

        /* Check access and return appropriate status */
        if ($rolePermission && $rolePermission->view_only == 1) {
            return 'view_only';
        } elseif ($rolePermission && $rolePermission->access == 1) {
            return true;
        }

        return false;
    }
}
