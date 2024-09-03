var updateChatListAjax = [];

$(document).ready(function () {
    $('.switch').prop('disabled', true);
    $('.campaign_list').on('click', function (e) {
        const id = $(this).prop('id').replace('campaign_list_', '');
        if (id) {
            window.location.href = filterCampaignRoute.replace(':campaign_id', id);
        }
    });
    getChatData();
});

$(window).on('beforeunload', function () {
    if (updateChatListAjax.length > 0) {
        const ajaxRequests = [...updateChatListAjax];
        ajaxRequests.forEach((ajax, index) => {
            if (ajax && ajax.readyState !== 4) {
                ajax.abort();
            }
        });
        updateChatListAjax = [];
    }
});

function getChatData() {
    const $chats = $('.chat-tab');
    $chats.each(function () {
        const $chat = $(this);
        updateChatListAjax.push(getProfileAndLatestMessage($chat));
    });
}

function getProfileAndLatestMessage($chat) {
    const profileId = $chat.attr('data-profile');
    const chatId = $chat.prop('id');
    const ajaxRequest = $.ajax({
        url: getProfileAndLatestMessageRoute.replace(":profile_id", profileId).replace(":chat_id", chatId),
        type: "GET",
        success: function (response) {
            if (response.success) {
                const profile = response.user_profile;
                $chat.find('.skel_profile a').eq(0).attr('href', 'https://www.linkedin.com/in/' + profile.provider_id);
                const $chatName = $chat.find('.skel_profile .skel_profile_name');
                const $chatImage = $chat.find('.skel_profile .skel_profile_img');
                const fullName = `${profile.first_name} ${profile.last_name}`;
                const trimmedName = fullName.length > 20 ? fullName.substring(0, 20) + '...' : fullName;
                $chatName.text(trimmedName).removeClass('skel_profile_name');
                if (profile.profile_picture_url) {
                    $chatImage.replaceWith($('<img>').attr('src', profile.profile_picture_url));
                }
                $chatImage.removeClass('skel_profile_img');
                if (response.message?.length) {
                    const $latestMessage = $chat.find('.skel_message');
                    const latestMessage = response.message[0];
                    if (latestMessage.text) {
                        const trimmedText = latestMessage.text.length > 35 ? `${latestMessage.text.substring(0, 35)}...` : latestMessage.text;
                        $latestMessage.html(trimmedText);
                    } else if (latestMessage.attachments?.length) {
                        const attachment = latestMessage.attachments[0];
                        $latestMessage.html(attachment.file_name);
                    } else {
                        $latestMessage.html('');
                    }
                    $latestMessage.removeClass('skel_message');
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