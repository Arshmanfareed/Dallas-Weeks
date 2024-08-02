$(document).ready(function () {
    /* Action toggle on Campaign list */
    sessionStorage.removeItem("campaign_details");
    sessionStorage.removeItem("settings");
    sessionStorage.removeItem("elements_array");
    sessionStorage.removeItem("elements_data_array");

    $(".setting_btn").on("click", setting_list);
    $("#filterSelect").on("change", filter_search);
    $("#search_campaign").on("input", filter_search);

    $(document).on("change", ".switch", function (e) {
        var campaign_id = $(this).attr("id").replace("switch", "");
        $("#loader").show();
        $.ajax({
            url: activateCampaignRoute.replace(":campaign_id", campaign_id),
            type: "GET",
            success: function (response) {
                if (response.success && response.active == 1) {
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
                        timeOut: "3000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                    };
                    toastr.success("Campaign successfully Activated");
                } else {
                    toastr.info("Campaign successfully Deactivated");
                }
                if ($("#filterSelect").val() != "archive") {
                    $("#table_row_" + campaign_id).remove();
                }
                if ($(".campaign_table_row").length <= 0) {
                    html = "";
                    html += '<tr><td colspan="8">';
                    html +=
                        '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                    html += "</td></tr>";
                    $("#campaign_table_body").html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    });

    $(document).on("click", ".delete_campaign", function (e) {
        if (confirm("Are you sure to delete this campaign?")) {
            var campaign_id = $(this).attr("id").replace("delete", "");
            $("#loader").show();
            $.ajax({
                url: deleteCampaignRoute.replace(":id", campaign_id),
                type: "GET",
                success: function (response) {
                    if (response.success) {
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
                            timeOut: "3000",
                            extendedTimeOut: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                        };
                        toastr.success("Campaign successfully Deleted");
                    } else {
                        toastr.error("Campaign cannot be Deleted");
                    }
                    $("#table_row_" + campaign_id).remove();
                    if ($(".campaign_table_row").length == 0) {
                        html = "";
                        html += '<tr><td colspan="8">';
                        html +=
                            '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                        html += "</td></tr>";
                        $("#campaign_table_body").html(html);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                },
                complete: function () {
                    $("#loader").hide();
                }
            });
        }
    });

    $(document).on("click", ".archive_campaign", function (e) {
        if (confirm("Are you sure to archive this campaign?")) {
            var campaign_id = $(this).attr("id").replace("archive", "");
            $("#loader").show();
            $.ajax({
                url: archiveCampaignRoute.replace(":id", campaign_id),
                type: "GET",
                success: function (response) {
                    if (response.success && response.archive == 1) {
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
                            timeOut: "3000",
                            extendedTimeOut: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                        };
                        toastr.success("Campaign successfully Archived");
                    } else {
                        toastr.info("Campaign successfully Archived");
                    }
                    $("#table_row_" + campaign_id).remove();
                    if ($(".campaign_table_row").length == 0) {
                        html = "";
                        html += '<tr><td colspan="8">';
                        html +=
                            '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                        html += "</td></tr>";
                        $("#campaign_table_body").html(html);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                },
                complete: function () {
                    $("#loader").hide();
                }
            });
        }
    });

    $(document).on("click", "#filterToggle", function (e) {
        e.preventDefault();
        $("#filterSelect").toggle();
    });

    function filter_search(e) {
        e.preventDefault();
        var filter = $("#filterSelect").val();
        var search = $("#search_campaign").val();
        if (search === "") {
            search = "null";
        }
        $("#loader").show();
        $.ajax({
            url: filterCampaignRoute
                .replace(":filter", filter)
                .replace(":search", search),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    var campaigns = response.campaigns;
                    var html = ``;
                    if (campaigns.length > 0) {
                        for (let i = 0; i < campaigns.length; i++) {
                            let campaign = campaigns[i];
                            html +=
                                `<tr id="` +
                                "table_row_" +
                                campaign["id"] +
                                `" class="campaign_table_row"><td><div class="switch_box">`;
                            if (campaign["is_active"] == 1) {
                                html +=
                                    `<input type="checkbox" class="switch" id="switch` +
                                    campaign["id"] +
                                    `" checked>`;
                            } else {
                                html +=
                                    `<input type="checkbox" class="switch" id="switch` +
                                    campaign["id"] +
                                    `">`;
                            }
                            html +=
                                `<label for="switch` +
                                campaign["id"] +
                                `">Toggle</label></div></td>`;
                            html +=
                                `<td>` + campaign["campaign_name"] + `</td>`;
                            html += `<td id="lead_count_${campaign["id"]}">` + campaign["lead_count"] + `</td>`;
                            html += `<td id="sent_message_count_${campaign["id"]}">` + campaign['message_count'] + `</td>`;
                            html += `<td class="stats"><ul class="status_list d-flex align-items-center list-unstyled p-0 m-0">`;
                            html += `<li><span><img src="/assets/img/eye.svg" alt=""><span id="view_profile_count_${campaign["id"]}">` + campaign['view_action_count'] + `</span></span></li>`;
                            html += `<li><span><img src="/assets/img/request.svg" alt=""><span id="invite_to_connect_count_${campaign["id"]}">` + campaign['invite_action_count'] + `</span></span></li>`;
                            html += `<li><span><img src="/assets/img/mailmsg.svg" alt=""><span id="email_message_count_${campaign['id']}">` + campaign['email_action_count'] + `</span></span></li>`;
                            // html += `<li><span><img src="/assets/img/mailopen.svg" alt="">16</span></li></ul></td>`;
                            html += `<td><div class="per up">34%</div></td>`;
                            html += `<td><div class="per down">23%</div></td>`;
                            html += `<td><a type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>`;
                            html += `<ul class="setting_list" style="display: none;">`;
                            html +=
                                `<li><a href="/campaign/campaignDetails/` +
                                campaign["id"] +
                                `">Check campaign details</a></li>`;
                            html +=
                                `<li><a href="/campaign/editcampaign/` +
                                campaign["id"] +
                                `">Edit campaign</a></li>`;
                            // html += '<li><a href="#">Duplicate campaign steps</a></li>';
                            // html += '<li><a href="javascript:;" data-bs-toggle="modal" data-bs-target="#add_new_leads_modal">Add new leads</a></li>';
                            // html += '<li><a href="#">Export data</a></li>';
                            html +=
                                `<li><a class="archive_campaign" id="archive` +
                                campaign["id"] +
                                `">Archive campaign</a></li>`;
                            html +=
                                `<li><a class="delete_campaign" id="delete` +
                                campaign["id"] +
                                `">Delete campaign</a></li>`;
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
                if ($("#filterSelect").val() == "archive") {
                    $(".archive_campaign").html("Remove From Archive");
                } else {
                    $(".archive_campaign").html("Archive campaign");
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
            }
        });
    }

    function setting_list(e) {
        $(".setting_list").hide();
        $(".setting_btn").on("click", function (e) {
            $(".setting_list").not($(this).siblings(".setting_list")).hide();
            $(this).siblings(".setting_list").toggle();
        });
        $(document).on("click", function (e) {
            if (!$(e.target).closest(".setting").length) {
                $(".setting_list").hide();
            }
        });
    }
});
