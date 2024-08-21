$(document).ready(function () {
    sessionStorage.removeItem("settings");
    sessionStorage.removeItem("elements_array");
    sessionStorage.removeItem("elements_data_array");

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
        $(".campaign_tab.active").parent(".border_box").css({
            "background-color": "#16adcb",
        });
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
        $(".campaign_tab.active").parent(".border_box").css({
            "background-color": "#16adcb",
        });
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
            active_form.attr("id") != "campaign_form_3" &&
            active_form.attr("id") != "campaign_form_6"
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
        var active_form = $(".campaign_pane.active").find("form");
        if (active_form.attr("id") != "campaign_form_4") {
            if (active_form.attr("id") == "campaign_form_1") {
                var url = $(".campaign_pane.active")
                    .find("#campaign_url")
                    .val();
                const queryString = url.split('?')[1];
                const params = new URLSearchParams(queryString);
                const query = {};
                params.forEach((value, key) => {
                    try {
                        const parsedValue = JSON.parse(value);
                        if (key === 'keywords' && typeof parsedValue === 'string') {
                            query[key] = encodeURIComponent(parsedValue.trim());
                        } else {
                            query[key] = parsedValue;
                        }
                    } catch (e) {
                        query[key] = encodeURIComponent(value.trim());
                    }
                });
                find_connection_linkedin(query);
            } else if (active_form.attr("id") == "campaign_form_2") {
                var url = $(".campaign_pane.active")
                    .find("#campaign_url")
                    .val();
                const queryString = url.split('?')[1];
                const params = new URLSearchParams(queryString);
                const query = params.get('query');
                find_connection_sales_navigator(query);
            }
            campaign_details["campaign_url"] = $(this).val();
            sessionStorage.setItem(
                "campaign_details",
                JSON.stringify(campaign_details)
            );
        }
    });

    function find_connection_sales_navigator(query) {
        var relation_index = query.search(/RELATIONSHIP/i);
        if (relation_index > 0) {
            var newStr = query.substr(0, relation_index);
            var paran = newStr.lastIndexOf('(');
            var newSubStr = query.substring(paran, query.length);
            var include = newSubStr.search(/INCLUDED/i);
            if (include > 0) {
                var newSubStr = query.substr(paran, include);
                if (newSubStr.includes('1st')) {
                    $(".campaign_pane.active").find("form").find('.connections').val('1');
                } else if (newSubStr.includes('2nd')) {
                    $(".campaign_pane.active").find("form").find('.connections').val('2');
                } else if (newSubStr.includes('3rd')) {
                    $(".campaign_pane.active").find("form").find('.connections').val('3');
                } else {
                    $(".campaign_pane.active").find("form").find('.connections').val('o');
                }
            } else {
                $(".campaign_pane.active").find("form").find('.connections').val('o');
            }
        } else {
            $(".campaign_pane.active").find("form").find('.connections').val('o');
        }
        $(".campaign_pane.active").find("form").find('.connections').prop('disabled', true);
        campaign_details["connections"] = $(".campaign_pane.active").find("form").find('.connections').val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
    }

    function find_connection_linkedin(query) {
        if (!query['network']) {
            $(".campaign_pane.active").find("form").find('.connections').val('o');
        } else {
            const array = query['network'];
            if (array.length > 1) {
                $(".campaign_pane.active").find("form").find('.connections').val('o');
            } else {
                if (array[0] == 'F') {
                    $(".campaign_pane.active").find("form").find('.connections').val('1');
                } else if (array[0] == 'S') {
                    $(".campaign_pane.active").find("form").find('.connections').val('2');
                } else if (array[0] == 'O') {
                    $(".campaign_pane.active").find("form").find('.connections').val('3');
                } else {
                    $(".campaign_pane.active").find("form").find('.connections').val('o');
                }
            }
        }
        $(".campaign_pane.active").find("form").find('.connections').prop('disabled', true);
        campaign_details["connections"] = $(".campaign_pane.active").find("form").find('.connections').val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
    }

    $(".connections").on("change", function (e) {
        campaign_details["connections"] = $(this).val();
        sessionStorage.setItem(
            "campaign_details",
            JSON.stringify(campaign_details)
        );
    });

    $(".campaign_tab").on("click", function (e) {
        e.preventDefault();
        $(".campaign_tab").parent(".border_box").css({
            "background-color": "rgb(17 19 23)",
        });
        $(".campaign_tab").removeClass("active");
        $(this).addClass("active");
        var id = $(this).data("bs-target");
        $(".campaign_pane").removeClass("active");
        $("#" + id).addClass("active");
        $(".campaign_tab.active").parent(".border_box").css({
            "background-color": "#16adcb",
        });
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
            new_form.attr("id") != "campaign_form_3" &&
            new_form.attr("id") != "campaign_form_6"
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
            $("#loader").show();
            $.ajax({
                url: importCSVPath,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: { "X-CSRF-TOKEN": csrfToken },
                success: function (response) {
                    if (response.success) {
                        $("#sequance_modal")
                            .find("ul li #total_leads")
                            .text(response.total + " leads");
                        $("#sequance_modal")
                            .find("ul li #blacklist_leads")
                            .text(response.global_blacklists + " leads");
                        $("#sequance_modal")
                            .find("ul li #duplicate_among_teams")
                            .text(response.duplicates_across_team + " leads");
                        $("#sequance_modal")
                            .find("ul li #duplicate_csv_file")
                            .text(response.duplicates + " leads");
                        $("#sequance_modal")
                            .find("ul li #total_without_leads")
                            .text(
                                response.total_without_duplicate_blacklist +
                                " leads"
                            );
                        $("#campaign_url_hidden").val(response.path);
                        campaign_details["campaign_url"] = response.path;
                        sessionStorage.setItem(
                            "campaign_details",
                            JSON.stringify(campaign_details)
                        );
                        $("#sequance_modal").modal("show");
                    } else {
                        toastr.options = {
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
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    form.find("span.campaign_url").text(error);
                    form.find(".import_field").css({
                        border: "1px solid red",
                        "margin-bottom": "7px !important",
                    });
                    form.find(".file-input__label").css({
                        "background-color": "red",
                    });
                },
                complete: function () {
                    $("#loader").hide();
                }
            });
        } else {
            form.find('.connections').prop('disabled', false);
            form.submit();
        }
    });

    $(".import_btn").on("click", function (e) {
        e.preventDefault();
        var form = $(".campaign_pane.active").find("form");
        form.submit();
    });
});
