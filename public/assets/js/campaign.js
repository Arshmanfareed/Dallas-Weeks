$(document).ready(function () {
    clearSessionStorage();
    setUpEventListeners();

    function clearSessionStorage() {
        sessionStorage.removeItem("campaign_details");
        sessionStorage.removeItem("settings");
        sessionStorage.removeItem("elements_array");
        sessionStorage.removeItem("elements_data_array");
    }

    function setUpEventListeners() {
        $(".setting_btn").on("click", toggleSettingsList);
        $("#filterSelect, #search_campaign").on("change input", filterSearch);
        $(document).on("change", ".switch", toggleCampaignStatus);
        $(document).on("click", ".delete_campaign", deleteCampaign);
        $(document).on("click", ".archive_campaign", archiveCampaign);
        $(document).on("click", "#filterToggle", toggleFilterSelect);
    }

    function showLoader() {
        $("#loader").show();
    }

    function hideLoader() {
        $("#loader").hide();
    }

    function showToastr(type, message) {
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
        toastr[type](message);
    }

    function handleError(xhr, status, error) {
        console.error(error);
    }

    function handleEmptyTable() {
        if ($(".campaign_table_row").length === 0) {
            let html = '<tr><td colspan="8"><div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div></td></tr>';
            $("#campaign_table_body").html(html);
        }
    }

    function toggleCampaignStatus(e) {
        var campaign_id = $(this).attr("id").replace("switch", "");
        ajaxRequest(activateCampaignRoute.replace(":campaign_id", campaign_id), "GET", {}, function (response) {
            if (response.success) {
                showToastr(response.active === 1 ? 'success' : 'info', "Campaign successfully " + (response.active === 1 ? "Activated" : "Deactivated"));
            }
            if ($("#filterSelect").val() != "archive") {
                $("#table_row_" + campaign_id).remove();
            }
            handleEmptyTable();
        });
    }

    function deleteCampaign(e) {
        if (confirm("Are you sure to delete this campaign?")) {
            var campaign_id = $(this).attr("id").replace("delete", "");
            ajaxRequest(deleteCampaignRoute.replace(":id", campaign_id), "GET", {}, function (response) {
                if (response.success) {
                    showToastr('success', "Campaign successfully Deleted");
                } else {
                    showToastr('error', "Campaign cannot be Deleted");
                }
                $("#table_row_" + campaign_id).remove();
                handleEmptyTable();
            });
        }
    }

    function archiveCampaign(e) {
        if (confirm("Are you sure to archive this campaign?")) {
            var campaign_id = $(this).attr("id").replace("archive", "");
            ajaxRequest(archiveCampaignRoute.replace(":id", campaign_id), "GET", {}, function (response) {
                if (response.success) {
                    showToastr(response.archive === 1 ? 'success' : 'info', "Campaign successfully Archived");
                }
                $("#table_row_" + campaign_id).remove();
                handleEmptyTable();
            });
        }
    }

    function toggleFilterSelect(e) {
        e.preventDefault();
        $("#filterSelect").toggle();
    }

    function filterSearch(e) {
        e.preventDefault();
        var filter = $("#filterSelect").val();
        var search = $("#search_campaign").val() || "null";
        ajaxRequest(filterCampaignRoute.replace(":filter", filter).replace(":search", search), "GET", {}, function (response) {
            if (response.success) {
                renderCampaigns(response.campaigns);
            } else {
                html = displayNoCampaignsFound();
                $("#campaign_table_body").html(html);
                $(".setting_btn").on("click", toggleSettingsList);
            }
        });
    }

    function renderCampaigns(campaigns) {
        let html = '';
        if (campaigns.length > 0) {
            html = campaigns.map(campaign => renderCampaignRow(campaign)).join('');
        } else {
            html = displayNoCampaignsFound();
        }
        $("#campaign_table_body").html(html);
        $(".setting_btn").on("click", toggleSettingsList);
    }

    function renderCampaignRow(campaign) {
        return `
            <tr id="table_row_${campaign.id}" class="campaign_table_row">
                <td><div class="switch_box">
                    <input type="checkbox" class="switch" id="switch${campaign.id}" ${campaign.is_active == 1 ? 'checked' : ''}>
                    <label for="switch${campaign.id}">Toggle</label>
                </div></td>
                <td>${campaign.campaign_name}</td>
                <td id="lead_count_${campaign.id}">0</td>
                <td>105</td>
                <td class="stats"><ul class="status_list d-flex align-items-center list-unstyled p-0 m-0">
                    <li><span><img src="/assets/img/eye.svg" alt=""><span id="view_profile_count_${campaign.id}">0</span></span></li>
                    <li><span><img src="/assets/img/request.svg" alt=""><span id="invite_to_connect_count_${campaign.id}">0</span></span></li>
                    <li><span><img src="/assets/img/mailmsg.svg" alt="">10</span></li>
                    <li><span><img src="/assets/img/mailopen.svg" alt="">16</span></li>
                </ul></td>
                <td><div class="per up">34%</div></td>
                <td><div class="per down">23%</div></td>
                <td>
                    <a type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>
                    <ul class="setting_list" style="display: none;">
                        <li><a href="/campaign/campaignDetails/${campaign.id}">Check campaign details</a></li>
                        <li><a href="/campaign/editcampaign/${campaign.id}">Edit campaign</a></li>
                        <li><a class="archive_campaign" id="archive${campaign.id}">${$("#filterSelect").val() === "archive" ? "Remove From Archive" : "Archive campaign"}</a></li>
                        <li><a class="delete_campaign" id="delete${campaign.id}">Delete campaign</a></li>
                    </ul>
                </td>
            </tr>
        `;
    }

    function displayNoCampaignsFound() {
        return '<tr><td colspan="8"><div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div></td></tr>';
    }

    function toggleSettingsList(e) {
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

    function ajaxRequest(url, type, data, successCallback) {
        $.ajax({
            url: url,
            type: type,
            data: data,
            beforeSend: showLoader,
            success: successCallback,
            error: handleError,
            complete: hideLoader,
        });
    }
});
