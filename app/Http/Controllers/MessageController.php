<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function message()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'limit' => 10,
        ];
        $chats = $uc->list_all_chats(new \Illuminate\Http\Request($request));
        $all_chats = $chats->getData(true)['chats'];
        $data = [
            'title' => 'Message',
            'chats' => $all_chats['items'],
            'cursor' => $all_chats['cursor'],
        ];
        return view('message', $data);
    }

    public function get_chat_Profile($attendee_provider_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'profile_url' => $attendee_provider_id,
        ];
        $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
        $profile = $profile->getData(true);
        if (!isset($profile['error'])) {
            return response()->json(['success' => true, 'user_profile' => $profile['user_profile']]);
        }
        return response()->json(['success' => false]);
    }

    public function get_latest_Mesage_chat_id($chat_id)
    {
        $uc = new UnipileController();
        $request = [
            'chat_id' => $chat_id,
            'limit' => 1
        ];
        $last_message = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $last_message = $last_message->getData(true);
        if (!isset($last_message['error']) && isset($last_message['messages']['items'])) {
            return response()->json(['success' => true, 'message' => $last_message['messages']['items']]);
        }
        return response()->json(['success' => false]);
    }

    public function get_remain_chats($cursor)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'cursor' => $cursor,
            'limit' => 10,
        ];
        $chats = $uc->list_all_chats(new \Illuminate\Http\Request($request));
        $all_chats = $chats->getData(true)['chats'];
        $data = [
            'success' => true,
            'chats' => $all_chats['items'],
            'cursor' => $all_chats['cursor']
        ];
        return response()->json($data);
    }

    public function get_messages_chat_id($chat_id)
    {
        $uc = new UnipileController();
        $request = [
            'chat_id' => $chat_id,
            'limit' => 25
        ];
        $messages = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $messages = $messages->getData(true)['messages'];
        $messages['items'] = array_reverse($messages['items']);
        $data = [
            'success' => true,
            'messages' => $messages
        ];
        return response()->json($data);
    }
}
