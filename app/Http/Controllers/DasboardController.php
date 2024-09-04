<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;
use App\Models\TeamMember;
use App\Models\Teams;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DasboardController extends Controller
{
    function dashboard()
    {
        try {
            /* Clear specific session data related to the user's seat and account */
            session()->forget(['seat_id', 'account', 'account_profile']);

            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Fetch the team member record for the current user */
            $team_member = TeamMember::where('user_id', $user['id'])->first();

            /* Handle case where the user is not found in the team member table */
            if (!$team_member) {
                return redirect('login')->withErrors(['error' => 'Something went wrong']);
            }

            /* Retrieve the team associated with the team member */
            $team = Teams::find($team_member['team_id']);

            /* Get all seats associated with the user */
            $seats = SeatInfo::where('user_id', $user['id'])->get();
            // $uc = new UnipileController();

            // /* Initialize seat data processing */
            // foreach ($seats as $seat) {
            //     /* Default values for seat connection and activity status */
            //     $seat['connected'] = false;
            //     $seat['active'] = false;

            //     /* If seat has an associated account ID, retrieve related account information */
            //     if (!empty($seat['account_id'])) {
            //         $request = ['account_id' => $seat['account_id']];

            //         /* Retrieve account details */
            //         $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
            //         if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
            //             $seat['active'] = true;
            //             $seat['account'] = $account->getData(true)['account'];
            //         }

            //         /* Retrieve profile details for the account */
            //         $account = $uc->retrieve_own_profile(new \Illuminate\Http\Request($request));
            //         if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
            //             $seat['connected'] = true;
            //             $seat['account_profile'] = $account->getData(true)['account'];
            //         }
            //     }
            // }

            /* Prepare data for the view */
            $data = [
                'title' => 'Account Dashboard',
                'team' => $team,
                'seats' => $seats
            ];

            /* Return the view with the prepared data */
            return view('dashboard-account', $data);
        } catch (Exception $e) {
            /* Log the exception message for debugging */
            Log::error($e->getMessage());

            /* Redirect to login with error message */
            return redirect('login')->withErrors(['error' => 'Retry Login again']);
        }
    }
}
