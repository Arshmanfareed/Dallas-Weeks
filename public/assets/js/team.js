var customRoleAjax = null;
var inviteMemberAjax = null;
$(document).ready(function () {
    $('.permission').on('click', function () {
        if ($(this).prop('checked')) {
            $(this).parent().siblings('div').css('display', 'flex');
        } else {
            $(this).parent().siblings('div').css('display', 'none');
        }
    });
    $('#invite_email').on('input', inviteEmail);
    $('.roles').on('click', getRole);
    $('.step_form').on('submit', custom_role);
    $('.invite_form').on('submit', invite_member);
});

$(document).on('click', '.seats', function (e) {
    var checkbox = $(this).siblings('input');
    checkbox.prop('checked', !checkbox.prop('checked'));
});

function inviteEmail(e) {
    var email = $(this).val();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailPattern.test(email)) {
        $('.invite_modal_row .edit_able').removeClass('disabled');
    } else {
        $('.invite_modal_row .edit_able').addClass('disabled');
    }
}

function custom_role(e) {
    e.preventDefault();
    if (!customRoleAjax) {
        let formData = new FormData(e.target);
        customRoleAjax = $.ajax({
            url: customRoleRoute,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function (xhr, error, status) {
                console.error(error);
            },
            complete: function () {
                customRoleAjax = null;
            }
        });
    }
}

function getRole(e) {
    $('.roles').siblings('input').prop('checked', false);
    $(this).siblings('input').prop('checked', true);
    $('.seat-management').remove();
    var html = `<div class="seat-management" style="padding: 17px;">`;
    html += `<p>Select seats to manage. The selected seats will appear at the top:</p>`;
    if (seats.length > 0) {
        seats.forEach(function (seat, index) {
            html += `<div style="margin-bottom: 17px;">`;
            html += `<input value="${seat.id}" name="seats[]" type="checkbox">`;
            html += `<label class="seats" for="${seat.id}"> Seat ${seat.name || seat.id}</label>`
            html += `</div>`;
        });
    } else {
        html += `<p>You don't have any seats to manage. To continue, uncheck the role or add new seats.</p>`;
        $('.manage_member').addClass('disabled');
    }
    html += `</div>`;
    $(this).parent().append(html);
}

function invite_member(e) {
    e.preventDefault();
    if (!inviteMemberAjax) {
        let formData = new FormData(e.target);
        inviteMemberAjax = $.ajax({
            url: teamMemeberRoute,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function (xhr, error, status) {
                let errors = xhr.responseJSON.errors;
                let firstError = errors[Object.keys(errors)[0]][0];
                $('.model_alert').html(`
                    ${firstError}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `).show();
                $('.model_alert').prepand()
                console.error(error);
            },
            complete: function () {
                inviteMemberAjax = null;
            }
        });
    }
}