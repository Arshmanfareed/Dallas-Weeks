$(document).ready(function () {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    $('.tag_input_wrapper_input').on('input', inputWrapper);
    $('.global_blacklist_type').on('click', function () {
        $('.global_blacklist_type').siblings('input').prop('checked', false);
        let comparisonType = $('.global_comparison_type').parent().parent();
        if ($(this).siblings('input').val() == 'profile_url') {
            comparisonType.each(function (index, element) {
                let $element = $(element);
                if ($element.find('input').val() !== 'exact') {
                    $element.find('input').prop('checked', false);
                    $element.addClass('disabled');
                } else {
                    $element.find('input').click();
                }
            });
        } else {
            comparisonType.each(function (index, element) {
                let $element = $(element);
                $element.removeClass('disabled');
            });
        }
        $(this).siblings('input').prop('checked', true);
    });
    $('.global_comparison_type').on('click', function () {
        $('.global_comparison_type').siblings('input').prop('checked', false);
        $(this).siblings('input').prop('checked', true);
    });
    $('.email_blacklist_type').on('click', function () {
        $('.email_blacklist_type').siblings('input').prop('checked', false);
        $(this).siblings('input').prop('checked', true);
    });
    $('.email_comparison_type').on('click', function () {
        $('.email_comparison_type').siblings('input').prop('checked', false);
        $(this).siblings('input').prop('checked', true);
    });
    $(document).on('click', '.remove_global_blacklist_item', function () {
        $(this).parent().remove();
    });
    $(document).on('click', '.remove_email_blacklist_item', function () {
        $(this).parent().remove();
    });
    $(document).on('click', '.delete-global-blacklist', deleteGlobalBlacklist);
    $(document).on('click', '.delete-email-blacklist', deleteEmailBlacklist);
});

function deleteGlobalBlacklist() {
    const id = $(this).data('id');

    if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
            url: deleteGlobalBlacklistRoute.replace(':blacklist-id', id),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                toastr.success('Deleted succesfully');
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            },
            error: function (xhr) {
                toastr.error('Something went wrong');
            }
        });
    }
}

function deleteEmailBlacklist() {
    const id = $(this).data('id');

    if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
            url: deleteEmailBlacklistRoute.replace(':blacklist-id', id),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                toastr.success('Deleted succesfully');
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            },
            error: function (xhr) {
                toastr.error('Something went wrong');
            }
        });
    }
}

function inputWrapper(e) {
    let blacklistValue = $(this).val();
    let blacklistItems = blacklistValue.split(';');
    let blacklistDivId = $(this).data('div-id');
    let removeItem;
    let inputItem;

    blacklistItems.forEach((item, index) => {
        let trimmedItem = item.trim();
        if (blacklistDivId == 'global_blacklist_div') {
            removeItem = 'remove_global_blacklist_item';
        } else {
            removeItem = 'remove_email_blacklist_item';
        }
        if (blacklistDivId == 'global_blacklist_div') {
            inputItem = 'global_blacklist_item';
        } else {
            inputItem = 'email_blacklist_item';
        }
        if (trimmedItem !== '' && index < blacklistItems.length - 1) {
            $('#' + blacklistDivId).append(
                `<div class="item"><span>`
                + trimmedItem +
                `</span><span class="` + removeItem + `"><i class="fa-solid fa-xmark"></i></span>
                <input type="hidden" name="` + inputItem + `[]" value="`
                + trimmedItem +
                `"></div>`);
        } else {
            $(this).val(item);
        }
    });
}