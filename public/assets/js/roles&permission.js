var customRoleAjax = null;
var deleteRoleAjax = null;
$(document).ready(function () {
    $('.permission').on('click', function () {
        if ($(this).prop('checked')) {
            $(this).parent().siblings('div').css('display', 'flex');
        } else {
            $(this).parent().siblings('div').css('display', 'none');
        }
    });
    $('.step_form').on('submit', custom_role);
    $('.delete_role').on('click', delete_role);
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

function delete_role(e) {
    e.preventDefault();
    var id = $(this).parent().attr('id').replace('table_row_', '');

    var toastrOptions = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

    if (!deleteRoleAjax) {
        deleteRoleAjax = $.ajax({
            url: deleteRoleRoute.replace(':id', id),
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    toastr.options = toastrOptions;
                    toastr.success('Role deleted successfully.');
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function (xhr, status, error) {
                toastr.options = toastrOptions;
                toastr.error(xhr.responseJSON.errors);
            },
            complete: function () {
                deleteRoleAjax = null;
            }
        });
    }
}