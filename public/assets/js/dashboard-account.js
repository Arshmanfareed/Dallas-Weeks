var searchAjax = null;
var deleteAjax = null;

$(document).ready(function () {
    $(document).on('click', '.setting_btn', setting_list);
    $("#search_seat").on("input", filter_search);
    $(document).on('click', '.update_seat_name', update_seat_name);
    $('.btn-prev').on('click', btn_prev);
    $('.btn-next').on('click', btn_next);
    $('#payment-form').on('submit', paymentForm);
    $(document).on('click', '.delete_seat', deleteSeat);
    $(document).on('click', '.seat_table_data', toSeat);
});

function toSeat(e) {
    var id = $(this).parent().attr("id").replace("table_row_", "");
    var form = $("<form>", {
        method: "POST",
        action: dashboardRoute
    });
    form.append(
        $("<input>", {
            type: "hidden",
            name: "_token",
            value: $('meta[name="csrf-token"]').attr("content")
        }),
        $("<input>", {
            type: "hidden",
            name: "seat_id",
            value: id
        })
    );
    form.appendTo("body").submit();
}

function deleteSeat(e) {
    e.preventDefault();
    var id = $(this).attr('id').replace('delete_seat_', '');

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

    if (!deleteAjax) {
        deleteAjax = $.ajax({
            url: deleteSeatRoute.replace(':seat_id', id),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    $('#update_seat').modal('hide');
                    $('#table_row_' + response.seat).remove();
                    if ($('.seat_table_row').length == 0) {
                        $('#campaign_table_body').html(
                            `<tr>
                                <td colspan="8">
                                    <div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">
                                        Not Found!
                                    </div>
                                </td>
                            </tr>`
                        );
                    }
                    toastr.options = toastrOptions;
                    toastr.success('Seat deleted successfully.');
                }
            },
            error: function (xhr, status, error) {
                toastr.options = toastrOptions;
                toastr.error(xhr.responseJSON.errors);
            },
            complete: function () {
                deleteAjax = null;
            }
        });
        deleteAjax = null;
    }
}

function paymentForm(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        type: 'POST',
        url: "{{ route('stripe.post') }}",
        data: formData,
        success: function (response) {
            console.log(response);
        },
        error: function (error) {
            console.log(error);
        },
    });
}

function changeStep(isNext) {
    const $progressStep = $('.progress-step.active');
    const $formStep = $('.form-step.active');
    const $targetProgressStep = isNext ? $progressStep.next('.progress-step') : $progressStep.prev('.progress-step');
    const $targetFormStep = isNext ? $formStep.next('.form-step') : $formStep.prev('.form-step');
    if ($targetProgressStep.length && $targetFormStep.length) {
        $progressStep.removeClass('active');
        $formStep.removeClass('active');
        $targetProgressStep.addClass('active');
        $targetFormStep.addClass('active');
        const activeIndex = $('.progress-step').index($targetProgressStep) + 1;
        $('#progress').css('width', 25 * activeIndex + '%');
    }
}

function btn_next(e) {
    changeStep(true);
}

function btn_prev(e) {
    changeStep(false);
}

function filter_search(e) {
    e.preventDefault();
    var search = $("#search_seat").val();
    if (search === "") {
        search = "null";
    }
    if (searchAjax) {
        searchAjax.abort();
        searchAjax = null;
    }
    searchAjax = $.ajax({
        url: filterSeatRoute.replace(":search", search),
        type: "GET",
        beforeSend: function () {
            let html = ``;
            for (let index = 0; index < 5; index++) {
                html += `
                    <tr id="table_row_${index}" class="seat_table_row skel_table_row">
                        <td width="10%" class="seat_table_data">
                            <img class="seat_img" src="/assets/img/acc.png" alt="">
                        </td>
                        <td width="50%" class="text-left seat_table_data">
                            <div style="width: 250px; height: 35px; border-radius: 15px;" bis_skin_checked="1"></div>
                        </td>
                        <td width="15%" class="connection_status">
                            <div class="connected"><span></span>Connected</div>
                        </td>
                        <td width="15%" class="activeness_status">
                            <div class="active"><span></span>Active</div>
                        </td>
                        <td width="10%">
                            <a href="javascript:;" type="button" class="setting setting_btn"><i class="fa-solid fa-gear"></i></a>
                        </td>
                    </tr>
                `;
            }
            $("#campaign_table_body").html(html);
        },
        success: function (response) {
            if (response.success) {
                var seats = response.seats;
                const html = seats.map(seat => `
                    <tr id="table_row_${seat['id']}" class="seat_table_row">
                        <td width="10%" class="seat_table_data">
                            <img class="seat_img" 
                                src="${seat['account_profile'] && seat['account_profile']['profile_picture_url'] != ''
                        ? seat['account_profile']['profile_picture_url'] : '/assets/img/acc.png'}" alt="">
                        </td>
                        <td width="50%" class="text-left seat_table_data">${seat['username']}</td>
                        <td width="15%" class="connection_status">
                            ${seat['connected']
                        ? '<div class="connected"><span></span>Connected</div>'
                        : '<div class="disconnected"><span></span>Disconnected</div>'}
                        </td>
                        <td width="15%" class="activeness_status">
                            ${seat['active']
                        ? '<div class="active"><span></span>Active</div>'
                        : '<div class="not_active"><span></span>In Active</div>'}
                        </td>
                        <td width="10%">
                            <a href="javascript:;" type="button"
                                class="setting setting_btn"><i
                                    class="fa-solid fa-gear"></i></a>
                        </td>
                    </tr>
                `).join('');
                $(".seat_table_data").on("click", toSeat);
                $("#campaign_table_body").html(html);
                $(".setting_btn").on("click", setting_list);
            }
        },
        error: function (xhr, status, error) {
            const html = `
            <tr>
                <td colspan="8">
                    <div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">
                        Not Found!
                    </div>
                </td>
            </tr>`;
            $("#campaign_table_body").html(html);
        },
        complete: function () {
            searchAjax = null;
        }
    });
}

function setting_list(e) {
    var id = $(this).parent().parent().attr("id").replace("table_row_", "");
    $.ajax({
        url: getSeatRoute.replace(':seat_id', id),
        type: "GET",
        success: function (response) {
            if (response.success) {
                var seat = response.seat;
                var html = `<div class="modal-header">
                                <h4 class="text-center">
                                    Your subscription is 
                                    <span id="active_subs">Active</span>
                                </h4>
                                <button type="button" class="close mt-1" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                                </button>
                            </div>`;
                html += `<div class="modal-body text-center">
                            <div class="accordion" id="accordionExample">`;
                if (response.allow_manage_settings) {
                    html += `<div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                        Change seat name
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="form-group">
                                        <label for="seat_name">Seat Name: </label>
                                        <input type="text" id="seat_input_name" name="seat_name">
                                    </div>
                                    <button id="update_seat_name" type="button" class="update_seat_name theme_btn mb-3" style="background-color: #16adcb" ;>Save Changes</button>
                                </div>
                            </div>`;
                    html += `<div class="accordion-item d-none">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                        Change seat time zone
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                </div>
                            </div>`;
                    html += `<div class="accordion-item d-none">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                        Cancel subscription
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                </div>
                            </div>`;
                } else {
                    html += `<div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                        Change seat name
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="form-group">
                                        <label for="seat_name">Seat Name: </label>
                                        <input type="text" id="seat_input_name" name="seat_name" readonly>
                                        <span class="text-danger fw-bold">You cannot update seat name</span>
                                    </div>
                                </div>
                            </div>`;
                    html += `<div class="accordion-item d-none">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                        Change seat time zone
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                </div>
                            </div>`;
                    html += `<div class="accordion-item d-none">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                        Cancel subscription
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                </div>
                            </div>`;
                }

                if (response.allow_delete_seat) {
                    html += `<div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="headingFour">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                    Delete seat </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    Are you sure you want to delete 
                                    <span id="seat_name" style="color: #16adcb; font-weight: 600;"></span> seat?
                                    <button id="delete_seat" type="button" class="theme_btn mb-3 delete_seat">Delete seat</button>
                                </div>
                            </div>`;
                } else {
                    html += `<div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="headingFour">
                                        <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i>
                                    Delete seat </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    You can not delete this 
                                    '<span id="seat_name" style="color: #16adcb; font-weight: 600;"></span>' seat.
                                </div>
                            </div>`;
                }
                html += `</div>
                    </div >`;
                $('#update_seat .modal-content').html(html);
                var username = seat.username.charAt(0).toUpperCase() + seat.username.slice(1);
                $('#seat_input_name').val(username);
                $('#seat_name').html(username);
                $('.delete_seat').attr('id', 'delete_seat_' + seat.id);
                $('.update_seat_name').attr('id', 'update_seat_name_' + seat.id);
                $('#update_seat').modal('show');
            }
        },
        error: function (xhr, status, error) {
            if (status == 404) {
                $('#update_seat').modal('hide');
            }
            console.error(error);
        }
    });
}

function update_seat_name(e) {
    e.preventDefault();
    var id = $(this).attr('id').replace('update_seat_name_', '');
    var name = $('#seat_input_name').val();

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

    if (!name.trim()) {
        toastr.options = toastrOptions;
        toastr.error('Seat name cannot be empty.');
        return;
    }

    $.ajax({
        url: updateNameRoute.replace(':seat_id', id).replace(':seat_name', name),
        type: "GET",
        success: function (response) {
            if (response.success) {
                $('#update_seat').modal('hide');
                $('#table_row_' + id).find('.text-left').html(response.seat.username);
                toastr.options = toastrOptions;
                toastr.success('Seat name updated successfully.');
            }
        },
        error: function (xhr, status, error) {
            toastr.options = toastrOptions;
            toastr.error(xhr.responseJSON.errors);
        }
    });
}
