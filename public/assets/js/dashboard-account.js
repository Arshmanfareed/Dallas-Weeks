$(document).ready(function () {
    // $(".setting_btn").on("click", setting_list);
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

    // function setting_list(e) {
    // }
});
