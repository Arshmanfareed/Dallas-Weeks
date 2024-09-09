<?php

namespace App\Http\Controllers;

use App\Models\AssignedSeats;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use App\Models\Campaign;
use App\Models\LinkedinSetting;
use App\Models\LeadActions;
use App\Models\Leads;
use App\Models\ImportedLeads;
use App\Models\GlobalSetting;
use App\Models\EmailSetting;
use App\Models\UpdatedCampaignProperties;
use App\Models\CampaignPath;
use App\Models\UpdatedCampaignElements;
use App\Models\CampaignActions;
use App\Models\Permissions;
use App\Models\PhysicalPayment;
use App\Models\Role_Permission;
use App\Models\Roles;
use App\Models\Teams;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SeatController extends Controller
{
    /**
     * Retrieve seat details by seat ID for the authenticated user.
     *
     * @param int $seat_id The ID of the seat to retrieve.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If the seat is not found or any error occurs during processing.
     */
    public function get_seat_by_id($seat_id)
    {
        try {
            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::findOrFail($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->firstOrFail();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->firstOrFail();

            /* Get the user's role based on the assigned seat */
            $role = Roles::findOrFail($assignedSeat->role_id);

            /* Retrieve permissions and role permissions for manage and delete seat actions */
            $allow_manage_settings = $this->checkPermission($role->id, 'manage_seat_settings');
            $allow_delete_seat = $this->checkPermission($role->id, 'delete_seat');
            $allow_delete_seat = true; //Only for test

            /* Check if the seat was found */
            if (!empty($seat)) {
                /* Return a JSON response with seat details and permission statuses */
                return response()->json([
                    'success' => true,
                    'seat' => $seat,
                    'allow_manage_settings' => $allow_manage_settings,
                    'allow_delete_seat' => $allow_delete_seat
                ]);
            }

            /* If seat is not found, throw an exception */
            throw new Exception('Seat Not Found', 404);
        } catch (ModelNotFoundException $e) {
            /* Return a 404 response if any model (SeatInfo, AssignedSeats, Role) is not found */
            return response()->json(['success' => false, 'errors' => 'Not Found'], 404);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and a 404 status code */
            return response()->json(['success' => false, 'errors' => $e->getMessage()], $e->getCode());
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

    /**
     * Update the seat name for a specific seat ID.
     *
     * @param int $seat_id The ID of the seat to update.
     * @param string $seat_name The new name for the seat.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating success or failure.
     */
    public function update_name($seat_id, $seat_name)
    {
        try {
            /* Get the authenticated user */
            $user = Auth::user();

            /* Find the seat associated with the user and the provided seat ID */
            $seat = SeatInfo::where('user_id', $user->id)
                ->where('id', $seat_id)
                ->firstOrFail();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->firstOrFail();

            /* Get the user's role based on the assigned seat */
            $role = Roles::findOrFail($assignedSeat->role_id);

            /* Retrieve permissions and role permissions for managing seat settings */
            $allow_manage_settings = $this->checkPermission($role->id, 'manage_seat_settings');

            /* Check if the user has permission to manage seat settings */
            if ($allow_manage_settings == true) {
                /* Update the seat's name */
                $seat->username = $seat_name;

                /* Save the changes to the database */
                if ($seat->save()) {
                    /* Return a JSON response indicating success and the updated seat */
                    return response()->json(['success' => true, 'seat' => $seat]);
                }

                /* If saving failed, throw an exception */
                throw new Exception('Seat Updation Failed', 500);
            }

            /* If the user does not have permission, throw an exception */
            throw new Exception('Permission Denied', 403);
        } catch (ModelNotFoundException $e) {
            /* Handle not found exceptions for seat or role */
            Log::info($e);

            /* Return a JSON response with the error message and status code */
            return response()->json(['success' => false, 'errors' => 'Not Found'], 404);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and status code */
            return response()->json(['success' => false, 'errors' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Delete a seat and its associated data.
     *
     * @param int $seat_id The ID of the seat to delete.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating success or failure.
     */
    public function delete_seat($seat_id)
    {
        try {
            /* Get the ID of the authenticated user */
            $user = Auth::user();

            /* Find the seat associated with the user and the provided seat ID */
            $seat = SeatInfo::where('user_id', $user->id)
                ->where('id', $seat_id)
                ->firstOrFail();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->firstOrFail();

            /* Get the user's role based on the assigned seat */
            $role = Roles::findOrFail($assignedSeat->role_id);

            /* Retrieve permissions and role permissions for managing seat settings */
            $allow_delete_seat = $this->checkPermission($role->id, 'delete_seat');

            /* Check if the user has permission to delete seat */
            if ($allow_delete_seat == true) {
                /* If the seat has an associated account ID, proceed with account deletion */
                if (!empty($seat['account_id'])) {
                    /* Prepare request data for deleting the account */
                    $request = ['account_id' => $seat['account_id']];
                    $uc = new UnipileController();

                    /* Attempt to delete the account */
                    $account = $uc->delete_account(new \Illuminate\Http\Request($request));

                    /* Check if the account deletion was successful */
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        /* Retrieve associated campaigns and their IDs */
                        $campaigns = Campaign::where('seat_id', $seat->id)->get();
                        $campaign_ids = $campaigns->pluck('id')->toArray();

                        /* Delete associated records from various models */
                        PhysicalPayment::where('product_id', $seat->id)->delete();
                        LinkedinSetting::whereIn('campaign_id', $campaign_ids)->delete();
                        LeadActions::whereIn('campaign_id', $campaign_ids)->delete();
                        Leads::whereIn('campaign_id', $campaign_ids)->delete();
                        ImportedLeads::whereIn('campaign_id', $campaign_ids)->delete();
                        GlobalSetting::whereIn('campaign_id', $campaign_ids)->delete();
                        EmailSetting::whereIn('campaign_id', $campaign_ids)->delete();
                        UpdatedCampaignProperties::whereIn('campaign_id', $campaign_ids)->delete();
                        CampaignPath::whereIn('campaign_id', $campaign_ids)->delete();
                        UpdatedCampaignElements::whereIn('campaign_id', $campaign_ids)->delete();
                        CampaignActions::whereIn('campaign_id', $campaign_ids)->delete();
                        Campaign::whereIn('id', $campaign_ids)->delete();
                    } else {
                        /* Throw an exception if account deletion failed */
                        throw new Exception('Seat Deletion failed', 500);
                    }
                }
                /* Delete the seat record */
                $seat->delete();

                /* Return a JSON response indicating success */
                return response()->json(['success' => true, 'seat' => $seat_id]);
            }

            /* If the user does not have permission, throw an exception */
            throw new Exception('Permission Denied', 403);
        } catch (ModelNotFoundException $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and status code */
            return response()->json(['success' => false, 'errors' => 'Not Found'], 404);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and status code */
            return response()->json(['success' => false, 'errors' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Filter seats based on the search term and retrieve additional account information.
     *
     * @param string $search The search term to filter seat names.
     * @return \Illuminate\Http\JsonResponse The JSON response with the filtered seats and their statuses.
     */
    public function filterSeat($search)
    {
        try {
            /* Get the ID of the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::findOrFail($user->team_id);

            /* Retrieve seats associated with the user, applying search filter if necessary */
            $seats = SeatInfo::where('team_id', $team->id)
                ->when($search !== 'null', function ($query) use ($search) {
                    return $query->where('username', 'LIKE', '%' . $search . '%');
                })
                ->get();
            $uc = new UnipileController();

            /* Process seats */
            $seats = $seats->map(function ($seat) use ($user, $uc) {
                /* Default values for seat connection and activity status */
                $seat['connected'] = false;
                $seat['active'] = false;

                /* Retrieve Assigned seats */
                $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                /* Check that if seat is assigned or not */
                if (!empty($assignedSeat)) {
                    /* If seat has an associated account ID, retrieve related account information */
                    if (!empty($seat['account_id'])) {
                        $request = ['account_id' => $seat['account_id']];

                        /* Retrieve account details */
                        $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                        if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                            $seat['active'] = true;
                            $seat['account'] = $account->getData(true)['account'];
                        }

                        /* Retrieve profile details for the account */
                        $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
                        if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                            $seat['connected'] = true;
                            $seat['account_profile'] = $account->getData(true)['account'];
                        }
                    }
                    return $seat;
                }
                return null;
            })->filter();

            /* Check if any seats were found and return the response */
            if (count($seats) > 0) {
                return response()->json(['success' => true, 'seats' => $seats]);
            }

            /* Throw an exception if no seats were found */
            throw new Exception('Seats Not Found', 404);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and status code */
            return response()->json(['success' => false, 'errors' => $e->getMessage()], $e->getCode());
        }
    }

    public function get_final_accounts()
    {
        $seats = SeatInfo::whereNotNull('account_id')->get();
        $final_accounts = [];
        $uc = new UnipileController();
        for ($i = 0; $i < count($seats); $i++) {
            $account_id = [
                'account_id' => $seats[$i]['account_id'],
            ];
            $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($account_id));
            $account = $account->getData(true);
            if (array_key_exists($account['account']['connection_params']['im']['id'], $final_accounts)) {
                $final_accounts[$account['account']['connection_params']['im']['id']][] = $seats[$i]['account_id'];
            } else {
                $final_accounts[$account['account']['connection_params']['im']['id']] = [];
                $final_accounts[$account['account']['connection_params']['im']['id']][] = $seats[$i]['account_id'];
            }
        }
        return $final_accounts;
    }
}
