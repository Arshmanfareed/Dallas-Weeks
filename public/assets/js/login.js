var credentialAjax = null;
$(document).ready(function () {
    $('#password').on('input', credential_check);
    $('#email').on('input', credential_check);
});

function credential_check() {
    if (credentialAjax) {
        credentialAjax.abort();
        credentialAjax = null;
    }
    var email = $('#email').val();
    var password = $('#password').val();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    credentialAjax = $.ajax({
        url: checkCredentialsRoute,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            'email': email,
            'password': password
        },
        beforeSend: function () {
            $('#passwordError').html('');
            $('#successMessage').html('');
        },
        success: function (response, textStatus, xhr) {
            if (response.success) {
                $('#successMessage').html(response.message);
                $('.btn_div').html(`
                    <a href="${response.dashboardUrl}" class="theme_btn login_btn">Login</a>
                `);
            } else {
                $('#passwordError').html(response.error);
                $('.btn_div').html(``);
            }
        },
        error: function (xhr, textStatus, error) {
            $('#passwordError').html('An unexpected error occurred.');
            $('#successMessage').html('');
            $('.btn_div').html(``);
        },
    });
}