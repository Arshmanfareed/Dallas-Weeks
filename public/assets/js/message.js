$(document).ready(function () {
    getChatData();
    let isLoading = false;
    $('.chat-list').on('scroll', updateChatList);
    $('.chat-tab').on('click', getMessages);

    function getChatData() {
        var chats = $('.skel-chat');
        chats.each(function () {
            var chat = $(this);
            getProfileMessage(chat);
            getLatestMessage(chat);
            chat.removeClass('skel-chat');
        });
    }

    function getLatestMessage(chat) {
        $.ajax({
            url: getLatestMessageRoute.replace(":chat_id", chat.prop('id')),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    if (response.message && response.message[0]['text']) {
                        let input = response.message[0]['text'];
                        if (input.length > 25) {
                            let trimmed_text = input.substring(0, 25) + '...';
                            response.message[0]['text'] = trimmed_text;
                        }
                    }
                    if (response.message && response.message[0]['timestamp']) {
                        let date = new Date(response.message[0]['timestamp']);
                        let options = { day: '2-digit', month: 'short' };
                        response.message[0]['timestamp'] = date.toLocaleDateString('en-GB', options);
                    }
                    chat.find('.latest_message').html(response.message[0]['text']);
                    chat.find('.latest_message').removeClass('skel_latest_message');
                    chat.find('.latest_message_timestamp').html(response.message[0]['timestamp']);
                    chat.find('.latest_message_timestamp').removeClass('skel_latest_message_timestamp');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }

    function getProfileMessage(chat) {
        $.ajax({
            url: getChatProfile.replace(":profile_id", chat.data()['profile']),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    var profile = response.user_profile;
                    chat.find('.chat_name').html(profile['first_name'] + ' ' + profile['last_name']);
                    chat.find('.chat_name').removeClass('skel_chat_name');
                    var imgTag = $('<img>').attr('src', profile['profile_picture_url']);
                    chat.find('.chat_image').replaceWith(imgTag);
                } else {
                    chat.remove();
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }

    function updateChatList(e) {
        e.preventDefault();
        if (isLoading) return;
        var contentHeight = $(this).prop('scrollHeight');
        var visibleHeight = $(this).height();
        var scrollTop = $(this).scrollTop();
        if (scrollTop + visibleHeight >= contentHeight - 1) {
            $('#chat-loader').show();
            var cursor = $('#chat_cursor').val();
            var html = ``;
            isLoading = true;
            $.ajax({
                url: getRemainMessage.replace(":cursor", cursor),
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        if (response.chats.length > 0) {
                            var chats = response.chats;
                            chats.forEach(chat => {
                                html += `<li class="d-flex chat-tab skel-chat" id="`;
                                html += chat['id'] + `"data-profile="`;
                                html += chat['attendee_provider_id'] + `">`;
                                html += `<img class="chat_image skel_chat_img" src="" alt="">`;
                                html += `<div class="d-block">`;
                                html += `<strong class="chat_name skel_chat_name"></strong>`;
                                html += `<span class="latest_message skel_latest_message"></span>`;
                                html += `</div><div class="date latest_message_timestamp skel_latest_message_timestamp"></div>`;
                                html += `<div class="linkedin"><a href="javascript:;"><i class="fa-brands fa-linkedin">`;
                                html += `</i></a></div></li>`;
                            });
                            $('.chat-list').append(html);
                            if (response.cursor) {
                                $('#chat_cursor').val(response.cursor);
                            } else {
                                $('#chat_cursor').val('');
                            }
                            getChatData();
                        } else {
                            $('#chat-loader').hide();
                        }
                    } else {
                        $('#chat-loader').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    $('#chat-loader').hide();
                },
                complete: function () {
                    $('#chat-loader').hide();
                    isLoading = false;
                },
            });
        }
    }

    function getMessages() {
        var chat_id = $(this).attr("id");
        var html = ``;
        $.ajax({
            url: getMessageChatRoute.replace(":chat_id", chat_id),
            type: "GET",
            before: function () {
                var html = `<li class="not_me"><span class="skel_img"></span>
                            <span class="message_text skel_text"></span></li>
                            <li class="is_me"><span class="skel_img"></span>
                            <span class="message_text skel_text"></span></li>`;
                $('#chat-message>ul').html(html);
            },
            success: function (response) {
                if (response.success) {
                    if (response.messages.items.length > 0) {
                        var messages = response.messages.items;
                        messages.forEach(message => {
                            html += `<li class="`;
                            html += message['is_sender'] == 0 ? 'not_me' : 'is_me';
                            html += `">`;
                            html += `<span class="skel_img"></span>`;
                            html += `<span class="message_text">`;
                            var text = message['text'];
                            if (text && text.indexOf('\n') !== -1) {
                                html += text.replace(/\n/g, '<br>');
                            } else {
                                html += text ? text : '';
                            }
                            html += `</span></li>`;
                        });
                        $('#chat-message>ul').html(html);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    }
});
