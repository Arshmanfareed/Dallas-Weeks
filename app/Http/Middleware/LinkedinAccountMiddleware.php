<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class LinkedinAccountMiddleware
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
        if (!session()->has('seat_id')) {
            return redirect()->route('dashobardz')->withErrors('Seat Not Found');
        }
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        if (is_null($seat)) {
            return redirect()->route('dashobardz')->withErrors('Seat Not Found');
        }
        if (is_null($seat->account_id) && (!session()->has('account_profile') || !session()->has('account'))) {
            session(['add_account' => true]);
            return redirect()->route('dash-settings');
        }
        return $next($request);
    }
}
