<?php

namespace App\Http\Controllers;

use App\Events\Message;
use Illuminate\Http\Request;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        // event(new Message($request->input('message')));
        // // event(new Message('hello world'));
        // return [];

        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    'useTLS' => true,
                ]
            );
    
            $pusher->trigger('test-channel', 'test-event', ['message' => 'Pusher is connected!']);
    
            return response()->json(['status' => 'Pusher is connected!']);
        } catch (\Exception $exception) {
            return response()->json(['status' => 'Pusher connection error', 'error' => $exception->getMessage()], 500);
        }
    }
}