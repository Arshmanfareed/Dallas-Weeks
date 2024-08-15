<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use DateTime;

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

    public function get_profile_and_latest_message($attendee_provider_id, $chat_id)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $data = [];
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'profile_url' => $attendee_provider_id,
        ];
        $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
        $profile = $profile->getData(true);
        if (isset($profile['error'])) {
            return response()->json(['success' => false]);
        }
        $data['success'] = true;
        $data['user_profile'] = $profile['user_profile'];
        $request = [
            'chat_id' => $chat_id,
            'limit' => 1
        ];
        $last_message = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
        $last_message = $last_message->getData(true);
        if (!isset($last_message['error']) && isset($last_message['messages']['items'])) {
            $data['message'] = $last_message['messages']['items'];
        }
        return response()->json($data);
    }

    public function get_remain_chats($cursor)
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'limit' => 15,
        ];
        if ($cursor != 'emp') {
            $request['cursor'] = $cursor;
        }
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

    public function get_latest_message_in_chat($chat_id, $count)
    {
        $uc = new UnipileController();
        $request = [
            'chat_id' => $chat_id,
        ];
        $chat = $uc->retrieve_a_chat(new \Illuminate\Http\Request($request));
        $chat = $chat->getData(true);
        if (!isset($chat['error'])) {
            $chat = $chat['chat'];
            if ($chat['unread'] != 0 && $chat['unread_count'] > 0) {
                $request = [
                    'chat_id' => $chat_id,
                    'limit' => $count + $chat['unread_count']
                ];
                $messages = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
                $messages = $messages->getData(true);
                if (!isset($messages['error'])) {
                    $messages = $messages['messages'];
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
            }
        }
        return response()->json(['success' => false]);
    }

    public function message_search(Request $request)
    {
        $all = $request->all();
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $keywords = $all['keywords'];
        $uc = new UnipileController();
        $request = [
            'account_id' => $seat['account_id'],
            'keywords' => $keywords
        ];
        $messages = $uc->messages_search(new \Illuminate\Http\Request($request));
        $chats = $messages->getData(true)['searches'];
        if (count($chats) > 0) {
            $all_chats = [];
            foreach ($chats as $chat) {
                $provider_id = str_replace('urn:li:fsd_profile:', '', $chat['targetEntityViewModel']['entity']['profile']['entityUrn']);
                $request = [
                    'account_id' => $seat['account_id'],
                    'attendee_id' => $provider_id
                ];
                $messages = $uc->list_1_to_1_chats_from_attendee(new \Illuminate\Http\Request($request));
                $messages = $messages->getData(true);
                if (!isset($messages['error'])) {
                    $all_chats[] = $messages['chats']['items'][0];
                } else {
                    $all_chats[]['provider'] = $provider_id;
                }
            }
            $data = [
                'success' => true,
                'chats' => $all_chats,
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
        $chat_id = $request['chat_id'];
        $uc = new UnipileController();
        if ($chat_id != 'null') {
            $request = [
                'message' => $message,
                'chat_id' => $chat_id
            ];
            $message = $uc->send_a_message_in_a_chat(new \Illuminate\Http\Request($request));
            $message = $message->getData(true);
            if (!isset($message['error'])) {
                $message = $message['message'];
                $request = [
                    'message_id' => $message['message_id']
                ];
                $message = $uc->retrieve_a_message(new \Illuminate\Http\Request($request));
                $message = $message->getData(true);
                if (!isset($message['error'])) {
                    $data = [
                        'success' => true,
                        'message' => $message['message'],
                    ];
                    return response()->json($data);
                }
            }
        } else {
            $user_id = Auth::user()->id;
            $seat_id = session('seat_id');
            $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
            $account_id = $seat['account_id'];
            $attendee_id = $request['attendee_id'];
            $request = [
                'account_id' => $account_id,
                'attendee_id' => $attendee_id,
                'message' => $message
            ];
            $chat = $uc->start_a_new_chat(new \Illuminate\Http\Request($request));
            $chat = $chat->getData(true);
            if (!isset($chat['error'])) {
                $data = [
                    'success' => true,
                    'chat_id' => $chat['chat']['chat_id'],
                ];
                return response()->json($data);
            }
        }
        return response()->json(['success' => false]);
    }

    public function unread_message()
    {
        $user_id = Auth::user()->id;
        $seat_id = session('seat_id');
        $seat = SeatInfo::where('id', $seat_id)->where('user_id', $user_id)->first();
        $uc = new UnipileController();
        $date = new DateTime();
        $date->modify('-30 seconds');
        return response()->json(['time' => $date->format('Y-m-d H:i:s')]);
        $request = [
            'account_id' => $seat['account_id'],
            'unread' => true,
            'after' => $date->format('Y-m-d H:i:s')
        ];
        $chats = $uc->list_all_chats(new \Illuminate\Http\Request($request));
        $all_chats = $chats->getData(true)['chats'];
        $data = [
            'title' => 'Message',
            'chats' => $all_chats['items'],
            'cursor' => $all_chats['cursor'],
        ];
        return view('message', $data);
        if (!isset($all_chats['error'])) {
            $all_chats = $all_chats['chats'];
            $data = [
                'success' => true,
                'chats' => $all_chats['items'],
                'cursor' => $all_chats['cursor'],
            ];
            return response()->json($data);
        }
        return response()->json(['success' => false]);
    }
}
