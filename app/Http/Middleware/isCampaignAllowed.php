<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Teams;
use App\Models\SeatInfo;
use App\Models\AssignedSeats;
use App\Models\Roles;
use App\Models\Permissions;
use App\Models\Role_Permission;
use Exception;

class isCampaignAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
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
            if ($data['manage_campaigns'] == true || $data['manage_campaigns'] == 'view_only') {
                return $next($request);
            }
            /* If the user does not have permission, throw an exception */
            throw new Exception('You can not access campaigns', 403);
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
