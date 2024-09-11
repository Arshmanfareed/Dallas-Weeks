<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\TeamMembers;
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
    function blacklist($team_id)
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the teams the user is a member of */
            $memberOf = TeamMembers::where('team_id', $team_id)->where('user_id', $user->id)->first();
            if (!$memberOf) {
                throw new Exception('Invalid Team');
            }
            
            //Need to work from here!!!!

            /* Fetch the blacklist items for the user, ordered by creation date descending */
            $blacklist = Blacklist::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            /* Prepare data for the view */
            $data = [
                'title' => 'Blacklist',
                'blacklist' => $blacklist,
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
