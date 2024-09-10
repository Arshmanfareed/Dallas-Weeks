<?php

namespace App\Http\Controllers;

use App\Models\AssignedSeats;
use App\Models\Roles;
use App\Models\Permissions;
use Exception;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamController extends Controller
{
    /**
     * Retrieve and prepare team members with their associated roles.
     *
     * @return \Illuminate\View\View
     */
    function team()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the team associated with the team member */
            $team = Teams::findOrFail($user->team_id);

            /* Retrieve all users in the same team */
            $users = User::where('team_id', $team->id)->get();

            /* Map through each user to attach their roles */
            $teamMembers = $users->map(function ($user) {
                /* Initialize roles list for the user */
                $rolesList = [];

                /* Get all assigned seats for the user */
                $assignedSeats = AssignedSeats::where('user_id', $user->id)->get();

                foreach ($assignedSeats as $assignedSeat) {
                    /* Find the role associated with the assigned seat */
                    $role = Roles::find($assignedSeat->role_id);

                    if ($role) {
                        /* Add the role name to the roles list array */
                        $rolesList[] = ['role_name' => $role->role_name];
                    }
                }

                /* Attach the roles list to the user object */
                $user->roles = $rolesList;

                /* Return the user with additional roles data */
                return $user;
            });

            /* Get the assigned seat for the user where seat_id is 0 
            (potentially indicating an unassigned seat or default seat)
            */
            $assignedSeat = AssignedSeats::where('seat_id', 0)
                ->where('user_id', $user->id)
                ->first();

            /* Check if the user is the owner of the seat (is assigned seat_id 0) */
            $is_owner = false;
            if (!empty($assignedSeat)) {
                $is_owner = true;
            }

            $roles = Roles::whereIn('team_id', [0, $user->team_id])->get();

            /* Fetch all available permissions */
            $permissions = Permissions::all();

            /* Prepare data for the view */
            $data = [
                'title' => 'Team Dashboard',
                'team_members' => $teamMembers,
                'is_owner' => $is_owner,
                'permissions' => $permissions,
                'roles' => $roles
            ];

            /* Return the view with the prepared data */
            return view('team', $data);
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
}
