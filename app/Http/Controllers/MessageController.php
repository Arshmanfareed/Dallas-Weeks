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
        $final_chats = [];
        foreach ($all_chats['items'] as $chat) {
            $request = [
                'account_id' => $seat['account_id'],
                'profile_url' => $chat['attendee_provider_id'],
            ];
            $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
            $profile = $profile->getData(true);
            if (!isset($profile['error'])) {
                $profile = $profile['user_profile'];
                $request = [
                    'chat_id' => $chat['id'],
                    'limit' => 1
                ];
                $last_message = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
                $last_message = $last_message->getData(true)['messages']['items'][0];
                $profile['last_message'] = $last_message;
                $final_chats[] = $profile;
            }
        }
        $request = [
            'chat_id' => $all_chats['items'][0]['id'],
            'limit' => 25
        ];
        $last_chat = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $last_chat = $last_chat->getData(true)['messages'];
        $last_chat['items'] = array_reverse($last_chat['items']);
        $receiver = null;
        $sender = null;
        if ($sender == null) {
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
                        $sender = $profile;
                    }
                }
            }
        }
        if ($receiver == null) {
            $request = [
                'chat_id' => $final_chats[0]['last_message']['chat_id'],
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
                    $receiver = $profile;
                }
            }
        }
        $all_chats['items'] = $final_chats;
        $data = [
            'title' => 'Message',
            'chats' => $all_chats['items'],
            'cursor' => $all_chats['cursor'],
            'last_chat' => $last_chat,
            'sender' => $sender,
            'receiver' => $receiver
        ];
        return view('message', $data);
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
        $request = [
            'chat_id' => $all_chats['items'][0]['id'],
        ];
        $last_chat = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $last_chat = $last_chat->getData(true)['messages'];
        $final_chats = [];
        foreach ($all_chats['items'] as $chat) {
            $request = [
                'account_id' => $seat['account_id'],
                'profile_url' => $chat['attendee_provider_id'],
            ];
            $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
            $profile = $profile->getData(true);
            if (!isset($profile['error'])) {
                $profile = $profile['user_profile'];
                $request = [
                    'chat_id' => $chat['id'],
                ];
                $last_message = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
                $last_message = $last_message->getData(true)['messages']['items'][0];
                $profile['last_message'] = $last_message;
                $final_chats[] = $profile;
            }
        }
        $all_chats['items'] = $final_chats;
        return response()->json(['success' => true, 'chats' => $all_chats['items'], 'cursor' => $all_chats['cursor']]);
    }

    public function get_messages_chat_id($chat_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'chat_id' => $chat_id,
            'limit' => 25
        ];
        $messages = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $messages = $messages->getData(true)['messages'];
        $messages['items'] = array_reverse($messages['items']);
        $receiver = null;
        $sender = null;
        if ($sender == null) {
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
                        $sender = $profile;
                    }
                }
            }
        }
        if ($receiver == null) {
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
                    $receiver = $profile;
                }
            }
        }
        return response()->json(['success' => true, 'messages' => $messages, 'sender' => $sender, 'receiver' => $receiver]);
    }
}
