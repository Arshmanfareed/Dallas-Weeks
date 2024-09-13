<?php

namespace App\Http\Controllers;

use App\Models\AssignedSeats;
use App\Models\Roles;
use App\Models\Permissions;
use Exception;
use App\Models\Teams;
use App\Models\SeatInfo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\GlobalPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class TeamController extends Controller
{
    /**
     * Retrieve and prepare team members with their associated roles.
     *
     * This method:
     * 
     * 1. Fetches the authenticated user and their team.
     * 2. Checks user permissions for managing payment systems and global blacklist.
     * 3. Retrieves all team members and their assigned roles.
     * 4. Checks if the user is an owner of an unassigned seat.
     * 5. Gathers all roles, permissions, and seat information for the team.
     * 6. Prepares data for the view and returns it.
     * 
     * @return \Illuminate\View\View The view with team and user data.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If any required model is not found.
     * @throws \Exception For general errors during execution.
     */
    function team()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the team associated with the team member */
            $team = Teams::find($user->team_id);

            /* Check user permissions for managing payment system and global blacklist */
            $is_manage_payment_system = false;
            $manage_payment_system = GlobalPermission::where('permission_slug', 'manage_payment_system')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_payment_system)) {
                $is_manage_payment_system = true;
            }

            /* Check user permissions for managing payment system and global blacklist */
            $is_manage_global_blacklist = false;
            $manage_global_blacklist = GlobalPermission::where('permission_slug', 'manage_global_blacklist')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_global_blacklist)) {
                $is_manage_global_blacklist = true;
            }

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

            /* Get the assigned seat for the user where seat_id is 0 (potentially indicating an unassigned seat or default seat) */
            $assignedSeat = AssignedSeats::where('seat_id', 0)
                ->where('user_id', $user->id)
                ->first();

            /* Check if the user is the owner of the seat (is assigned seat_id 0) */
            $is_owner = false;
            if (!empty($assignedSeat)) {
                $is_owner = true;
            }

            /* Retrieve roles, permissions, and seats for the team */
            $roles = Roles::whereIn('team_id', [0, $user->team_id])->get();

            /* Fetch all available permissions */
            $permissions = Permissions::all();

            /* Get all seats associated with the user's team */
            $seats = SeatInfo::where('team_id', $team->id)->get();
            $uc = new UnipileController();

            /* Process seats */
            $seats = $seats->map(function ($seat) use ($user, $uc) {
                /* Default values for seat connection and activity status */
                $seat['connected'] = false;
                $seat['active'] = false;

                /* Retrieve Assigned seats */
                $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat['id']])
                    ->where('user_id', $user['id'])
                    ->first();

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

            /* Prepare data for the view */
            $data = [
                'title' => 'Team Dashboard',
                'seats' => $seats,
                'team_members' => $teamMembers,
                'is_owner' => $is_owner,
                'permissions' => $permissions,
                'roles' => $roles,
                'is_manage_payment_system' => $is_manage_payment_system,
                'is_manage_global_blacklist' => $is_manage_global_blacklist,
                'team' => $team,
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

    /**
     * Create a add team member based on the request data.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing form data.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating success or failure.
     */
    function add_team_member(Request $request)
    {
        /* Validate request data */
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'invite_email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'seats' => 'required',
        ]);

        /* Return validation errors if validation fails */
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->input('invite_email'))->first();

        if (!$user) {
            /* Generate a random password */
            $randomPassword = Str::random(37);

            /* Create a new user with the provided details */
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('invite_email'),
                'password' => Hash::make($randomPassword),
                'team_id' => $request->input('team_id'),
            ]);

            /* Extract role ID */
            $roleId = str_replace('role_', '', $request->input('role'));

            /* Create a new assigned seat for the user */
            $seats = $request->input('seats');
            foreach ($seats as $seat) {
                $assignedSeat = AssignedSeats::create([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'seat_id' => $seat,
                ]);
            }

            if ($request->input('manage_payment_system')) {
                /* Create a new manage payment system for the user */
                $manage_payment_system = GlobalPermission::create([
                    'permission_name' => 'Manage payment system',
                    'permission_slug' => 'manage_payment_system',
                    'user_id' => $user->id,
                    'team_id' => $request->input('team_id'),
                    'access' => 1,
                ]);
            }

            if ($request->input('manage_global_blacklist')) {
                /* Create a new manage global blacklist for the user */
                $manage_global_blacklist = GlobalPermission::create([
                    'permission_name' => 'Manage global blacklist',
                    'permission_slug' => 'manage_global_blacklist',
                    'user_id' => $user->id,
                    'team_id' => $request->input('team_id'),
                    'access' => 1,
                ]);
            }

            /* Send a welcome email to the new user */
            Mail::to($user->email)->send(new WelcomeMail($user, $randomPassword));

            return response()->json(['success' => true, 'user' => $user]);
        }

        return response()->json(['success' => false, 'errors' => 'User already exists'], 422);
    }
}
