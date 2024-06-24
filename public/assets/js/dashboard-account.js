$(document).ready(function () {
    $(".setting_btn").on("click", setting_list);
    $("#search_seat").on("input", filter_search);
    
    $(".seat_table_data").on("click", function (e) {
        var id = $(this).parent().attr("id").replace("table_row_", "");
        var form = document.createElement("form");
        form.method = "POST";
        form.action = dashboardRoute;

        var csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = $('meta[name="csrf-token"]').attr("content");
        form.appendChild(csrfInput);

        var seatInput = document.createElement("input");
        seatInput.type = "hidden";
        seatInput.name = "seat_id";
        seatInput.value = id;
        form.appendChild(seatInput);

        document.body.appendChild(form);
        form.submit();
    });

    function filter_search(e) {
        e.preventDefault();
        var search = $("#search_seat").val();
        if (search === "") {
            search = "null";
        }
        $.ajax({
            url: filterSeatRoute.replace(":search", search),
            type: "GET",
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (response) {
                if (response.success) {
                    var seats = response.seats;
                    var html = ``;
                    if (seats.length > 0) {
                        for (let i = 0; i < seats.length; i++) {
                            seat = seats[i];
                            html += `<tr id="table_row_${seat["id"]}" class="seat_table_row">`;
                            html += `<td class="seat_table_data"><img src="/assets/img/acc.png" alt=""></td>`;
                            html += `<td class="text-left seat_table_data">${seat["username"]}</td>`;
                            html += `<td>`;
                            if (seat['connected']) {
                                html += `<div class="per discovered">Connected</div>`;
                            } else {
                                html += `<div class="per connected_not_replied">Disconnected</div>`;
                            }
                            html += `</td>`;
                            html += `<td><a href="javascript:;" type="button" class="setting setting_btn"><i class="fa-solid fa-gear"></i></a>`;
                            html += `<ul class="setting_list text-left" style="display: none">`;
                            html += `</ul></td></tr>`;
                        }
                    }
                    $("#campaign_table_body").html(html);
                    $(".setting_btn").on("click", setting_list);
                } else {
                    var html = ``;
                    html += '<tr><td colspan="8">';
                    html +=
                        '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                    html += "</td></tr>";
                    $("#campaign_table_body").html(html);
                }
            },
            error: function (xhr, status, error) {
                var html = ``;
                html += '<tr><td colspan="8">';
                html +=
                    '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                html += "</td></tr>";
                $("#campaign_table_body").html(html);
            },
            complete: function () {
                $("#loader").hide();
            },
        });
    }

    function setting_list(e) {
        var id = $(this).parent().parent().attr("id").replace("table_row_", "");
        $.ajax({
            url: getSeatRoute.replace(':seat_id', id),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    var username = response.seat.username;
                    username = username.charAt(0).toUpperCase() + username.slice(1);
                    var id = response.seat.id;
                    $('#seat_input_name').val(username);
                    $('#seat_name').html(username);
                    $('#delete_seat').attr('id', 'delete_seat_' + id);
                    $('#update_seat_name').attr('id', 'update_seat_name_' + id);
                    $('#update_seat').modal('show');
                } else {
                    console.log(repsponse);
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    $('.delete_seat').on('click', function (e) {
        var id = $(this).attr('id').replace('delete_seat_', '');
        $.ajax({
            url: deleteSeatRoute.replace(':seat_id', id),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    console.log(response);
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    $('.update_seat_name').on('click', function (e) {
        var id = $(this).attr('id').replace('update_seat_name_', '');
        var name = $('#seat_input_name').val();
        $.ajax({
            url: updateNameRoute.replace(':seat_id', id).replace(':seat_name', name),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    console.log(response);
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});
