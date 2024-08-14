var sender = null;
var getMessageAjax = null;
var getReceiverAjax = null;
var updateChatListAjax = [];
var receiver = null;
var receivers = [];
let isLoading = false;
let isMessageLoading = false;
var getChatAjax = null;

$(document).ready(function () {
    $('.chat-tab').on('click', getMessages);
    $('.chat-list').on('scroll', updateChatListLoader);
    $('.mesasges').on('scroll', updateMessageLoader);
    $('#search_message').on('input', search_message);
    $('#send_btn').on('click', sendMessage);
    getSender();
    setInterval(function () {
        getLatestMessageInChat();
    }, 30000);
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
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
}

function getMessages() {
    if (getMessageAjax) {
        getMessageAjax.abort();
    }
    if (updateChatListAjax.length > 0) {
        updateChatListAjax.forEach(ajax => {
            ajax.abort();
        });
        updateChatListAjax = [];
    }
    const chat_id = $(this).attr("id");
    const skeletonHtml = `
        <li class="not_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
        <li class="is_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
        <li class="not_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>
        <li class="is_me"><span class="skel_img"></span><span class="message_text skel_text"></span></li>`;
    $('#chat-message>ul').html(skeletonHtml);
    getMessageAjax = $.ajax({
        url: getMessageChatRoute.replace(":chat_id", chat_id),
        type: "GET",
        success: function (response) {
            if (response.success && response.messages.length > 0) {
                $('#' + chat_id + ' .unread_count').remove();
                const messagesHtml = response.messages.map(message => {
                    const isSenderClass = message.is_sender == 0 ? 'not_me' : 'is_me';
                    let messageContent;
                    if (message.deleted == 0) {
                        const text = message.text ? message.text.replace(/\n/g, '<br>') : '';
                        messageContent = `<span class="message_text">${text}</span>`;
                    } else {
                        messageContent = `
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
                    return `<li class="${isSenderClass}" id="${message.id}"><span class="skel_img"></span>${messageContent}</li>`;
                }).join('');
                $('#chat-message>ul').html(messagesHtml);
                $('#message_cursor').val(response.cursor || '');
                getReceiver(chat_id);
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
        complete: function () {
            getChatData();
            getMessageAjax = null;
            $('#chat-message').attr('data-chat', chat_id);
            $('#chat-message').animate({ scrollTop: $('#chat-message')[0].scrollHeight }, 'slow');
        },
    });
}

function getChatData() {
    var chats = $('.chat-tab');
    chats.each(function () {
        var chat = $(this);
        if (chat.find('.skel_chat_img').length > 0 && chat.find('.skel_chat_name').length > 0) {
            updateChatListAjax.push(getProfileMessage(chat));
        }
        if (chat.find('.skel_latest_message').length > 0 && chat.find('.skel_latest_message_timestamp').length > 0) {
            updateChatListAjax.push(getLatestMessage(chat));
        }
    });
    $('.chat-tab').on('click', getMessages);
}

function getProfileMessage(chat) {
    const profileId = chat.data('profile');
    const ajaxRequest = $.ajax({
        url: getChatProfile.replace(":profile_id", profileId),
        type: "GET",
        success: function (response) {
            if (response.success) {
                const profile = response.user_profile;
                chat.find('.chat_name').text(`${profile.first_name} ${profile.last_name}`).removeClass('skel_chat_name');
                const imgTag = $('<img>').attr('src', profile.profile_picture_url);
                chat.find('.chat_image').replaceWith(imgTag);
                receivers[chat.prop('id')] = profile;
            } else {
                chat.remove();
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
        },
    });
    return ajaxRequest;
}

function getLatestMessage(chat) {
    const chat_id = chat.prop('id');
    const ajaxRequest = $.ajax({
        url: getLatestMessageRoute.replace(":chat_id", chat_id),
        type: "GET",
        success: function (response) {
            if (response.success && response.message?.length) {
                const latestMessage = response.message[0];
                if (latestMessage.text) {
                    const trimmedText = latestMessage.text.length > 25 ? `${latestMessage.text.substring(0, 25)}...` : latestMessage.text;
                    chat.find('.latest_message').html(trimmedText).removeClass('skel_latest_message');
                }
                if (latestMessage.timestamp) {
                    const formattedDate = new Date(latestMessage.timestamp).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                    chat.find('.latest_message_timestamp').html(formattedDate).removeClass('skel_latest_message_timestamp');
                }
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
            chat.removeClass('skel-chat');
        }
    });
    return ajaxRequest;
}

function getReceiver(chat_id) {
    if (getReceiverAjax) {
        getReceiverAjax.abort();
    }
    const updateMessages = (selector, profilePictureUrl) => {
        $(selector).each(function () {
            $(this).find('.skel_img').replaceWith($('<img>').attr('src', profilePictureUrl));
        });
    };
    const updateConversationInfo = (receiver) => {
        const infoContainer = $('.conversation_info .info');
        infoContainer.find('.skel_img').attr('src', receiver.profile_picture_url).removeClass('skel_img');
        const connectionDegrees = {
            'FIRST_DEGREE': '1st',
            'SECOND_DEGREE': '2nd',
            'THIRD_DEGREE': '3rd'
        };
        const connection = connectionDegrees[receiver.network_distance] || '';
        const fullName = `${receiver.first_name} ${receiver.last_name}<u>.</u>${connection}`;
        infoContainer.find('.skel_head').html(fullName).removeClass('skel_head');
        infoContainer.find('.skel_user_name').html(receiver.headline).removeClass('skel_user_name');
        const userEmailContainer = infoContainer.find('.skel_user_email');
        if (receiver.contact_info?.emails?.length) {
            const email = receiver.contact_info.emails[0];
            userEmailContainer.html(`<a href="mailto:${email}">${email}</a>`).removeClass('skel_user_email');
        } else {
            userEmailContainer.removeClass('skel_user_email');
        }
    };
    if (sender) {
        updateMessages('.is_me', sender.profile_picture_url);
    }
    const skeletonHtml = `
            <img class="skel_img" src="" alt="">
            <h6 class="skel_head"></h6>
            <span class="user_name skel_user_name"></span>
            <span class="user_email skel_user_email"></span>
            <div class="note skel_text"></div>`;
    $('.conversation_info>.info').html(skeletonHtml);
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

function updateChatListLoader(e) {
    const search = $('#search_message').val().trim();
    if (search || isLoading) {
        e.preventDefault();
        return;
    }
    const $this = $(this);
    const contentHeight = $this.prop('scrollHeight');
    const visibleHeight = $this.height();
    const scrollTop = $this.scrollTop();
    if (scrollTop + visibleHeight >= contentHeight - 1) {
        $('#chat-loader').show();
        updateChatList();
    }
}

function updateChatList() {
    const cursor = $('#chat_cursor').val();
    const $chatList = $('.chat-list');
    const $chatLoader = $('#chat-loader');
    let html = '';
    isLoading = true;
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
            if (response.success) {
                if (response.chats.length > 0) {
                    html = response.chats.reduce((acc, chat) => {
                        if (chat.folder.includes('INBOX_LINKEDIN_CLASSIC') && chat.archived === 0) {
                            acc += `
                                <li class="d-flex chat-tab skel-chat" id="${chat.id}" data-profile="${chat.attendee_provider_id}">
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
                        $('#chat_cursor').val(response.cursor);
                    } else {
                        $('#chat_cursor').val('');
                    }
                    getChatData();
                } else {
                    $chatLoader.hide();
                }
            } else {
                $chatLoader.hide();
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
            $chatLoader.hide();
        },
        complete: function () {
            $chatLoader.hide();
            isLoading = false;
        }
    });
    updateChatListAjax.push(ajaxRequest);
}

function updateMessageLoader(e) {
    e.preventDefault();
    if (isMessageLoading) return;
    const cursor = $('#message_cursor').val();
    const chat_id = $('#chat-message').data('chat');
    if (cursor !== '' && $(this).scrollTop() <= 2) {
        isMessageLoading = true;
        $('#message-loader').show();
        $.ajax({
            url: getMessageChatCursorRoute.replace(":chat_id", chat_id).replace(":cursor", cursor),
            type: "GET",
            success: function (response) {
                if (response.success && response.messages.length > 0) {
                    const messagesHtml = response.messages.map(message => {
                        const isSenderClass = message.is_sender === 0 ? 'not_me' : 'is_me';
                        const messageText = message.deleted === 0
                            ? (message.text ? message.text.replace(/\n/g, '<br>') : '')
                            : 'This message has been deleted.';
                        const messageStyle = message.deleted === 0 ? '' : `
                            padding: 2px 10px;
                            height: fit-content;
                            background-color: #f4f2ee;
                            color: #000;
                            border: 1px solid #343434;
                            box-shadow: inset 4px 4px 4px #8c8c8c, inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;
                        `;
                        return `
                            <li class="${isSenderClass}" id="${message.id}">
                                <span class="skel_img"></span>
                                <span class="message_text" style="${messageStyle}">
                                    ${messageText}
                                </span>
                            </li>`;
                    }).join('');
                    $('#chat-message>ul').prepend(messagesHtml);
                    $('#message_cursor').val(response.cursor || '');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
            complete: function () {
                $('#message-loader').hide();
                isMessageLoading = false;
                if (receiver !== null) {
                    $('.is_me .skel_img').each(function () {
                        $(this).replaceWith($('<img>').attr('src', sender.profile_picture_url)).removeClass('skel_img');
                    });
                    $('.not_me .skel_img').each(function () {
                        $(this).replaceWith($('<img>').attr('src', receiver.profile_picture_url)).removeClass('skel_img');
                    });
                } else {
                    getReceiver(chat_id);
                }
                $('#chat-message').animate({ scrollTop: $('#chat-message')[0].scrollHeight }, 'slow');
            }
        });
    } else {
        isMessageLoading = false;
    }
}

function sendMessage(e) {
    e.preventDefault();
    const message = $('#sendMessage').val().trim();
    if (message === '') return;
    const formData = new FormData();
    formData.append('message', message);
    formData.append('chat_id', $('#chat-message').data('chat'));
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url: sendMessageRoute,
        data: formData,
        type: "POST",
        processData: false,
        contentType: false,
        headers: { "X-CSRF-TOKEN": csrfToken },
        success: function (response) {
            if (response.success && response.message) {
                const messageData = response.message;
                const isSender = messageData.is_sender === 0 ? 'not_me' : 'is_me';
                const deletedStyle = messageData.deleted === 0 ? '' : `
                    padding: 2px 10px;
                    height: fit-content;
                    background-color: #f4f2ee;
                    color: #000;
                    border: 1px solid #343434;
                    box-shadow: inset 4px 4px 4px #8c8c8c, inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;
                `;
                const messageText = messageData.deleted === 0
                    ? messageData.text.replace(/\n/g, '<br>')
                    : 'This message has been deleted.';
                const messageHtml = `
                    <li class="${isSender}" id="${messageData.id}">
                        <span class="skel_img"></span>
                        <span class="message_text" style="${deletedStyle}">
                            ${messageText}
                        </span>
                    </li>
                `;
                $('#' + messageData.chat_id + ' .unread_count').remove();
                $('#chat-message>ul').append(messageHtml);
                if (sender) {
                    $('.is_me .skel_img').each(function () {
                        $(this).replaceWith($('<img>').attr('src', sender.profile_picture_url)).removeClass('skel_img');
                    });
                }
                const latestMessageText = messageData.text.length > 25
                    ? messageData.text.substring(0, 25) + '...'
                    : messageData.text;

                $('#' + messageData.chat_id).find('.latest_message').html(latestMessageText);
            }
            $('#sendMessage').val('');
        },
        error: function (xhr, status, error) {
            console.error('Error sending message:', error);
        }
    });
}

function search_message() {
    if (getChatAjax) {
        getChatAjax.abort();
        getChatAjax = null;
    }
    updateChatListAjax.forEach(chatAjax => chatAjax.abort());
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
                    if (chat.folder.includes('INBOX_LINKEDIN_CLASSIC') && chat.archived === 0) {
                        html += `
                            <li class="d-flex chat-tab skel-chat" id="${chat.id}" data-profile="${chat.attendee_provider_id}">
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
                });
                $('.chat-list').html(html).show();
                getChatData();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error during search:', error);
        },
        complete: function () {
            getChatAjax = null;
            $('#chat-loader').hide();
        }
    });
}

function getLatestMessageInChat() {
    const chatId = $('#chat-message').data('chat');
    const count = $('#chat-message>ul>li').length;
    $.ajax({
        url: getLatestMessageInChatRoute.replace(":chat_id", chatId).replace(':count', count),
        type: "GET",
        success: function (response) {
            if (response.success && response.messages.length > 0) {
                $('#' + chatId + ' .unread_count').remove();
                let html = '';
                const messages = response.messages;
                messages.forEach(message => {
                    const isSender = message.is_sender === 0 ? 'not_me' : 'is_me';
                    const messageText = message.deleted === 0
                        ? (message.text || '').replace(/\n/g, '<br>')
                        : 'This message has been deleted.';
                    html += `
                        <li class="${isSender}" id="${message.id}">
                            <span class="skel_img"></span>
                            <span class="message_text" style="${message.deleted === 1 ? `
                                padding: 2px 10px; height: fit-content;
                                background-color: #f4f2ee; color: #000;
                                border: 1px solid #343434; box-shadow: inset 4px 4px 4px #8c8c8c,
                                inset -4px -4px 4px #8c8c8c, 4px 4px 4px #414141;
                            ` : ''}">
                                ${messageText}
                            </span>
                        </li>
                    `;
                });
                $('#chat-message>ul').html(html);
                const lastMessage = messages[messages.length - 1];
                if (lastMessage && lastMessage.text) {
                    const trimmedText = lastMessage.text.length > 25
                        ? `${lastMessage.text.substring(0, 25)}...`
                        : lastMessage.text;
                    $('#' + chatId).find('.latest_message').html(trimmedText);
                }
                if (sender) {
                    $('.is_me').each(function () {
                        const imgTag = $('<img>').attr('src', sender.profile_picture_url);
                        $(this).find('.skel_img').replaceWith(imgTag).removeClass('skel_img');
                    });
                }
                if (receiver) {
                    $('.not_me').each(function () {
                        const imgTag = $('<img>').attr('src', receiver.profile_picture_url);
                        $(this).find('.skel_img').replaceWith(imgTag).removeClass('skel_img');
                    });
                }
                if (response.cursor) {
                    $('#message_cursor').val(response.cursor);
                } else {
                    $('#message_cursor').val('');
                }
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching latest messages:', error);
        },
    });
}
