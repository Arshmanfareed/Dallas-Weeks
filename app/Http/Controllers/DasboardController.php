<?php

namespace App\Http\Controllers;

use App\Models\AssignedSeats;
use App\Models\GlobalPermission;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use App\Models\Teams;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DasboardController extends Controller
{
    /**
     * Display the user's dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        try {
            /* Clear specific session data related to the user's seat and account */
            session()->forget(['seat_id', 'account', 'account_profile']);

            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the team associated with the team member */
            $team = Teams::find($user->team_id);

            /* Get all seats associated with the user's team */
            $seats = SeatInfo::where('team_id', $team->id)->get();
            $uc = new UnipileController();

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
                'title' => 'Account Dashboard',
                'team' => $team,
                'seats' => $seats,
                'is_owner' => $is_owner,
                'is_manage_payment_system' => $is_manage_payment_system,
                'is_manage_global_blacklist' => $is_manage_global_blacklist,
            ];

            /* Return the view with the prepared data */
            return view('dashboard-account', $data);
        } catch (Exception $e) {
            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect to login with error message */
            return redirect('login')->withErrors(['error' => 'Retry Login again']);
        }
    }
}
