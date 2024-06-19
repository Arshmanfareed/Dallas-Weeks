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
            return redirect(route('dashobardz'));
        }
        $seat_id = session('seat_id');
        $seat = SeatInfo::find($seat_id);
        if (is_null($seat) || is_null($seat->account_id)) {
            session(['add_account' => true]);
            return redirect(route('dash-settings'));
        }
        $request = new \Illuminate\Http\Request(['account_id' => $seat->account_id]);
        $uc = new \App\Http\Controllers\UnipileController();
        $accountResponse = $uc->retrieve_an_account($request);
        if ($accountResponse instanceof JsonResponse) {
            $account = $accountResponse->getData(true);
            $seat->connected = !isset($account['error']);
        } else {
            $seat->connected = false;
        }
        if (!$seat->connected) {
            session(['add_account' => true]);
            return redirect(route('dash-settings'));
        }
        return $next($request);
    }
}
