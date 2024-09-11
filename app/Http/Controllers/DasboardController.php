<?php

namespace App\Http\Controllers;

use App\Models\AssignedSeats;
use App\Models\CompanyInfo;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use App\Models\Seats;
use App\Models\TeamMembers;
use App\Models\Teams;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DasboardController extends Controller
{
    /**
     * Redirects to the user's dashboard based on their team.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function to_dashobard()
    {
        try {
            /* Get the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the teams the user is a member of */
            $memberOf = TeamMembers::where('user_id', $user->id)->first();

            /* Retrieve the team based on membership */
            $team = Teams::where('id', $memberOf->team_id)->first();

            /* Redirect to the dashboard route */
            return redirect()->route('dashobard', ['team_id' => $team->id]);
        } catch (\Exception $e) {
            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect to login with error message */
            return redirect('login')->withErrors(['error' => 'Retry Login again']);
        }
    }

    /**
     * Display the user's dashboard.
     *
     * @param int $team_id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard($team_id)
    {
        try {
            /* Clear specific session data */
            session()->forget(['seat_id', 'account', 'account_profile']);

            /* Get the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the teams the user is a member of */
            $memberOf = TeamMembers::where('team_id', $team_id)->where('user_id', $user->id)->first();
            if (!$memberOf) {
                throw new Exception('Invalid Team');
            }

            $team = Teams::find($team_id);

            /* Retrieve seats for the first team */
            $seats = Seats::where('team_id', $team->id)->get();

            /* Attach related data to seats */
            foreach ($seats as $seat) {
                $seat->creator = User::find($seat->creator_id);
                $seat->team = Teams::find($seat->team_id);
                $seat->company_info = CompanyInfo::find($seat->company_info_id);
                $seat->payment = Payment::find($seat->payment_id);
                $seat->seat_info = SeatInfo::find($seat->seat_info_id);
            }

            /* Initialize UnipileController */
            $uc = new UnipileController();

            /* Determine if the user is an executive */
            $is_executive = ($team->creator_id === $user->id);

            /* Process seats based on user role */
            $seats = $seats->map(function ($seat) use ($user, $uc, $is_executive) {
                $seat['connected'] = false;
                $seat['active'] = false;
                $assignedSeat = AssignedSeats::where('seat_id', $seat['id'])->where('member_id', $user['id'])->first();
                if (!empty($assignedSeat) || $is_executive) {
                    if (!empty($seat['seat_info']['account_id'])) {
                        $request = ['account_id' => $seat['seat_info']['account_id']];
                        $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
                        if ($account instanceof JsonResponse && !isset($account->getData(true)['error'])) {
                            $seat['active'] = true;
                            $seat['account'] = $account->getData(true)['account'];
                        }
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
                'is_executive' => $is_executive,
            ];

            /* Return the view with the prepared data */
            return view('dashboard-account', $data);
        } catch (Exception $e) {
            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect to login with error message */
            return redirect('dashobardz')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
