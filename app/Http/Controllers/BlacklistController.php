<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\GlobalPermission;
use App\Models\Teams;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlacklistController extends Controller
{
    /**
     * Display the blacklist for the authenticated user.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    function blacklist()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            $team = Teams::find($user->team_id);

            /* Fetch the blacklist items for the user, ordered by creation date descending */
            $blacklist = Blacklist::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $manage_global_blacklist = GlobalPermission::where('permission_slug', 'manage_global_blacklist')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();

            if (empty($manage_global_blacklist)) {
                return redirect()->route('dashobardz')->withErrors(['error' => "You don't have access to blacklist"]);
            }

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

            /* Prepare data for the view */
            $data = [
                'title' => 'Blacklist',
                'blacklist' => $blacklist,
                'is_manage_payment_system' => $is_manage_payment_system,
                'is_manage_global_blacklist' => $is_manage_global_blacklist,
            ];

            /* Return the view with the prepared data */
            return view('blacklist', $data);
        } catch (Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::info($e);

            /* Redirect to login with an error message */
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
