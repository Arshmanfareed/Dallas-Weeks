var customRoleAjax = null;
var deleteRoleAjax = null;
var editRoleAjax = null;

$(document).on('submit', '.step_form', custom_role);
$(document).on('click', '.delete_role', delete_role);
$(document).on('click', '.edit_role', edit_role);
$(document).on('click', '.permission', function () {
    let displayStyle = $(this).prop('checked') ? 'flex' : 'none';
    $(this).parent().siblings('div').css('display', displayStyle);
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
                console.log(xhr.responseJSON);
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error(xhr.responseJSON.error);
                } else {
                    toastr.error('An unexpected error occurred.');
                }
            },
            complete: function () {
                deleteRoleAjax = null;
            }
        });
    }
}

function edit_role(e) {
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

    if (!editRoleAjax) {
        editRoleAjax = $.ajax({
            url: getRoleRoute.replace(':id', id),
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    let permissionMap = {};
                    response.permissions_to_roles.forEach(permit => {
                        permissionMap[permit.permission_id] = permit.access;
                    });
                    $('#edit_role').find('#role_name').val(response.role.role_name);
                    response.permissions.forEach(permission => {
                        let target = $('#edit_role').find('#permission_' + permission.permission_slug);
                        let access = permissionMap[permission.id] || 0;
                        if (access == 1) {
                            target.prop('checked', true);
                            target.siblings('.permission').trigger('click');
                        } else {
                            target.prop('checked', false);
                        }
                    });
                    $('#edit_role').modal('show');
                }
            },
            error: function (xhr, status, error) {
                toastr.options = toastrOptions;
                console.log(xhr.responseJSON);
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error(xhr.responseJSON.error);
                } else {
                    toastr.error('An unexpected error occurred.');
                }
            },
            complete: function () {
                editRoleAjax = null;
            }
        });
    }
}