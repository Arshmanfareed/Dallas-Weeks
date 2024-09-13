<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Exception;
use App\Models\Role_Permission;
use App\Models\Permissions;
use App\Models\Teams;
use Illuminate\Support\Facades\Log;
use App\Models\AssignedSeats;
use App\Models\Roles;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Models\Campaign;

class MaindashboardController extends Controller
{
    /**
     * Retrieve seat details by seat ID for the authenticated user.
     *
     * @param int $seat_id The ID of the seat to retrieve.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If the seat is not found or any error occurs during processing.
     */
    function maindasboard(Request $request)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = $request->input('seat_id', session('seat_id'));
            if ($seat_id) {
                /* Store seat_id in session if found */
                session(['seat_id' => $seat_id]);
            } else {
                /* Throw an exception if seat_id is not found */
                throw new Exception('Seat Not Found');
            }

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::findOrFail($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->firstOrFail();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::findOrFail($assignedSeat->role_id);

            /* Initialize seat status as inactive and not connected. */
            $seat['active'] = false;
            $seat['connected'] = false;

            /* If the seat has an account_id, retrieve and process account information. */
            if (!empty($seat['account_id'])) {
                $uc = new UnipileController();
                $request = ['account_id' => $seat['account_id']];

                /* Call UnipileController to retrieve the account data. */
                $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));

                /* If account retrieval is successful, set the seat as active and store account details in session. */
                if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                    $seat['active'] = true;
                    $seat['account'] = $account->getData(true)['account'];
                    session(['account' => $seat['account']]);
                }

                /* Retrieve the user's own profile associated with the account. */
                $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));

                /* If profile retrieval is successful, fetch LinkedIn profile and store profile data in session. */
                if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                    $account = $account->getData(true)['account'];
                    $profile_url = 'https://www.linkedin.com/in/' . $account['provider_id'];
                    $request = [
                        'account_id' => $seat['account_id'],
                        'profile_url' => $profile_url
                    ];

                    /* View the profile of the account using UnipileController. */
                    $account = $uc->view_profile(new \Illuminate\Http\Request($request));

                    /* If profile viewing is successful, store account profile data in session. */
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $seat['account_profile'] = $account->getData(true)['user_profile'];
                        session(['account_profile' => $seat['account_profile']]);
                    }

                    /* Mark the seat as connected. */
                    $seat['connected'] = true;
                }
            }

            /* If the seat is both connected and active, retrieve additional data like campaigns, chats, and relations. */
            if ($seat['connected'] && $seat['active']) {
                $lc = new LeadsController();

                /* Retrieve the latest 10 campaigns associated with the seat and fetch lead counts for each campaign. */
                $campaigns = Campaign::where('seat_id', $seat_id)->orderBy('is_active', 'desc')->limit(10)->get();
                foreach ($campaigns as $campaign) {
                    $campaign['lead_count'] = $lc->getLeadsCountByCampaign($user->id, $campaign->id);
                }

                /* Prepare a request to list all chats (limited to 10) associated with the account. */
                $request = [
                    'account_id' => $seat['account_id'],
                    'limit' => 10,
                ];
                $chats = $uc->list_all_chats(new \Illuminate\Http\Request($request));

                /* If chats are successfully retrieved, store them in the chats variable, otherwise set to an empty array. */
                if ($chats instanceof JsonResponse && !isset($chats->getData(true)['error'])) {
                    $chats = $chats->getData(true)['chats']['items'];
                } else {
                    $chats = array();
                }

                /* Prepare a request to list the latest 3 relations associated with the account. */
                $request = [
                    'account_id' => $seat['account_id'],
                    'limit' => 3,
                ];
                $relations = $uc->list_all_relations(new \Illuminate\Http\Request($request));

                /* If relations are successfully retrieved, store them in the relations variable, otherwise set to an empty array. */
                if ($relations instanceof JsonResponse && !isset($relations->getData(true)['error'])) {
                    $relations = $relations->getData(true)['relations']['items'];
                } else {
                    $relations = array();
                }

                /* Prepare data to be passed to the main-dashboard view. */
                $data['title'] = 'Account Dashboard';
                $data['campaigns'] = $campaigns;
                $data['seat'] = $seat;
                $data['chats'] = $chats;
                $data['relations'] = $relations;
                $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
                $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
                $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
                $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
                $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
                $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');

                /* Return the main-dashboard view with the fetched data. */
                return view('main-dashboard', $data);
            }

            /* If the seat is not connected or inactive, redirect to dashboard settings to add an account. */
            session(['add_account' => true]);
            return redirect(route('dash-settings'));
        } catch (ModelNotFoundException $e) {
            /* Return a 404 response if any model (SeatInfo, AssignedSeats, Role) is not found */
            return redirect()->route('dashobardz')->withErrors(['error' => 'Not Found']);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and a 404 status code */
            return redirect()->route('dashobardz')->withErrors(['error' => $e->getMessage()]);
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
