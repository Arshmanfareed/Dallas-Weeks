@extends('partials/dashboard_header')
@section('content')
    <section class="main_dashboard message_sec">
        <div class="container_fluid">
            <div class="row">
                <div class="col-lg-1">
                    @include('partials/dashboard_sidebar_menu')
                </div>
                <div class="col-lg-11 col-sm-12">
                    <div class="row">
                        <div class="col-lg-12 lead_sec justify-content-between d-flex">
                            <h3>Messages</h3>
                            {{-- <div class="filt_opt d-flex">
                                <div class="filt_opt">
                                    <select name="Channels" id="Channels">
                                        <option value="01">All Channels</option>
                                        <option value="02">All Channels</option>
                                        <option value="03">3All Channels</option>
                                        <option value="04">All Channels</option>
                                    </select>
                                </div>
                                <div class="add_btn">
                                    <a href="javascript:;" class="" type="button" data-bs-toggle="modal"
                                        data-bs-target="#export_modal"><img class="img-fluid"
                                            src="{{ asset('assets/img/sync.svg') }}" alt=""></a>Sync
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="messages_box">
                                <div class="border_box">
                                    <div class="filter d-flex">
                                        <form action="/search" method="get" class="search-form">
                                            <input type="text" name="q" id="search_message"
                                                placeholder="Search Accounts here...">
                                            <button type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </form>
                                        <a href="javascript:;" class="message_filter"><i class="fa-solid fa-filter"></i></a>
                                        <div class="msg_filter_cont">
                                            <h6>Filter Leads by Label</h6>
                                            <span>Lorem ipsum dolor, sit amet consectetur adipisicing elit.</span>
                                            <hr>
                                            <h6>Filter by chat type</h6>
                                            <form action="" class="filter_form">
                                                <div class="" data-toggle="">
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="LinkedIn" name="LinkedIn"
                                                            value="LinkedIn">
                                                        <label for="LinkedIn">LinkedIn Messages
                                                        </label>
                                                    </div>
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="Messages" name="Messages"
                                                            value="Messages">
                                                        <label for="Messages">Email Messages
                                                        </label>
                                                    </div>
                                                    <div class="fltr_btn_form">
                                                        <button type="button" class="list-group-item">Apply filter<i
                                                                class="fa-solid fa-arrow-right"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled p-0 m-0 chat-list">
                                        @if (isset($chats))
                                            @foreach ($chats as $chat)
                                                @php
                                                    $disable_chat = 'false';
                                                    if (
                                                        $chat['read_only'] ||
                                                        in_array('reply', $chat['disabledFeatures'])
                                                    ) {
                                                        $disable_chat = 'true';
                                                    }
                                                @endphp
                                                @if (in_array('INBOX_LINKEDIN_CLASSIC', $chat['folder']) && $chat['archived'] == 0)
                                                    <li class="d-flex chat-tab skel-chat" id="{{ $chat['id'] }}"
                                                        data-profile="{{ $chat['attendee_provider_id'] }}"
                                                        data-disable="{{ $disable_chat }}">
                                                        @if ($chat['unread'] == 1)
                                                            <span class="unread_count">{{ $chat['unread_count'] }}</span>
                                                        @endif
                                                        <span class="chat_image skel_chat_img"></span>
                                                        <div class="d-block">
                                                            <strong class="chat_name skel_chat_name"></strong>
                                                            <span class="latest_message skel_latest_message"></span>
                                                        </div>
                                                        <div
                                                            class="date latest_message_timestamp skel_latest_message_timestamp">
                                                        </div>
                                                        <div class="linkedin">
                                                            <a href="javascript:;"><i class="fa-brands fa-linkedin"></i></a>
                                                        </div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                    <input type="hidden" name="chat_cursor" id="chat_cursor" value="{{ $cursor }}">
                                    <div id="chat-loader" style="display: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"
                                            preserveAspectRatio="xMidYMid" width="200" height="200"
                                            style="shape-rendering: auto; display: block; width: 100%; height: 55px;"
                                            xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <g>
                                                <g transform="rotate(0 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.9166666666666666s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(30 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.8333333333333334s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(60 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.75s" dur="1s"
                                                            keyTimes="0;1" values="1;0" attributeName="opacity">
                                                        </animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(90 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.6666666666666666s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(120 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.5833333333333334s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(150 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.5s" dur="1s"
                                                            keyTimes="0;1" values="1;0" attributeName="opacity">
                                                        </animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(180 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.4166666666666667s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(210 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.3333333333333333s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(240 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.25s" dur="1s"
                                                            keyTimes="0;1" values="1;0" attributeName="opacity">
                                                        </animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(270 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.16666666666666666s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(300 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="-0.08333333333333333s"
                                                            dur="1s" keyTimes="0;1" values="1;0"
                                                            attributeName="opacity"></animate>
                                                    </rect>
                                                </g>
                                                <g transform="rotate(330 50 50)">
                                                    <rect fill="#16adcb" height="12" width="6" ry="6"
                                                        rx="3" y="24" x="47">
                                                        <animate repeatCount="indefinite" begin="0s" dur="1s"
                                                            keyTimes="0;1" values="1;0" attributeName="opacity">
                                                        </animate>
                                                    </rect>
                                                </g>
                                                <g></g>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="conversation_box row border_box">
                                <div class="conversation col-lg-12">
                                    <ul class=" msg_setting d-flex justify-content-end p-0 list-unstyled">
                                        <li class="user_profile"><a href="javascript:;"><i
                                                    class="fa-solid fa-user"></i></a></li>
                                        <li><a href="javascript:;"><i class="fa-solid fa-tag"></i></a></li>
                                        <li><a href="javascript:;"><img src="{{ asset('assets/img/settings.svg') }}"
                                                    alt=""></a>
                                        </li>
                                    </ul>
                                    <input type="hidden" name="message_cursor" id="message_cursor">
                                    <div class="mesasges" id="chat-message" data-chat="">
                                        <div id="message-loader" style="display: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"
                                                preserveAspectRatio="xMidYMid" width="200" height="200"
                                                style="shape-rendering: auto; display: block; width: 100%; height: 55px;"
                                                xmlns:xlink="http://www.w3.org/1999/xlink">
                                                <g>
                                                    <g transform="rotate(0 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.9166666666666666s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity"></animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(30 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.8333333333333334s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity"></animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(60 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.75s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity">
                                                            </animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(90 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.6666666666666666s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity"></animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(120 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.5833333333333334s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity"></animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(150 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.5s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity">
                                                            </animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(180 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.4166666666666667s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity"></animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(210 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.3333333333333333s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity"></animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(240 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="-0.25s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity">
                                                            </animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(270 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite"
                                                                begin="-0.16666666666666666s" dur="1s"
                                                                keyTimes="0;1" values="1;0" attributeName="opacity">
                                                            </animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(300 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite"
                                                                begin="-0.08333333333333333s" dur="1s"
                                                                keyTimes="0;1" values="1;0" attributeName="opacity">
                                                            </animate>
                                                        </rect>
                                                    </g>
                                                    <g transform="rotate(330 50 50)">
                                                        <rect fill="#16adcb" height="12" width="6"
                                                            ry="6" rx="3" y="24" x="47">
                                                            <animate repeatCount="indefinite" begin="0s"
                                                                dur="1s" keyTimes="0;1" values="1;0"
                                                                attributeName="opacity">
                                                            </animate>
                                                        </rect>
                                                    </g>
                                                    <g></g>
                                                </g>
                                            </svg>
                                        </div>
                                        <ul>
                                            <li class="not_me">
                                                <span class="skel_img"></span>
                                                <span class="message_text skel_text"></span>
                                            </li>
                                            <li class="is_me">
                                                <span class="skel_img"></span>
                                                <span class="message_text skel_text"></span>
                                            </li>
                                            <li class="not_me"><span class="skel_img"></span>
                                                <span class="message_text skel_text"></span>
                                            </li>
                                            <li class="is_me"><span class="skel_img"></span>
                                                <span class="message_text skel_text"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="unread_label" bis_skin_checked="1">
                                        <i class="fa-solid fa-arrow-down"></i>
                                        Unread
                                    </div>
                                    <form class="send_form">
                                    </form>
                                </div>
                                <div class="conversation_info" style="display: none">
                                    <div class="info">
                                        <img class="skel_img" src="" alt="">
                                        <h6 class="skel_head"></h6>
                                        <span class="user_name skel_user_name"></span>
                                        <span class="user_email skel_user_email"></span>
                                        <div class="note skel_text"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="imageContainer"></div>
    </section>
    <script>
        var getMessageChatRoute = "{{ route('get_messages_chat_id', ':chat_id') }}";
        var getMessageChatCursorRoute =
            "{{ route('get_messages_chat_id_cursor', ['chat_id' => ':chat_id', 'cursor' => ':cursor']) }}";
        var getRemainMessage = "{{ route('get_remain_chats', ':cursor') }}";
        var getProfileAndLatestMessageRoute =
            "{{ route('get_profile_and_latest_message', ['profile_id' => ':profile_id', 'chat_id' => ':chat_id']) }}";
        var getLatestMessageRoute = "{{ route('get_latest_Mesage_chat_id', ':chat_id') }}";
        var getChatProfile = "{{ route('get_chat_Profile', ':profile_id') }}";
        var getChatSender = "{{ route('get_chat_sender') }}";
        var getChatReceiver = "{{ route('get_chat_receive', ':chat_id') }}";
        var sendMessageRoute = "{{ route('send_a_message') }}";
        var getLatestMessageInChatRoute =
            "{{ route('get_latest_message_in_chat', ['chat_id' => ':chat_id', 'count' => ':count']) }}";
        var messageSearch = "{{ route('message_search') }}";
        var unreadMessage = "{{ route('unread_message') }}";
        var getProfileByIdRoute = "{{ route('profile_by_id', ':profile_id') }}";
        var getAnAttachmentFromMessage = "{{ route('retrieve_an_attachment_from_a_message') }}";
    </script>
    <script src="{{ asset('assets/js/message.js') }}"></script>
@endsection
