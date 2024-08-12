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
            'limit' => 15,
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
            'limit' => 15,
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
            'limit' => 15
        ];
        $messages = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $messages = $messages->getData(true)['messages'];
        $messages['items'] = array_reverse($messages['items']);
        $request = [
            'chat_id' => $chat_id
        ];
        $uc->change_status_chat(new \Illuminate\Http\Request($request));
        $data = [
            'success' => true,
            'messages' => $messages['items'],
            'cursor' => $messages['cursor']
        ];
        return response()->json($data);
    }

    public function get_messages_chat_id_cursor($chat_id, $cursor)
    {
        $uc = new UnipileController();
        $request = [
            'chat_id' => $chat_id,
            'limit' => 15,
            'cursor' => $cursor
        ];
        $messages = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $messages = $messages->getData(true)['messages'];
        $messages['items'] = array_reverse($messages['items']);
        $request = [
            'chat_id' => $chat_id
        ];
        $uc->change_status_chat(new \Illuminate\Http\Request($request));
        $data = [
            'success' => true,
            'messages' => $messages['items'],
            'cursor' => $messages['cursor']
        ];
        return response()->json($data);
    }

    public function get_chat_sender()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
        ];
        $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($request));
        if ($account instanceof JsonResponse) {
            $account = $account->getData(true);
            if (!isset($account['error'])) {
                $request = [
                    'account_id' => $seat['account_id'],
                    'profile_url' => $account['account']['connection_params']['im']['id'],
                ];
                $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                $profile = $profile->getData(true);
                if (!isset($profile['error'])) {
                    $profile = $profile['user_profile'];
                    $data = [
                        'success' => true,
                        'sender' => $profile
                    ];
                    return response()->json($data);
                }
            }
        }
        return response()->json(['success' => false]);
    }

    public function get_chat_receive($chat_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'chat_id' => $chat_id,
        ];
        $attendee = $uc->list_all_attendees_from_chat(new \Illuminate\Http\Request($request));
        $attendee = $attendee->getData(true);
        if (!isset($attendee['error'])) {
            $request = [
                'account_id' => $seat['account_id'],
                'profile_url' => $attendee['attendees']['items'][0]['provider_id'],
            ];
            $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
            $profile = $profile->getData(true);
            if (!isset($profile['error'])) {
                $profile = $profile['user_profile'];
                $data = [
                    'success' => true,
                    'receiver' => $profile
                ];
                return response()->json($data);
            }
        }
        return response()->json(['success' => false]);
    }

    public function get_latest_chat()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'limit' => 15
        ];
        $chats = $uc->list_all_chats(new \Illuminate\Http\Request($request));
        $all_chats = $chats->getData(true)['chats'];
        $final_chats = [];
        foreach ($all_chats['items'] as $chat) {
            if ($chat['unread_count'] > 0) {
                $final_chats[] = $chat;
            }
        }
        if (count($final_chats) > 0) {
            $data = [
                'success' => true,
                'chats' => $final_chats,
            ];
            return response()->json($data);
        }
        return response()->json(['success' => false]);
    }

    public function send_a_message(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required'
        ]);
        $message = $request['message'];
        return response()->json($message);
    }
}
