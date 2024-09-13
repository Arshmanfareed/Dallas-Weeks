<?php

namespace App\Http\Controllers;

use App\Models\CampaignElement;
use App\Models\ElementProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Roles;
use App\Models\Teams;
use App\Models\AssignedSeats;
use App\Models\SeatInfo;
use Illuminate\Support\Facades\Log;
use App\Models\Role_Permission;
use App\Models\Permissions;

class PropertiesController extends Controller
{
    function getPropertyDatatype($id, $element_slug)
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
            $string = $element_slug;
            $element = CampaignElement::where('element_slug', $string)->first();
            if ($element) {
                $property = ElementProperties::where('element_id', $element->id)->where('id', $id)->first();
                if ($property) {
                    return response()->json(['success' => true, 'property' => $property]);
                } else {
                    return response()->json(['success' => false, 'property' => 'Properties not found!']);
                }
            } else {
                return response()->json(['success' => false, 'properties' => 'Element not found!' . $string]);
            }
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function getPropertyRequired($id)
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
            $property = ElementProperties::where('id', $id)->first();
            if ($property) {
                return response()->json(['success' => true, 'property' => $property]);
            } else {
                return response()->json(['success' => false, 'property' => 'Properties not found!']);
            }
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
