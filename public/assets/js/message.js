$(document).ready(function () {
    $('.chat-list').on('scroll', updateChatList);
    $('.chat-tab').on('click', getMessages);

    function updateChatList() {
        var contentHeight = $(this).prop('scrollHeight');
        var visibleHeight = $(this).height();
        var scrollTop = $(this).scrollTop();
        if (scrollTop + visibleHeight >= contentHeight - 1) {
            $('#chat-loader').show();
            var cursor = $('#chat_cursor').val();
            var html = ``;
            $.ajax({
                url: getRemainMessage.replace(":cursor", cursor),
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        if (response.chats.length > 0) {
                            var chats = response.chats;
                            chats.forEach(chat => {
                                html += `<li class="d-flex chat-tab" id="`;
                                html += chat['last_message']['chat_id'];
                                html += `">`;
                                if (chat['profile_picture_url']) {
                                    html += `<img src="` + chat['profile_picture_url'] + `" alt="">`;
                                } else {
                                    html += `<img src="" alt="">`;
                                }
                                if (chat['last_message'] && chat['last_message']['text']) {
                                    let input = chat['last_message']['text'];
                                    if (input.length > 25) {
                                        let trimmed_text = input.substring(0, 25) + '...';
                                        chat['last_message']['text'] = trimmed_text;
                                    }
                                }
                                if (chat['last_message'] && chat['last_message']['timestamp']) {
                                    let date = new Date(chat['last_message']['timestamp']);
                                    let options = { day: '2-digit', month: 'short' };
                                    chat['last_message']['timestamp'] = date.toLocaleDateString('en-GB', options);
                                }
                                html += `<div class="d-block"><strong>`;
                                html += chat['first_name'] + ' ' + chat['last_name'];
                                html += `</strong><span>`;
                                html += chat['last_message']['text'] + `</span></div>`;
                                html += `<div class="date">` + chat['last_message']['timestamp'] + `</div><div class="linkedin">`;
                                html += `<a href="javascript:;"><i class="fa-brands fa-linkedin"></i></a></div></li>`;
                            });
                            $('.chat-list').append(html);
                            if (response.cursor) {
                                $('#chat_cursor').val(response.cursor);
                            } else {
                                $('#chat_cursor').val('');
                            }
                        }
                    }
                    $('.chat-list').on('scroll', updateChatList);
                    $('.chat-tab').on('click', getMessages);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                },
                complete: function () {
                    $('#chat-loader').hide();
                },
            });
        }
    }

    function getMessages() {
        var chat_id = $(this).attr("id");
        var html = ``;
        $("#loader").show();
        $.ajax({
            url: getMessageChatRoute.replace(":chat_id", chat_id),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    var items = response.messages.items;
                    items.forEach(message => {
                        html += `<li class="`;
                        html += message['is_sender'] == 0 ? 'not_me' : 'is_me';
                        html += `">`;
                        html += `<img src="" alt="">`;
                        html += `<span>`;
                        var text = message['text'];
                        html += text.replace(/\n/g, '<br>');
                        html += `</span></li>`;
                    });
                    $('#chat-message>ul').html(html);
                    if (response.sender && response.sender.profile_picture_url) {
                        $('#chat-message>ul>.is_me>img').prop('src', response.sender.profile_picture_url);
                    }
                    if (response.receiver && response.receiver.profile_picture_url) {
                        $('#chat-message>ul>.not_me>img').prop('src', response.receiver.profile_picture_url);
                    }
                }
                $('.chat-list').on('scroll', updateChatList);
                $('.chat-tab').on('click', getMessages);
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    }
});
