var sender = null;
var getMessageAjax = null;
var getReceiverAjax = null;
var updateChatListAjax = [];
var getChatAjax = null;
var getUnreadMessageAjax = null;
var getLatestMessageInChatAjax = null;
var receiver = null;
var receivers = [];
let isLoading = false;
let isMessageLoading = false;

$(document).ready(function () {
    $('.chat-tab').on('click', getMessages);
    $('.chat-list').on('scroll', updateChatListLoader);
    $('#search_message').on('input', debounce(search_message, 300));
    $('#send_btn').on('click', sendMessage);
    $('.user_profile').on('click', function () {
        if ($('.conversation_info').hasClass('col-lg-4')) {
            $('.conversation_info').css('display', 'none');
            $('.conversation_info').removeClass('col-lg-4');
            $('.conversation').removeClass('col-lg-8');
            $('.conversation').addClass('col-lg-12');
        } else {
            $('.conversation_info').css('display', 'block');
            $('.conversation_info').addClass('col-lg-4');
            $('.conversation').removeClass('col-lg-12');
            $('.conversation').addClass('col-lg-8');
        }
    });
    $('.unread_label').on('click', function () {
        const $chatMessage = $('#chat-message');
        $chatMessage.animate({ scrollTop: $chatMessage[0].scrollHeight }, 'slow');
        $(this).hide();
    });
    $('.mesasges').on('scroll', function () {
        const $chatMessage = $(this);
        if ($chatMessage[0].scrollTop + $chatMessage[0].clientHeight >= $chatMessage[0].scrollHeight - 10) {
            $('.unread_label').hide();
        } else if ($chatMessage[0].scrollTop <= 10) {
            updateMessageLoader($chatMessage);
        }
    });
    getSender();
    intervalId = setInterval(function () {
        const isAnyAjaxInProgress = (getMessageAjax || getReceiverAjax || updateChatListAjax.length > 0 || getChatAjax || getLatestMessageInChatAjax || getUnreadMessageAjax);
        if (!isAnyAjaxInProgress) getLatestMessageInChat();
    }, 6000);
    unreadInterval = setInterval(function () {
        const isAnyAjaxInProgress = (getMessageAjax || getReceiverAjax || updateChatListAjax.length > 0 || getChatAjax || getLatestMessageInChatAjax || getUnreadMessageAjax);
        if (!isAnyAjaxInProgress) getUnreadMessage();
    }, 30000);
});

$(window).on('beforeunload', function () {
    clearInterval(intervalId);
    clearInterval(unreadInterval);
    if (getMessageAjax) getMessageAjax.abort();
    if (getReceiverAjax) getReceiverAjax.abort();
    if (updateChatListAjax.length > 0) {
        const ajaxRequests = [...updateChatListAjax];
        ajaxRequests.forEach((ajax, index) => {
            if (ajax && ajax.readyState !== 4) {
                ajax.abort();
            }
        });
        updateChatListAjax = [];
    }
    if (getChatAjax) getChatAjax.abort();
    if (getLatestMessageInChatAjax) getLatestMessageInChatAjax.abort();
    if (getUnreadMessageAjax) getUnreadMessageAjax.abort();
    $('.chat-tab').off('click', getMessages);
    $('.chat-list').off('scroll', updateChatListLoader);
    $('#search_message').off('input', debounce(search_message, 300));
    $('#send_btn').off('click', sendMessage);
    $('.mesasges').off('scroll');
});

function getSender() {
    if (sender !== null) return;
    $.ajax({
        url: getChatSender,
        type: "GET",
        success: function (response) {
            if (response.success) {
                sender = response.sender;
                $('.chat-tab')[0].click();
            } else {
                getSender();
            }
        },
        error: function (xhr, status, error) {
            getSender();
        },
    });
}

function getReceiver(chat_id) {
    if (getReceiverAjax) {
        getReceiverAjax.abort();
        getReceiverAjax = null;
    }
    const $conversationInfo = $('.conversation_info .info');
    const connectionDegrees = {
        'FIRST_DEGREE': '1st',
        'SECOND_DEGREE': '2nd',
        'THIRD_DEGREE': '3rd'
    };
    const updateMessages = (selector, profilePictureUrl) => {
        $(selector).each(function () {
            if (profilePictureUrl) {
                $(this).find('.skel_img').replaceWith($('<img>').attr('src', profilePictureUrl));
            } else {
                $(this).find('.skel_img').replaceWith($('<img>').attr('src', "/assets/img/acc.png"));
            }
        });
    };
    const updateConversationInfo = (receiver) => {
        const connection = connectionDegrees[receiver.network_distance] || '';
        const fullName = `${receiver.first_name} ${receiver.last_name}<u>.</u>${connection}`;
        if (receiver.profile_picture_url) {
            $conversationInfo.find('.skel_img').attr('src', receiver.profile_picture_url).removeClass('skel_img');
        } else {
            $conversationInfo.find('.skel_img').attr('src', "/assets/img/acc.png").removeClass('skel_img');
        }
        $conversationInfo.find('.skel_head').html(fullName).removeClass('skel_head');
        $conversationInfo.find('.skel_user_name').html(receiver.headline).removeClass('skel_user_name');
        const email = receiver.contact_info?.emails?.[0];
        const website = receiver.websites?.[0];
        const $userEmailContainer = $conversationInfo.find('.skel_user_email');
        if (email) {
            $userEmailContainer.html(`<a href="mailto:${email}">${email}</a>`).removeClass('skel_user_email');
        } else if (website) {
            $userEmailContainer.html(`<a href="${website}" target="_blank">${website}</a>`).removeClass('skel_user_email');
        } else {
            $userEmailContainer.remove();
        }
        if (receiver.summary) {
            const text = receiver.summary.replace(/\n/g, '<br>');
            $conversationInfo.find('.note').html(text).removeClass('skel_text');
        } else {
            $conversationInfo.find('.note').removeClass('skel_text');
        }
    };
    if (sender) {
        updateMessages('.is_me', sender.profile_picture_url);
    }
    $('.conversation_info>.info').html(`
        <img class="skel_img" src="" alt="">
        <h6 class="skel_head"></h6>
        <span class="user_name skel_user_name"></span>
        <span class="user_email skel_user_email"></span>
        <div class="note skel_text"></div>
    `);
    if (receivers[chat_id]) {
        receiver = receivers[chat_id];
        updateMessages('.not_me', receiver.profile_picture_url);
        updateConversationInfo(receiver);
        getReceiverAjax = null;
    } else {
        getReceiverAjax = $.ajax({
            url: getChatReceiver.replace(':chat_id', chat_id),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    receiver = response.receiver;
                    receivers[chat_id] = receiver;
                    updateMessages('.not_me', receiver.profile_picture_url);
                    updateConversationInfo(receiver);
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
            complete: function () {
                getReceiverAjax = null;
            }
        });
    }
}

function getChatData() {
    const $chats = $('.chat-tab');
    $chats.each(function () {
        const $chat = $(this);
        const $hasSkeletonChatImg = $chat.find('.skel_chat_img').length > 0;
        const $hasSkeletonChatName = $chat.find('.skel_chat_name').length > 0;
        const $hasSkeletonLatestMessage = $chat.find('.skel_latest_message').length > 0;
        const $hasSkeletonTimestamp = $chat.find('.skel_latest_message_timestamp').length > 0;
        if ($hasSkeletonChatImg && $hasSkeletonChatName && $hasSkeletonLatestMessage && $hasSkeletonTimestamp) {
            updateChatListAjax.push(getProfileAndLatestMessage($chat));
        } else {
            if ($hasSkeletonChatImg && $hasSkeletonChatName) {
                updateChatListAjax.push(getProfileMessage($chat));
            }
            if ($hasSkeletonLatestMessage && $hasSkeletonTimestamp) {
                updateChatListAjax.push(getLatestMessage($chat));
            }
        }
    });
    $('.chat-tab').on('click', getMessages);
}

function getMessages() {
    const chat_id = $(this).attr("id");
    const $chatMessage = $('#chat-message');
    const $conversationInfo = $('.conversation_info .info');
    const $send_form = $('.send_form');
    $('.selected').removeClass('selected');
    $(this).addClass('selected');
    if (getMessageAjax) {
        getMessageAjax.abort();
        getMessageAjax = null;
    }
    const ajaxRequests = [...updateChatListAjax];
    ajaxRequests.forEach((ajax, index) => {
        if (ajax && ajax.readyState !== 4) {
            ajax.abort();
        }
    });
    updateChatListAjax = [];
    const connectionDegrees = {
        'FIRST_DEGREE': '1st',
        'SECOND_DEGREE': '2nd',
        'THIRD_DEGREE': '3rd'
    };
    if ($(this).attr("data-disable") != 'true') {
        $send_form.html(`
            <input type="file" name="attachment" id="attachment" style="display: none;">
            <label for="attachment" class="custom-file-label"></label>
            <textarea placeholder="Send a message" name="sendMessage" class="sendMessage" id="sendMessage"></textarea>
            <input type="button" class="send_btn" id="send_btn" value="send">`);
    } else {
        $send_form.html(``);
    }
    if (chat_id != '') {
        $chatMessage.find('ul').html(`
            <li class="not_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
            <li class="is_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
            <li class="not_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
            <li class="is_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
        `);
        getMessageAjax = $.ajax({
            url: getMessageChatRoute.replace(":chat_id", chat_id),
            type: "GET",
            beforeSend: function () {
                $('.conversation_info>.info').html(`
                    <img class="skel_img" src="" alt="">
                    <h6 class="skel_head"></h6>
                    <span class="user_name skel_user_name"></span>
                    <span class="user_email skel_user_email"></span>
                    <div class="note skel_text"></div>
                `);
                if (receivers[chat_id]) {
                    receiver = receivers[chat_id];
                    const connection = connectionDegrees[receiver.network_distance] || '';
                    const fullName = `${receiver.first_name} ${receiver.last_name}<u>.</u>${connection}`;
                    if (receiver.profile_picture_url) {
                        $conversationInfo.find('.skel_img').attr('src', receiver.profile_picture_url).removeClass('skel_img');
                    } else {
                        $conversationInfo.find('.skel_img').attr('src', "/assets/img/acc.png").removeClass('skel_img');
                    }
                    $conversationInfo.find('.skel_head').html(fullName).removeClass('skel_head');
                    $conversationInfo.find('.skel_user_name').html(receiver.headline).removeClass('skel_user_name');
                    const email = receiver.contact_info?.emails?.[0];
                    const website = receiver.websites?.[0];
                    const $userEmailContainer = $conversationInfo.find('.skel_user_email');
                    if (email) {
                        $userEmailContainer.html(`<a href="mailto:${email}">${email}</a>`).removeClass('skel_user_email');
                    } else if (website) {
                        $userEmailContainer.html(`<a href="${website}" target="_blank">${website}</a>`).removeClass('skel_user_email');
                    } else {
                        $userEmailContainer.remove();
                    }
                    if (receiver.summary) {
                        const text = receiver.summary.replace(/\n/g, '<br>');
                        $conversationInfo.find('.note').html(text).removeClass('skel_text');
                    } else {
                        $conversationInfo.find('.note').removeClass('skel_text');
                    }
                }
            },
            success: function (response) {
                if (response.success && response.messages.length > 0) {
                    $('#' + chat_id + ' .unread_count').remove();
                    const $messageHtml = response.messages.map(message => {
                        const isSenderClass = message.is_sender == 0 ? 'not_me' : 'is_me';
                        let messageContent = `<div class="message_content">`;
                        if (message.deleted == 0) {
                            if (message.text) {
                                const text = message.text.replace(/\n/g, '<br>');
                                messageContent += `<span class="message_text">${text}</span>`;
                            }
                            if (message.attachments) {
                                message.attachments.forEach(attachment => {
                                    if (attachment.type == "img") {
                                        messageContent += `<img style={width:"${attachment.size.width}"; height:"${attachment.size.height}";} id="${attachment.id}" data-mimeType="${attachment.type}" data-fileName="" class="attach_img dummy_attach_img" src="${attachment.url}">`;
                                    } else {
                                        messageContent += `<span class="attach_file" id="${attachment.id}">${attachment.file_name}<a href="${attachment.url}" download></a></span>`;
                                    }
                                });
                            }
                            if (!message.text && !message.attachments) {
                                messageContent += `<span class="message_text"></span>`;
                            }
                        } else {
                            messageContent += `
                            <span class="message_text" style="
                                padding: 2px 10px; 
                                height: fit-content;
                                background-color: #f4f2ee;
                                color: #000;
                                border: 1px solid #343434; 
                                box-shadow: inset 4px 4px 4px #8c8c8c, inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;">
                                This message has been deleted.
                            </span>`;
                        }
                        messageContent += `</div>`;
                        return `<li class="${isSenderClass}" id="${message.id}"><span class="skel_img"></span>${messageContent}</li>`;
                    }).join('');
                    $chatMessage.find('ul').html($messageHtml);
                    $('#message_cursor').val(response.cursor || '');
                    getReceiver(chat_id);
                }
                getChatData();
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
            complete: function () {
                getMessageAjax = null;
                $chatMessage.attr('data-chat', chat_id);
                $chatMessage.animate({ scrollTop: $chatMessage[0].scrollHeight }, 'slow');
                getImages();
            },
        });
    } else {
        $chatMessage.attr('data-chat', 'null').find('ul').html(``);
        $('.conversation_info>.info').html(`
            <img class="skel_img" src="" alt="">
            <h6 class="skel_head"></h6>
            <span class="user_name skel_user_name"></span>
            <span class="user_email skel_user_email"></span>
            <div class="note skel_text"></div>
        `);
        const profile_id = $(this).attr('data-profile');
        if (receivers[profile_id]) {
            receiver = receivers[profile_id];
            const connection = connectionDegrees[receiver.network_distance] || '';
            const fullName = `${receiver.first_name} ${receiver.last_name}<u>.</u>${connection}`;
            if (receiver.profile_picture_url) {
                $conversationInfo.find('.skel_img').attr('src', receiver.profile_picture_url).removeClass('skel_img');
            } else {
                $conversationInfo.find('.skel_img').attr('src', "/assets/img/acc.png").removeClass('skel_img');
            }
            $conversationInfo.find('.skel_head').html(fullName).removeClass('skel_head');
            $conversationInfo.find('.skel_user_name').html(receiver.headline).removeClass('skel_user_name');
            const email = receiver.contact_info?.emails?.[0];
            const website = receiver.websites?.[0];
            const $userEmailContainer = $conversationInfo.find('.skel_user_email');
            if (email) {
                $userEmailContainer.html(`<a href="mailto:${email}">${email}</a>`).removeClass('skel_user_email');
            } else if (website) {
                $userEmailContainer.html(`<a href="${website}" target="_blank">${website}</a>`).removeClass('skel_user_email');
            } else {
                $userEmailContainer.remove();
            }
            if (receiver.summary) {
                const text = receiver.summary.replace(/\n/g, '<br>');
                $conversationInfo.find('.note').html(text).removeClass('skel_text');
            } else {
                $conversationInfo.find('.note').removeClass('skel_text');
            }
        } else {
            $.ajax({
                url: getProfileByIdRoute.replace(':profile_id', profile_id),
                type: "GET",
                success: function (response) {
                    receiver = response.user_profile;
                    const connection = connectionDegrees[receiver.network_distance] || '';
                    const fullName = `${receiver.first_name} ${receiver.last_name}<u>.</u>${connection}`;
                    if (receiver.profile_picture_url) {
                        $conversationInfo.find('.skel_img').attr('src', receiver.profile_picture_url).removeClass('skel_img');
                    } else {
                        $conversationInfo.find('.skel_img').attr('src', "/assets/img/acc.png").removeClass('skel_img');
                    }
                    $conversationInfo.find('.skel_head').html(fullName).removeClass('skel_head');
                    $conversationInfo.find('.skel_user_name').html(receiver.headline).removeClass('skel_user_name');
                    const email = receiver.contact_info?.emails?.[0];
                    const website = receiver.websites?.[0];
                    const $userEmailContainer = $conversationInfo.find('.skel_user_email');
                    if (email) {
                        $userEmailContainer.html(`<a href="mailto:${email}">${email}</a>`).removeClass('skel_user_email');
                    } else if (website) {
                        $userEmailContainer.html(`<a href="${website}" target="_blank">${website}</a>`).removeClass('skel_user_email');
                    } else {
                        $userEmailContainer.remove();
                    }
                    if (receiver.summary) {
                        const text = receiver.summary.replace(/\n/g, '<br>');
                        $conversationInfo.find('.note').html(text).removeClass('skel_text');
                    } else {
                        $conversationInfo.find('.note').removeClass('skel_text');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                },
                complete: function () {
                    getChatData();
                }
            });
        }
    }
    $('#send_btn').on('click', sendMessage);
}

function getProfileAndLatestMessage($chat) {
    const profileId = $chat.attr('data-profile');
    const chat_id = $chat.prop('id');
    const ajaxRequest = $.ajax({
        url: getProfileAndLatestMessageRoute.replace(":profile_id", profileId).replace(":chat_id", chat_id),
        type: "GET",
        success: function (response) {
            if (response.success) {
                const profile = response.user_profile;
                const $chatName = $chat.find('.chat_name');
                const $chatImage = $chat.find('.chat_image');
                const $latestMessage = $chat.find('.latest_message');
                const $latestMessageTimestamp = $chat.find('.latest_message_timestamp');
                $chatName.text(`${profile.first_name} ${profile.last_name}`).removeClass('skel_chat_name');
                if (profile.profile_picture_url) {
                    $chatImage.replaceWith($('<img>').attr('src', profile.profile_picture_url));
                } else {
                    $chatImage.replaceWith($('<img>').attr('src', "/assets/img/acc.png"));
                }
                receivers[chat_id] = profile;
                if (response.message?.length) {
                    const latestMessage = response.message[0];
                    if (latestMessage.text) {
                        const trimmedText = latestMessage.text.length > 25 ? `${latestMessage.text.substring(0, 25)}...` : latestMessage.text;
                        $latestMessage.html(trimmedText);
                    } else if (latestMessage.attachments?.length) {
                        const attachment = latestMessage.attachments[0];
                        $latestMessage.html(attachment.file_name);
                    } else {
                        $latestMessage.html('');
                    }
                    $latestMessage.removeClass('skel_latest_message');
                    if (latestMessage.timestamp) {
                        const formattedDate = new Date(latestMessage.timestamp).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                        $latestMessageTimestamp.html(formattedDate);
                    } else {
                        $latestMessageTimestamp.html('');
                    }
                    $latestMessageTimestamp.removeClass('skel_latest_message_timestamp');
                } else {
                    $chat.prop('id', '');
                    $latestMessage.removeClass('skel_latest_message');
                    $latestMessageTimestamp.removeClass('skel_latest_message_timestamp');
                }
            } else {
                $chat.remove();
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            const index = updateChatListAjax.indexOf(ajaxRequest);
            if (index > -1) {
                updateChatListAjax.splice(index, 1);
            }
            $chat.removeClass('skel-chat');
        },
    });
    return ajaxRequest;
}

function getProfileMessage($chat) {
    const profileId = $chat.data('profile');
    const chat_id = $chat.prop('id');
    const $chatName = $chat.find('.chat_name');
    const $chatImage = $chat.find('.chat_image');
    const ajaxRequest = $.ajax({
        url: getChatProfile.replace(":profile_id", profileId),
        type: "GET",
        success: function (response) {
            if (response.success) {
                const profile = response.user_profile;
                $chatName.text(`${profile.first_name} ${profile.last_name}`).removeClass('skel_chat_name');
                if (profile.profile_picture_url) {
                    $chatImage.replaceWith($('<img>').attr('src', profile.profile_picture_url));
                } else {
                    $chatImage.replaceWith($('<img>').attr('src', "/assets/img/acc.png"));
                }
                receivers[chat_id] = profile;
            } else {
                $chat.remove();
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            const index = updateChatListAjax.indexOf(ajaxRequest);
            if (index > -1) {
                updateChatListAjax.splice(index, 1);
            }
            $chat.removeClass('skel-chat');
        },
    });
    return ajaxRequest;
}

function getLatestMessage($chat) {
    const chat_id = $chat.prop('id');
    const $latestMessage = $chat.find('.latest_message');
    const $latestMessageTimestamp = $chat.find('.latest_message_timestamp');
    const ajaxRequest = $.ajax({
        url: getLatestMessageRoute.replace(":chat_id", chat_id),
        type: "GET",
        success: function (response) {
            if (response.success && response.message?.length) {
                const latestMessage = response.message[0];
                if (latestMessage.text) {
                    const trimmedText = latestMessage.text.length > 25 ? `${latestMessage.text.substring(0, 25)}...` : latestMessage.text;
                    $latestMessage.html(trimmedText);
                } else if (latestMessage.attachments?.length) {
                    $latestMessage.html(latestMessage.attachments[0].file_name);
                } else {
                    $latestMessage.html('');
                }
                $latestMessage.removeClass('skel_latest_message');
                if (latestMessage.timestamp) {
                    const formattedDate = new Date(latestMessage.timestamp).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                    $latestMessageTimestamp.html(formattedDate);
                } else {
                    $latestMessageTimestamp.html('');
                }
                $latestMessageTimestamp.removeClass('skel_latest_message_timestamp');
            } else {
                $chat.prop('id', '');
                $latestMessage.removeClass('skel_latest_message');
                $latestMessageTimestamp.removeClass('skel_latest_message_timestamp');
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            const index = updateChatListAjax.indexOf(ajaxRequest);
            if (index > -1) {
                updateChatListAjax.splice(index, 1);
            }
            $chat.removeClass('skel-chat');
        }
    });
    return ajaxRequest;
}

function updateChatListLoader(e) {
    const search = $('#search_message').val().trim();
    if (search || isLoading) {
        e.preventDefault();
        return;
    }
    const $this = $(this);
    if ($this[0].scrollTop + $this[0].clientHeight >= $this[0].scrollHeight - 10) {
        $('#chat-loader').show();
        updateChatList();
    }
}

function updateChatList() {
    const cursor = $('#chat_cursor').val();
    const $chatList = $('.chat-list');
    const $chatLoader = $('#chat-loader');
    const $chatCursor = $('#chat_cursor');
    isLoading = true;
    let html = '';
    if (cursor === 'emp') {
        $chatList.hide();
        $chatLoader.css({
            height: '70vh',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        }).show();
    }
    const ajaxRequest = $.ajax({
        url: getRemainMessage.replace(":cursor", cursor),
        type: "GET",
        success: function (response) {
            if (response.success && response.chats.length > 0) {
                html = response.chats.reduce((acc, chat) => {
                    let disableChat = 'false';
                    if (chat.read_only || (Array.isArray(chat.disabledFeatures) && chat.disabledFeatures.includes('reply'))) {
                        disableChat = 'true';
                    }
                    if (chat.folder.includes('INBOX_LINKEDIN_CLASSIC') && chat.archived === 0) {
                        acc += `
                            <li class="d-flex chat-tab skel-chat" id="${chat.id}" data-profile="${chat.attendee_provider_id}"
                                data-disable="${disableChat}">
                                ${chat.unread === 1 ? `<span class="unread_count">${chat.unread_count}</span>` : ''}
                                <span class="chat_image skel_chat_img"></span>
                                <div class="d-block">
                                    <strong class="chat_name skel_chat_name"></strong>
                                    <span class="latest_message skel_latest_message"></span>
                                </div>
                                <div class="date latest_message_timestamp skel_latest_message_timestamp"></div>
                                <div class="linkedin">
                                    <a href="javascript:;">
                                        <i class="fa-brands fa-linkedin"></i>
                                    </a>
                                </div>
                            </li>`;
                    }
                    return acc;
                }, '');
                if (cursor === 'emp') {
                    $chatList.html(html).show();
                } else {
                    $chatList.append(html);
                }
                if (response.cursor) {
                    $chatCursor.val(response.cursor);
                } else {
                    $chatCursor.val('');
                }
                getChatData();
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            $('.chat-tab').on('click', getMessages);
            $chatLoader.hide();
            isLoading = false;
        }
    });
    updateChatListAjax.push(ajaxRequest);
}

function updateMessageLoader($chatMessage) {
    if (isMessageLoading) return;
    const $messageCursor = $('#message_cursor');
    const $messageLoader = $('#message-loader');
    const cursor = $messageCursor.val();
    const chat_id = $chatMessage.attr('data-chat');
    if (cursor !== '' && $chatMessage[0].scrollTop <= 10) {
        isMessageLoading = true;
        $messageLoader.show();
        $.ajax({
            url: getMessageChatCursorRoute.replace(":chat_id", chat_id).replace(":cursor", cursor),
            type: "GET",
            success: function (response) {
                if (response.success && response.messages.length > 0) {
                    const messagesHtml = response.messages.map(message => {
                        const isSenderClass = message.is_sender === 0 ? 'not_me' : 'is_me';
                        let messageContent = `<div class="message_content">`;
                        if (message.deleted == 0) {
                            if (message.text) {
                                const text = message.text.replace(/\n/g, '<br>');
                                messageContent += `<span class="message_text">${text}</span>`;
                            }
                            if (message.attachments) {
                                message.attachments.forEach(attachment => {
                                    if (attachment.type == "img") {
                                        messageContent += `<img style={width:"${attachment.size.width}"; height:"${attachment.size.height}";} id="${attachment.id}" data-mimeType="${attachment.type}" data-fileName="" class="attach_img dummy_attach_img" src="${attachment.url}">`;
                                    } else {
                                        messageContent += `<span class="attach_file" id="${attachment.id}">${attachment.file_name}<a href="${attachment.url}" download></a></span>`;
                                    }
                                });
                            }
                            if (!message.text && !message.attachments) {
                                messageContent += `<span class="message_text"></span>`;
                            }
                        } else {
                            messageContent += `
                            <span class="message_text" style="
                                padding: 2px 10px; 
                                height: fit-content;
                                background-color: #f4f2ee;
                                color: #000;
                                border: 1px solid #343434; 
                                box-shadow: inset 4px 4px 4px #8c8c8c, inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;">
                                This message has been deleted.
                            </span>`;
                        }
                        messageContent += `</div>`;
                        return `<li class="${isSenderClass}" id="${message.id}"><span class="skel_img"></span>${messageContent}</li>`;
                    }).join('');
                    $chatMessage.find('ul').prepend(messagesHtml);
                    $messageCursor.val(response.cursor || '');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
            complete: function () {
                $messageLoader.hide();
                isMessageLoading = false;
                if (receiver) {
                    $('.is_me .skel_img').each(function () {
                        if (sender.profile_picture_url) {
                            $(this).replaceWith($('<img>').attr('src', sender.profile_picture_url)).removeClass('skel_img');
                        } else {
                            $(this).replaceWith($('<img>').attr('src', "/assets/img/acc.png")).removeClass('skel_img');
                        }
                    });
                    $('.not_me .skel_img').each(function () {
                        if (receiver.profile_picture_url) {
                            $(this).replaceWith($('<img>').attr('src', receiver.profile_picture_url)).removeClass('skel_img');
                        } else {
                            $(this).replaceWith($('<img>').attr('src', "/assets/img/acc.png")).removeClass('skel_img');
                        }
                    });
                } else {
                    getReceiver(chat_id);
                }
                $chatMessage.animate({ scrollTop: $chatMessage[0].scrollHeight }, 'slow');
                getImages();
            }
        });
    } else {
        isMessageLoading = false;
    }
}

function sendMessage(e) {
    e.preventDefault();
    const $chatMessage = $('#chat-message');
    const message = $('#sendMessage').val().trim();
    const chat_id = $chatMessage.attr('data-chat');
    if (message === '') return;
    const formData = new FormData();
    formData.append('message', message);
    formData.append('chat_id', chat_id);
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    if (chat_id != 'null') {
        $.ajax({
            url: sendMessageRoute,
            data: formData,
            type: "POST",
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: function (response) {
                if (response.success && response.message) {
                    var chat = $('#' + chat_id);
                    $('.chat-list').prepend(chat);
                    const messageData = response.message;
                    const isSenderClass = messageData.is_sender === 0 ? 'not_me' : 'is_me';
                    let messageContent = `<div class="message_content">`;
                    if (messageData.deleted == 0) {
                        if (messageData.text) {
                            const text = messageData.text.replace(/\n/g, '<br>');
                            messageContent += `<span class="message_text">${text}</span>`;
                        }
                        if (messageData.attachments) {
                            messageData.attachments.forEach(attachment => {
                                messageContent += `<span class="attach_file" id="${attachment.id}">${attachment.file_name}<a href="${attachment.url}" download></a></span>`;
                            });
                        }
                        if (!messageData.text && !messageData.attachments) {
                            messageContent += `<span class="message_text"></span>`;
                        }
                    } else {
                        messageContent += `
                            <span class="message_text" style="
                                padding: 2px 10px; 
                                height: fit-content;
                                background-color: #f4f2ee;
                                color: #000;
                                border: 1px solid #343434; 
                                box-shadow: inset 4px 4px 4px #8c8c8c, inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;">
                                This message has been deleted.
                            </span>`;
                    }
                    messageContent += `</div>`;
                    const $messageHtml = `<li class="${isSenderClass}" id="${messageData.id}"><span class="skel_img"></span>${messageContent}</li>`;
                    $('#' + messageData.chat_id + ' .unread_count').remove();
                    $('#chat-message>ul').append($messageHtml);
                    if (sender) {
                        $('.is_me .skel_img').each(function () {
                            if (sender.profile_picture_url) {
                                $(this).replaceWith($('<img>').attr('src', sender.profile_picture_url)).removeClass('skel_img');
                            } else {
                                $(this).replaceWith($('<img>').attr('src', "/assets/img/acc.png")).removeClass('skel_img');
                            }
                        });
                    }
                    const latestMessage = response.message;
                    const $latestMessage = $('#' + chat_id).find('.latest_message');
                    const $latestMessageTimestamp = $('#' + chat_id).find('.latest_message_timestamp');
                    if (latestMessage.text) {
                        const trimmedText = latestMessage.text.length > 25 ? `${latestMessage.text.substring(0, 25)}...` : latestMessage.text;
                        $latestMessage.html(trimmedText);
                    } else if (latestMessage.attachments?.length) {
                        $latestMessage.html(latestMessage.attachments[0].file_name);
                    } else {
                        $latestMessage.html('');
                    }
                    $latestMessage.removeClass('skel_latest_message');
                    if (latestMessage.timestamp) {
                        const formattedDate = new Date(latestMessage.timestamp).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                        $latestMessageTimestamp.html(formattedDate);
                    } else {
                        $latestMessageTimestamp.html('');
                    }
                    $latestMessageTimestamp.removeClass('skel_latest_message_timestamp');
                }
                $('#sendMessage').val('');
                $chatMessage.animate({ scrollTop: $chatMessage[0].scrollHeight }, 'slow');
            },
            error: function (xhr, status, error) {
                console.error('Error sending message:', error);
            }
        });
    } else {
        var attendee_id = $('.selected').attr('data-profile');
        formData.append('attendee_id', attendee_id);
        $.ajax({
            url: sendMessageRoute,
            data: formData,
            type: "POST",
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: function (response) {
                if (response.success && response.chat_id) {
                    $('.selected').prop('id', response.chat_id);
                    $('#' + response.chat_id).click();
                }
                $('#sendMessage').val('');
            },
            error: function (xhr, status, error) {
                console.error('Error sending message:', error);
            }
        });
    }
}

function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

function search_message() {
    if (getChatAjax) {
        getChatAjax.abort();
        getChatAjax = null;
    }
    const ajaxRequests = [...updateChatListAjax];
    ajaxRequests.forEach((ajax, index) => {
        if (ajax && ajax.readyState !== 4) {
            ajax.abort();
        }
    });
    updateChatListAjax = [];
    const search = $(this).val().trim();
    if (search === '') {
        $('#chat_cursor').val('emp');
        updateChatList();
        return;
    }
    $('.chat-list').hide();
    $('#chat-loader').css({
        "height": "70vh",
        "display": "flex",
        "align-items": "center",
        "justify-content": "center"
    }).show();
    const formData = new FormData();
    formData.append('keywords', search);
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    getChatAjax = $.ajax({
        url: messageSearch,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        headers: { "X-CSRF-TOKEN": csrfToken },
        success: function (response) {
            if (response.success) {
                const chats = response.chats;
                let html = '';
                chats.forEach(chat => {
                    if (!chat['provider']) {
                        if (chat.folder.includes('INBOX_LINKEDIN_CLASSIC') && chat.archived === 0) {
                            html += `
                            <li class="d-flex chat-tab skel-chat" id="${chat.id}" data-profile="${chat.attendee_provider_id}">
                            ${chat.unread === 1 ? `<span class="unread_count">${chat.unread_count}</span>` : ''}
                            <span class="chat_image skel_chat_img"></span><div class="d-block">
                            <strong class="chat_name skel_chat_name"></strong>
                            <span class="latest_message skel_latest_message"></span></div>
                            <div class="date latest_message_timestamp skel_latest_message_timestamp"></div>
                            <div class="linkedin"><a href="javascript:;">
                            <i class="fa-brands fa-linkedin"></i></a></div></li>`;
                        }
                    } else {
                        html += `
                        <li class="d-flex chat-tab skel-chat" id="" data-profile="${chat.provider}">
                        <span class="chat_image skel_chat_img"></span><div class="d-block">
                        <strong class="chat_name skel_chat_name"></strong>
                        <span class="latest_message skel_latest_message"></span></div>
                        <div class="date latest_message_timestamp skel_latest_message_timestamp"></div>
                        <div class="linkedin"><a href="javascript:;">
                        <i class="fa-brands fa-linkedin"></i></a></div></li>`;
                    }
                });
                $('.chat-list').html(html).show();
                getChatData();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error during search:', error);
        },
        complete: function () {
            $('.chat-tab').on('click', getMessages);
            getChatAjax = null;
            $('#chat-loader').hide();
        }
    });
}

function getLatestMessageInChat() {
    const $chatMessage = $('#chat-message');
    const chatId = $chatMessage.attr('data-chat');
    const count = $chatMessage.find('>ul>li').length || 0;
    if (!chatId) return;
    if (getLatestMessageInChatAjax) {
        getLatestMessageInChatAjax.abort();
        getLatestMessageInChatAjax = null;
    }
    getLatestMessageInChatAjax = $.ajax({
        url: getLatestMessageInChatRoute.replace(":chat_id", chatId).replace(':count', count),
        type: "GET",
        success: function (response) {
            if (response.success && response.messages.length > 0) {
                const messages = response.messages;
                const $latestMessage = $('#' + chatId).find('.latest_message');
                const $latestMessageTimestamp = $('#' + chatId).find('.latest_message_timestamp');
                const latestMessage = messages[messages.length - 1];
                if (latestMessage.text) {
                    const trimmedText = latestMessage.text.length > 25 ? `${latestMessage.text.substring(0, 25)}...` : latestMessage.text;
                    $latestMessage.html(trimmedText);
                } else if (latestMessage.attachments?.length) {
                    $latestMessage.html(latestMessage.attachments[0].file_name);
                } else {
                    $latestMessage.html('');
                }
                $latestMessage.removeClass('skel_latest_message');
                if (latestMessage.timestamp) {
                    const formattedDate = new Date(latestMessage.timestamp).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                    $latestMessageTimestamp.html(formattedDate);
                } else {
                    $latestMessageTimestamp.html('');
                }
                $latestMessageTimestamp.removeClass('skel_latest_message_timestamp');
                const $chatList = $chatMessage.find('>ul');
                $('#' + chatId).find('.unread_count').remove();
                const $messageHtml = response.messages.map(message => {
                    if ($('#' + message.id).length == 0) {
                        $('.unread_label').show();
                        const isSenderClass = message.is_sender == 0 ? 'not_me' : 'is_me';
                        let messageContent = `<div class="message_content">`;
                        if (message.deleted == 0) {
                            if (message.text) {
                                const text = message.text.replace(/\n/g, '<br>');
                                messageContent += `<span class="message_text">${text}</span>`;
                            }
                            if (message.attachments) {
                                message.attachments.forEach(attachment => {
                                    if (attachment.type == "img") {
                                        messageContent += `<img style="width:${attachment.size.width}px; height:${attachment.size.height}px;" id="${attachment.id}" data-mimeType="${attachment.type}" data-fileName="" class="attach_img dummy_attach_img" src="${attachment.url}">`;
                                    } else {
                                        messageContent += `<span class="attach_file" id="${attachment.id}">${attachment.file_name}<a href="${attachment.url}" download></a></span>`;
                                    }
                                });
                            }
                            if (!message.text && !message.attachments) {
                                messageContent += `<span class="message_text"></span>`;
                            }
                        } else {
                            messageContent += `
                        <span class="message_text" style="
                            padding: 2px 10px; 
                            height: fit-content;
                            background-color: #f4f2ee;
                            color: #000;
                            border: 1px solid #343434; 
                            box-shadow: inset 4px 4px 4px #8c8c8c, inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;">
                            This message has been deleted.
                        </span>`;
                        }
                        messageContent += `</div>`;
                        return `<li class="${isSenderClass}" id="${message.id}"><span class="skel_img"></span>${messageContent}</li>`;
                    }
                }).join('');
                $chatList.append($messageHtml);
                if (sender) {
                    const $isMeElements = $('.is_me');
                    const profilePictureUrl = sender.profile_picture_url;
                    var imgTag = ``;
                    if (profilePictureUrl) {
                        imgTag = $('<img>').attr('src', profilePictureUrl);
                    } else {
                        imgTag = $('<img>').attr('src', "/assets/img/acc.png");
                    }
                    $isMeElements.each(function () {
                        const $skelImg = $(this).find('.skel_img');
                        $skelImg.replaceWith(imgTag.clone().removeClass('skel_img'));
                    });
                }
                if (receiver) {
                    const $notMeElements = $('.not_me');
                    const profilePictureUrl = receiver.profile_picture_url;
                    var imgTag = ``;
                    if (profilePictureUrl) {
                        imgTag = $('<img>').attr('src', profilePictureUrl);
                    } else {
                        imgTag = $('<img>').attr('src', "/assets/img/acc.png");
                    }
                    $notMeElements.each(function () {
                        const $skelImg = $(this).find('.skel_img');
                        $skelImg.replaceWith(imgTag.clone());
                    });
                }
                const $messageCursor = $('#message_cursor');
                if (response.cursor) {
                    $messageCursor.val(response.cursor);
                } else {
                    $messageCursor.val('');
                }
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            getLatestMessageInChatAjax = null;
            getImages();
        }
    });
}

function getUnreadMessage() {
    if (getUnreadMessageAjax) {
        getUnreadMessageAjax.abort();
        getUnreadMessageAjax = null;
    }
    getUnreadMessageAjax = $.ajax({
        url: unreadMessage,
        type: "GET",
        success: function (response) {
            if (response.success && response.chats.length > 0) {
                const $chatList = $('.chat-list');
                const updates = [];
                const $chatMessageId = $('#chat-message').attr('data-chat');
                response.chats.forEach(chat => {
                    if (chat.unread === 1 && chat.id !== $chatMessageId) {
                        const $listItem = $('#' + chat.id);
                        if ($listItem.length === 0) {
                            $listItem = $(`
                                <li class="d-flex chat-tab" id="${chat.id}" data-profile="${chat.attendee_provider_id}">
                                    <span class="chat_image"></span>
                                    <div class="d-block">
                                        <strong class="chat_name"></strong>
                                        <span class="latest_message"></span>
                                    </div>
                                    <div class="date latest_message_timestamp"></div>
                                    <div class="linkedin">
                                        <a href="javascript:;">
                                            <i class="fa-brands fa-linkedin"></i>
                                        </a>
                                    </div>
                                </li>
                            `);
                        }
                        const $unreadCount = $listItem.find('.unread_count');
                        if ($unreadCount.length) {
                            $unreadCount.text(chat.unread_count);
                        } else {
                            $listItem.prepend(`<span class="unread_count">${chat.unread_count}</span>`);
                        }
                        const latestMessage = chat.messages[0];
                        const $latestMessage = $listItem.find('.latest_message');
                        if (latestMessage.text) {
                            const trimmedText = latestMessage.text.length > 25 ? `${latestMessage.text.substring(0, 25)}...` : latestMessage.text;
                            $latestMessage.html(trimmedText);
                        } else if (latestMessage.attachments?.length) {
                            $latestMessage.html(latestMessage.attachments[0].file_name);
                        } else {
                            $latestMessage.html('');
                        }
                        const $latestMessageTimestamp = $listItem.find('.latest_message_timestamp');
                        if (latestMessage.timestamp) {
                            const formattedDate = new Date(latestMessage.timestamp).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                            $latestMessageTimestamp.html(formattedDate);
                        } else {
                            $latestMessageTimestamp.html('');
                        }
                        updates.push($listItem);
                    }
                });
                if (updates.length > 0) {
                    $chatList.prepend(updates);
                }
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            $('.chat-tab').on('click', getMessages);
            getUnreadMessageAjax = null;
        }
    });
}

function getImages() {
    const images = $('.dummy_attach_img');
    images.each(function (index, image) {
        var $image = $(image);
        var attachment_id = $image.prop('id');
        var message_id = $image.closest('li').prop('id');
        var mimetype = $image.attr('data-mimeType');
        var file_name = $(this).attr('data-fileName');
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        var formData = new FormData();
        formData.append('message_id', message_id);
        formData.append('attachment_id', attachment_id);
        formData.append('mimetype', mimetype);
        formData.append('file_name', file_name);
        $.ajax({
            url: getAnAttachmentFromMessage,
            type: "POST",
            data: formData,
            headers: { "X-CSRF-TOKEN": csrfToken },
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                const url = window.URL.createObjectURL(data);
                $image.attr('src', url);
                $image.attr('alt', 'Attachment');
                $image.removeClass('dummy_attach_img');
            },
            error: function (error, xhr, status) {
                console.error(error);
            }
        });
    });
}