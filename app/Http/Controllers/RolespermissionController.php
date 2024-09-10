<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Permissions;
use App\Models\Roles;
use Exception;
use App\Models\AssignedSeats;
use App\Models\Role_Permission;
use App\Models\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolespermissionController extends Controller
{
    /**
     * Display the Roles & Permissions page for the authenticated user.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function rolespermission()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Get the assigned seat for the user where seat_id is 0 (potentially indicating an unassigned seat or default seat) */
            $assignedSeat = AssignedSeats::where('seat_id', 0)
                ->where('user_id', $user->id)
                ->first();

            /* Check if the user is the owner of the seat (is assigned seat_id 0) */
            $is_owner = false;
            if (!empty($assignedSeat)) {
                $is_owner = true;
            }

            /* Fetch all available permissions */
            $permissions = Permissions::all();

            /* Fetch roles that either belong to the system (team_id = 0) or to the user's team */
            $roles = Roles::whereIn('team_id', [0, $user['team_id']])->get();

            /* Prepare data to be passed to the view */
            $data = [
                'title' => 'Roles & Permission',
                'permissions' => $permissions,
                'roles' => $roles,
                'is_owner' => $is_owner,
                'count_role' => Roles::where('team_id', $user['team_id'])->count(),
            ];

            /* Return the view with the prepared data */
            return view('roles&permission', $data);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::error($e->getMessage());

            /* Return a JSON response with the error message */
            return redirect()->route('dashobardz')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Create a new role and assign permissions based on the request data.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing form data.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating success or failure.
     */
    public function custom_role(Request $request)
    {
        try {
            /* Get all the input data from the request */
            $all = $request->all();

            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Find an assigned seat for the user where seat_id is 0 (indicating a default or unassigned seat) */
            $assignedSeat = AssignedSeats::where('seat_id', 0)
                ->where('user_id', $user->id)
                ->first();

            /* If an assigned seat is found for the user */
            if (!empty($assignedSeat)) {
                /* Find the team associated with the user */
                $team = Teams::find($user->team_id);

                /* Create a new role for the team */
                $role = Roles::create([
                    'role_name' => $all['role_name'],
                    'team_id' => $team->id,
                ]);

                /* Remove 'role_name' and '_token' from the request data */
                unset($all['role_name']);
                unset($all['_token']);

                /* Fetch all available permissions */
                $permissions = Permissions::all();

                /* Iterate over each permission to assign it to the new role */
                foreach ($permissions as $permission) {
                    if (array_key_exists($permission['permission_slug'], $all)) {
                        /* If the permission is present in the request, create a Role_Permission entry with access */
                        Role_Permission::create([
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                            'access' => 1,
                            'view_only' => array_key_exists('view_only_' . $permission['permission_slug'], $all) ? 1 : 0,
                        ]);
                    } else {
                        /* If the permission is not present in the request, create a Role_Permission entry without access */
                        Role_Permission::create([
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                            'access' => 0,
                            'view_only' => 0,
                        ]);
                    }

                    /* Remove processed permission entries from the request data */
                    unset($all[$permission['permission_slug']]);
                    unset($all['view_only_' . $permission['permission_slug']]);
                }

                /* Return the remaining data in JSON format */
                return response()->json(['success' => true]);
            }

            /* If the assigned seat is not found, throw a permission denied exception */
            throw new Exception('Permission Denied', 403);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::error($e->getMessage());

            /* Return a JSON response with the error message */
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a role by its ID.
     *
     * @param int $role_id The ID of the role to be deleted.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating success or failure.
     */
    public function delete_role($role_id)
    {
        try {
            /* Check if the role ID is not one of the default role IDs (1, 2, 3) */
            if ($role_id != 1 && $role_id != 2 && $role_id != 3) {
                /* Retrieve the currently authenticated user */
                $user = Auth::user();

                /* Find an assigned seat for the user where seat_id is 0 (indicating a default or unassigned seat) */
                $assignedSeat = AssignedSeats::where('seat_id', 0)
                    ->where('user_id', $user->id)
                    ->first();

                /* If an assigned seat is found for the user */
                if (!empty($assignedSeat)) {
                    /* Check if the role is assigned to any seat */
                    $assignedSeat = AssignedSeats::where('role_id', $role_id)->first();
                    if (empty($assignedSeat)) {
                        /* Delete role permissions associated with the role */
                        Role_Permission::where('role_id', $role_id)->delete();

                        /* Delete the role */
                        Roles::find($role_id)->delete();

                        /* Return a JSON response with the success */
                        return response()->json(['success' => true]);
                    }

                    /* If the role is in use, throw an exception */
                    throw new Exception('Role is already in use');
                }

                /* If the assigned seat is not found, throw a permission denied exception */
                throw new Exception('Permission Denied', 403);
            }

            /* If the role ID is one of the default roles (1, 2, 3), throw an exception */
            throw new Exception('Cannot delete default roles');
        } catch (Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::error($e->getMessage());

            /* Return a JSON response with the error message and failure status */
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
