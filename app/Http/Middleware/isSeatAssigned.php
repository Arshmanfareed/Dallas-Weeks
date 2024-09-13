<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Teams;
use App\Models\SeatInfo;
use App\Models\AssignedSeats;
use Exception;

class isSeatAssigned
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
            $seat_id = $request->input('seat_id', session('seat_id'));
            if ($seat_id) {
                /* Store seat_id in session if found */
                session(['seat_id' => $seat_id]);
            } else {
                /* Throw an exception if seat_id is not found */
                throw new Exception('Seat Not Found');
            }

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::findOrFail($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->firstOrFail();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();

            /* Check if the user has an assigned seat. */
            if (empty($assignedSeat)) {
                throw new Exception("You don't have access to seat");
            }
            return $next($request);
        } catch (Exception $e) {
            /* Log the exception for debugging purposes */
            Log::info($e);

            /* Return a JSON response with the error message and a 404 status code */
            return redirect()->route('dashobardz')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
