<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatEmail;
use App\Models\SeatInfo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Teams;
use App\Models\GlobalPermission;
use App\Models\Role_Permission;
use App\Models\Permissions;
use App\Models\AssignedSeats;
use App\Models\Roles;

class SettingController extends Controller
{
    function settingrolespermission()
    {
        try {
            $user = Auth::user();
            $team = Teams::find($user->team_id);
            $is_manage_payment_system = false;
            $manage_payment_system = GlobalPermission::where('permission_slug', 'manage_payment_system')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_payment_system)) {
                $is_manage_payment_system = true;
            }
            $is_manage_global_blacklist = false;
            $manage_global_blacklist = GlobalPermission::where('permission_slug', 'manage_global_blacklist')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_global_blacklist)) {
                $is_manage_global_blacklist = true;
            }
            $data = [
                'title' => 'Setting',
                'is_manage_payment_system' => $is_manage_payment_system,
                'is_manage_global_blacklist' => $is_manage_global_blacklist,
            ];
            return view('setting', $data);
        } catch (Exception $e) {
            Log::info($e);
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }

    function setting()
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');
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
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();

            /* Check if the user has an assigned seat. */
            if (!empty($assignedSeat)) {
                /* Get the user's role based on the assigned seat */
                $role = Roles::find($assignedSeat->role_id);

                $paymentStatus = PhysicalPayment::where('user_id', $user->id)->where('product_id', $seat_id)->value('physical_payment_status');
                $seat = SeatInfo::find($seat_id);
                $data['title'] = 'Setting';
                $data['paymentStatus'] = $paymentStatus;
                $data['seat_id'] = $seat_id;
                $uc = new UnipileController();
                $emails = SeatEmail::where('user_id', $user->id)->where('seat_id', $seat_id)->get();

                foreach ($emails as $key => $email) {
                    $request = ['account_id' => $email['email_id']];
                    $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $account = $account->getData(true);
                        $emails[$key]['account'] = $account['account'];
                    } else {
                        unset($emails[$key]);
                        continue;
                    }
                    $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                        $account = $account->getData(true);
                        $emails[$key]['profile'] = $account['account'];
                    } else {
                        unset($emails[$key]);
                        continue;
                    }
                }

                $data['emails'] = $emails;
                $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
                $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
                $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
                $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
                $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
                $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
                $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
                return view('settings', $data);
            }

            /* If the user does not have permission, throw an exception */
            throw new Exception('Permission Denied', 403);
        } catch (Exception $e) {
            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect to login with error message */
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
