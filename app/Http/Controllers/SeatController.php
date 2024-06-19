<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class SeatController extends Controller
{
    public function get_seat_by_id($seat_id)
    {
        $user_id = Auth::user()->id;
        $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
        return response()->json(['success' => true, 'seat' => $seat]);
    }

    public function delete_seat($seat_id)
    {
        $user_id = Auth::user()->id;
        $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
        if ($seat['account_id'] !== NULL) {
            $request = [
                'account_id' => $seat['account_id'],
            ];
            $uc = new UnipileController();
            $account = $uc->delete_account(new \Illuminate\Http\Request($request));
            if ($account instanceof JsonResponse) {
                $seat->delete();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        } else {
            $seat->delete();
            return response()->json(['success' => true]);
        }
    }

    public function update_name($seat_id, $seat_name)
    {
        $user_id = Auth::user()->id;
        $seat = SeatInfo::where('user_id', $user_id)->where('id', $seat_id)->first();
        $seat->username = $seat_name;
        $seat->save();
        return response()->json(['success' => true]);
    }
}
