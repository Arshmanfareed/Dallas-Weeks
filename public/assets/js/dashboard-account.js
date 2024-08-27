var searchAjax = null;

$(document).ready(function () {
    $(".setting_btn").on("click", setting_list);
    $("#search_seat").on("input", filter_search);
    $('.update_seat_name').on('click', update_seat_name);
    $('.btn-prev').on('click', btn_prev);
    $('.btn-next').on('click', btn_next);
    $('#payment-form').on('submit', paymentForm);
    $('.delete_seat').on('click', deleteSeat);
    $(".seat_table_data").on("click", toSeat);
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
    var id = $(this).attr('id').replace('delete_seat_', '');
    $.ajax({
        url: deleteSeatRoute.replace(':seat_id', id),
        type: "GET",
        success: function (response) {
            if (response.success) {
                $('#update_seat').modal('hide');
                $('#table_row_' + response.seat).remove();
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}

//Need to be fixed
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
                var username = seat.username.charAt(0).toUpperCase() + seat.username.slice(1);
                $('#seat_input_name').val(username);
                $('#seat_name').html(username);
                $('.delete_seat').attr('id', 'delete_seat_' + seat.id);
                $('.update_seat_name').attr('id', 'update_seat_name_' + seat.id);
                $('#update_seat').modal('show');
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}

function update_seat_name(e) {
    var id = $(this).attr('id').replace('update_seat_name_', '');
    var name = $('#seat_input_name').val();
    $.ajax({
        url: updateNameRoute.replace(':seat_id', id).replace(':seat_name', name),
        type: "GET",
        success: function (response) {
            if (response.success) {
                $('#update_seat').modal('hide');
                $('#table_row_' + id).find('.text-left').html(response.seat.username);
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
}
