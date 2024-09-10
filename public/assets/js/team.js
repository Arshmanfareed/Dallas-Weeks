var customRoleAjax = null;
$(document).ready(function () {
    $('.permission').on('click', function () {
        if ($(this).prop('checked')) {
            $(this).parent().siblings('div').css('display', 'flex');
        } else {
            $(this).parent().siblings('div').css('display', 'none');
        }
    });
    $('.roles').on('click', getSeat);
    $('.step_form').on('submit', custom_role);
});

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