<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Permissions;
use App\Models\Roles;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolespermissionController extends Controller
{
    public function rolespermission()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Fetch all available permissions */
            $permissions = Permissions::all();

            /* Fetch roles that either belong to the system (team_id = 0) or to the user's team */
            $roles = Roles::where('team_id', [0, $user['team_id']])->get();

            /* Prepare data to be passed to the view */
            $data = [
                'title' => 'Roles & Permission',
                'permissions' => $permissions,
                'roles' => $roles,
            ];

            /* Return the view with the prepared data */
            return view('roles&permission', $data);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::error($e->getMessage());

            /* Redirect to login with error message */
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
