<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    function message()
    {
        if (Auth::check()) {
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
                    if (isset($profile['last_message']['text'])) {
                        $input = $profile['last_message']['text'];
                        if (strlen($input) > 25) {
                            $trimmed_text = substr($input, 0, 25);
                            $trimmed_text .= '...';
                            $profile['last_message']['text'] = $trimmed_text;
                        }
                    }
                    if (isset($profile['last_message']['timestamp'])) {
                        $date = new \DateTime($profile['last_message']['timestamp']);
                        $profile['last_message']['timestamp'] = $date->format('d M');
                    }
                    $final_chats[] = $profile;
                }
            }
            $all_chats['items'] = $final_chats;
            $data = [
                'title' => 'Message',
                'chats' => $all_chats['items'],
                'cursor' => $all_chats['cursor'],
                'last_chat' => $last_chat,
            ];
            return view('message', $data);
        } else {
            return redirect(url('/'));
        }
    }

    public function get_remain_message($cursor)
    {
        if (Auth::check()) {
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
                    if (isset($profile['last_message']['text'])) {
                        $input = $profile['last_message']['text'];
                        if (strlen($input) > 25) {
                            $trimmed_text = substr($input, 0, 25);
                            $trimmed_text .= '...';
                            $profile['last_message']['text'] = $trimmed_text;
                        }
                    }
                    if (isset($profile['last_message']['timestamp'])) {
                        $date = new \DateTime($profile['last_message']['timestamp']);
                        $profile['last_message']['timestamp'] = $date->format('d M');
                    }
                    $final_chats[] = $profile;
                }
            }
            $all_chats['items'] = $final_chats;
            return response()->json(['success' => true, 'chats' => $all_chats['items'], 'cursor' => $all_chats['cursor']]);
        }
    }

    public function get_messages_chat_id($chat_id)
    {
        if (Auth::check()) {
            $uc = new UnipileController();
            $request = [
                'chat_id' => $chat_id,
            ];
            $messages = $uc->list_all_messages_from_chat(new \Illuminate\Http\Request($request));
            $messages = $messages->getData(true)['messages'];
            return response()->json(['success' => true, 'messages' => $messages]);
        } else {
            return redirect(url('/'));
        }
    }
}
