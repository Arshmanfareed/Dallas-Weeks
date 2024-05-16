$(document).ready(function () {
    localStorage.removeItem("settings");
    
    $(document).on("change", "#campaign_url", function (e) {
        var file = e.target.files[0];
        if (file) {
            $(".import_field").find("label").remove();
            $(".import_field").append(
                '<label style="margin-bottom: 0px">' + file.name + "</label>"
            );
        } else {
            $(".import_field").find("label").remove();
            html = "";
            html +=
                '<label class="file-input__label" for="file-input"><svg aria-hidden="true"';
            html += 'focusable="false" data-prefix="fas" data-icon="upload"';
            html += 'class="svg-inline--fa fa-upload fa-w-16"';
            html += 'role="img" xmlns="http://www.w3.org/2000/svg"';
            html += 'viewBox="0 0 512 512"> <path fill="currentColor"';
            html +=
                'd="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z">';
            html += "</path></svg><span>Upload file</span></label>";
            $(".import_field").append(html);
        }
    });
    if (campaign_details["campaign_type"] == undefined) {
        campaign_details["campaign_type"] = "linkedin";
    }
    var campaign_pane = $(".campaign_pane");
    for (var i = 0; i < campaign_pane.length; i++) {
        var campaignType = $(campaign_pane[i]).find("#campaign_type").val();
        if (campaignType == campaign_details["campaign_type"]) {
            $(campaign_pane[i]).addClass("active");
            $(
                '[data-bs-target="' + $(campaign_pane[i]).attr("id") + '"]'
            ).addClass("active");
        }
    }
    if (
        campaign_details["campaign_name"] == undefined ||
        campaign_details["campaign_url"] == undefined ||
        campaign_details["connections"] == undefined
    ) {
        campaign_details["campaign_name"] = "";
        campaign_details["campaign_url"] = "";
        campaign_details["connections"] = "1";
    } else {
        var active_form = $(".campaign_pane.active").find("form");
        active_form
            .find("#campaign_name")
            .val(campaign_details["campaign_name"]);
        if (active_form.attr("id") != "campaign_form_4") {
            active_form
                .find("#campaign_url")
                .val(campaign_details["campaign_url"]);
        }
        if (
            active_form.attr("id") != "campaign_form_4" &&
            active_form.attr("id") != "campaign_form_3"
        ) {
            active_form
                .find("#connections")
                .val(campaign_details["connections"]);
        }
    }
    $(".campaign_name").on("change", function (e) {
        campaign_details["campaign_name"] = $(this).val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
    });
    $(".campaign_url").on("change", function (e) {
        campaign_details["campaign_url"] = $(this).val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
    });
    $(".connections").on("change", function (e) {
        campaign_details["connections"] = $(this).val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
    });
    $(".campaign_tab").on("click", function (e) {
        e.preventDefault();
        $(".campaign_tab").removeClass("active");
        $(this).addClass("active");
        var id = $(this).data("bs-target");
        $(".campaign_pane").removeClass("active");
        $("#" + id).addClass("active");
        var new_form = $("#" + id).find("form");
        campaign_details["campaign_type"] = new_form
            .find("#campaign_type")
            .val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
        new_form.find("#campaign_name").val(campaign_details["campaign_name"]);
        if (new_form.attr("id") != "campaign_form_4") {
            new_form
                .find("#campaign_url")
                .val(campaign_details["campaign_url"]);
        }
        if (
            new_form.attr("id") != "campaign_form_4" &&
            new_form.attr("id") != "campaign_form_3"
        ) {
            new_form.find("#connections").val(campaign_details["connections"]);
        }
    });
    $(".nxt_btn").on("click", function (e) {
        e.preventDefault();
        var form = $(".campaign_pane.active").find("form");
        if (form.attr("id") == "campaign_form_4") {
            var fileInput = form.find("#campaign_url")[0].files[0];
            var formData = new FormData();
            formData.append("campaign_url", fileInput);
            var csrfToken = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url: importCSVPath,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
                },
                success: function (response) {
                    if (response.success) {
                        $("#sequance_modal")
                            .find("ul li #total_leads")
                            .text(response.total_leads + " leads");
                        $("#sequance_modal")
                            .find("ul li #blacklist_leads")
                            .text(response.blacklist_leads + " leads");
                        // $('#sequance_modal').find('ul li #duplicate_among_teams').text(response.duplicate_among_teams + ' leads');
                        // $('#sequance_modal').find('ul li #duplicate_csv_file').text(response.duplicate_csv_file + ' leads');
                        // $('#sequance_modal').find('ul li #total_without_leads').text(response.total_without_leads + ' leads');
                        $("#sequance_modal").modal("show");
                    } else {
                        console.log(response);
                    }
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 422) {
                        var response = JSON.parse(xhr.responseText);
                        var errorMessage = response.errors.campaign_url[0];
                        form.find("span.campaign_url").text(errorMessage);
                        form.find(".import_field").css({
                            border: "1px solid red",
                            "margin-bottom": "7px !important",
                        });
                        form.find(".file-input__label").css({
                            "background-color": "red",
                        });
                    } else {
                        console.error("Upload failed:", error);
                    }
                },
            });
        } else {
            form.submit();
        }
    });
});
