@extends('partials/dashboard_header')
@section('content')
<script src="{{ asset('assets/js/message.js') }}"></script>
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
                        <div class="filt_opt d-flex">
                            <div class="filt_opt">
                                <select name="Channels" id="Channels">
                                    <option value="01">All Channels</option>
                                    <option value="02">All Channels</option>
                                    <option value="03">3All Channels</option>
                                    <option value="04">All Channels</option>
                                </select>
                            </div>
                            <div class="add_btn">
                                <a href="javascript:;" class="" type="button" data-bs-toggle="modal" data-bs-target="#export_modal"><img class="img-fluid" src="{{ asset('assets/img/sync.svg') }}" alt=""></a>Sync
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="messages_box">
                            <div class="border_box">
                                <div class="filter d-flex">
                                    <form action="/search" method="get" class="search-form">
                                        <input type="text" name="q" placeholder="Search Accounts here...">
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
                                                    <input type="checkbox" id="LinkedIn" name="LinkedIn" value="LinkedIn">
                                                    <label for="LinkedIn">LinkedIn Messages
                                                    </label>
                                                </div>
                                                <div class="checkbox">
                                                    <input type="checkbox" id="Messages" name="Messages" value="Messages">
                                                    <label for="Messages">Email Messages
                                                    </label>
                                                </div>
                                                <div class="fltr_btn_form">
                                                    <button type="button" class="list-group-item">Apply filter<i class="fa-solid fa-arrow-right"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <ul class="list-unstyled p-0 m-0 chat-list">
                                    @if ($chats)
                                    @foreach ($chats as $chat)
                                    <li class="d-flex chat-tab" id="{{ $chat['last_message']['chat_id'] }}">
                                        @if (isset($chat['profile_picture_url']))
                                        <img src="{{ $chat['profile_picture_url'] }}" alt="">
                                        @else
                                        <img src="" alt="">
                                        @endif
                                        <div class="d-block">
                                            <strong>{{ $chat['first_name'] . ' ' . $chat['last_name'] }}</strong>
                                            <span>{{ $chat['last_message']['text'] }}</span>
                                        </div>
                                        <div class="date">{{ $chat['last_message']['timestamp'] }}</div>
                                        <div class="linkedin">
                                            <a href="javascript:;"><i class="fa-brands fa-linkedin"></i></a>
                                        </div>
                                    </li>
                                    @endforeach
                                    <input type="hidden" name="chat_cursor" id="chat_cursor" value="{{ $cursor }}">
                                    @endif
                                </ul>
                                <div id="chat-loader" style="display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="200" height="200" style="shape-rendering: auto; display: block; width: 100%; height: 55px;" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <g>
                                            <g transform="rotate(0 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.9166666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(30 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.8333333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(60 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.75s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(90 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.6666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(120 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.5833333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(150 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.5s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(180 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.4166666666666667s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(210 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.3333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(240 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.25s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(270 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.16666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(300 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="-0.08333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                </rect>
                                            </g>
                                            <g transform="rotate(330 50 50)">
                                                <rect fill="#16adcb" height="12" width="6" ry="6" rx="3" y="24" x="47">
                                                    <animate repeatCount="indefinite" begin="0s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
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
                            <div class="conversation col-lg-8">
                                <ul class=" msg_setting d-flex justify-content-end p-0 list-unstyled">
                                    <li><a href="javascript:;"><i class="fa-solid fa-user"></i></a></li>
                                    <li><a href="javascript:;"><i class="fa-solid fa-tag"></i></a></li>
                                    <li><a href="javascript:;"><img src="{{ asset('assets/img/settings.svg') }}" alt=""></a>
                                    </li>
                                </ul>
                                <div class="mesasges" id="chat-message">
                                    <ul class="list-unstyled">
                                        @if ($last_chat['items'])
                                        @foreach ($last_chat['items'] as $message)
                                        @php
                                        $text = nl2br($message['text']);
                                        @endphp
                                        <li class="{{ $message['is_sender'] == 0 ? 'not_me' : 'is_me' }}">
                                            <img src="" alt="">
                                            <span>{!! $text !!}</span>
                                        </li>
                                        @endforeach
                                        <input type="hidden" name="message_cursor" id="message_cursor" value="{{ $last_chat['cursor'] }}">
                                        @endif
                                    </ul>
                                </div>
                                <form action="" class="send_form">
                                    <input type="text" placeholder="Send a message">
                                    <input type="button" class="send_btn" value="send">
                                </form>
                            </div>
                            <div class="conversation_info col-lg-4">
                                <div class="info">
                                    <img src="{{ asset('assets/img/account_img.png') }}" alt="">
                                    <h6>John doe</h6>
                                    <span class="user_name">Lorem Ipsum</span>
                                    <span class="user_email">johndoe@gmail.com</span>
                                    <div class="note">
                                        <p>Note:</p>
                                        <span>Sed ut perspiciatis unde omnis iste natus error sit.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<script>
    var getMessageChatRoute = "{{ route('get_messages_chat_id', ':chat_id') }}";
    var getRemainMessage = "{{ route('get_remain_message', ':cursor') }}";
</script>
@endsection