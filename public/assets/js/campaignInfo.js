$(document).ready(function () {
    sessionStorage.removeItem("elements_array");
    sessionStorage.removeItem("elements_data_array");

    initializeSettings();
    appendHiddenInputs();
    setUpEventListeners();

    function initializeSettings() {
        let settings = JSON.parse(sessionStorage.getItem("settings")) || {};

        $(".linkedin_setting_switch").each(function () {
            const name = $(this).attr("name");
            if (settings[name] === "no") {
                $(this).attr("value", "no").prop("checked", false);
            } else {
                $(this).attr("value", "yes").prop("checked", true);
            }
        });

        if (!sessionStorage.getItem("settings")) {
            $(".linkedin_setting_switch").each(function () {
                const inputName = $(this).attr("name");
                settings[inputName] = "no";
                $(this).prop("checked", false);
            });
            sessionStorage.setItem("settings", JSON.stringify(settings));
        }
    }

    function appendHiddenInputs() {
        const form = $("#settings");
        const campaignDetails = campaign_details; // Assuming this is defined somewhere in your scope

        const hiddenFields = [
            { name: "campaign_type", value: campaignDetails["campaign_type"] },
            { name: "campaign_name", value: campaignDetails["campaign_name"] },
            { name: "campaign_url", value: campaignDetails["campaign_url"] },
            { name: "campaign_url_hidden", value: campaignDetails["campaign_url_hidden"] }
        ];

        if (campaignDetails["connections"] !== undefined) {
            hiddenFields.push({ name: "connections", value: campaignDetails["connections"] });
        }

        hiddenFields.forEach(field => {
            form.append($("<input>").attr("type", "hidden").attr("name", field.name).val(field.value));
        });
    }

    function setUpEventListeners() {
        $("#create_sequence").on("click", handleCreateSequence);
        $(".next_tab").on("click", navigateTabs.bind(null, "next"));
        $(".prev_tab").on("click", navigateTabs.bind(null, "prev"));
        $(".schedule-btn").on("click", toggleScheduleTab);
        $(".schedule_days").on("change", handleScheduleDays);
        $(".add_schedule").on("click", handleAddSchedule);
        $(".search_schedule").on("input", handleSearchSchedule);
    }

    function handleCreateSequence(e) {
        e.preventDefault();
        const form = $("#settings");
        const settings = JSON.parse(sessionStorage.getItem("settings"));

        form.find(".linkedin_setting_switch").each(function () {
            const name = $(this).attr("name");
            const value = $(this).is(":checked") ? "yes" : "no";
            $(this).attr("value", value);
            settings[name] = value;
        });

        sessionStorage.setItem("settings", JSON.stringify(settings));
        form.submit();
    }

    function navigateTabs(direction, e) {
        const activeTab = $(this).closest(".comp_tabs").find(".nav-tabs .nav-link.active");
        direction === "next" ? activeTab.next().click() : activeTab.prev().click();
    }

    function toggleScheduleTab(e) {
        e.preventDefault();
        const targetTab = $("#" + $(this).data("tab"));
        const parent = $(this).closest(".schedule-content").parent();

        parent.find(".schedule-content.active").removeClass("active");
        targetTab.addClass("active");
        parent.find(".schedule-btn.active").removeClass("active");
        $(this).addClass("active");
    }

    function handleScheduleDays(e) {
        const day = $(this).val();
        const isChecked = $(this).prop("checked");

        $("#" + day + "_start_time").val(isChecked ? "09:00:00" : "");
        $("#" + day + "_end_time").val(isChecked ? "17:00:00" : "");
    }

    function handleAddSchedule(e) {
        e.preventDefault();
        const form = $(".schedule_form");

        form.find('input[type="checkbox"]').each(function () {
            $(this).attr("value", $(this).is(":checked") ? "true" : "false").prop("checked", true);
        });

        $.ajax({
            url: createSchedulePath,
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: form.serialize(),
            beforeSend: () => $("#loader").show(),
            success: renderScheduleList,
            error: handleError,
            complete: () => $("#loader").hide()
        });
    }

    function handleSearchSchedule(e) {
        const search = $(this).val() || "null";
        const scheduleList = $(this).parent().next(".schedule_list");

        $.ajax({
            url: filterSchedulePath.replace(":search", search),
            method: "GET",
            success: response => renderScheduleList(response, scheduleList),
            error: xhr => {
                if (xhr.status == 404) {
                    renderNotFound(scheduleList);
                }
            }
        });
    }

    function renderScheduleList(response, scheduleList = null) {
        if (response.success) {
            const schedules = response.schedules;
            let html = schedules.map(schedule => {
                const daysHtml = schedule["Days"].map(day => `
                    <li class="schedule_day ${day["is_active"] == "1" ? "selected_day" : ""}">
                        ${day["schedule_day"].toUpperCase()}
                    </li>
                `).join('');

                return `
                    <li>
                        <div class="row schedule_list_item">
                            <div class="col-lg-1 schedule_item">
                                <input type="radio" name="email_settings_schedule_id" class="schedule_id" value="${schedule["id"]}" ${schedule["user_id"] == 0 ? "checked" : ""}>
                            </div>
                            <div class="col-lg-1 schedule_avatar">S</div>
                            <div class="col-lg-3 schedule_name">
                                <i class="fa-solid fa-circle-check" style="color: #4bcea6;"></i>
                                <span>${schedule["schedule_name"]}</span>
                            </div>
                            <div class="col-lg-6 schedule_days">
                                <ul class="schedule_day_list">
                                    ${daysHtml}
                                    <li class="schedule_time">
                                        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#time_modal">
                                            <i class="fa-solid fa-globe" style="color: #16adcb;"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-1 schedule_menu_btn">
                                <i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');

            if (scheduleList) {
                scheduleList.html(html);
            } else {
                $("#schedule_list_1").html(html);
                $("#schedule_list_2").html(html.replace("email_settings_schedule_id", "global_settings_schedule_id"));
            }
        }
    }

    function renderNotFound(scheduleList) {
        const notFoundHtml = `<li><div class="text-center text-danger">Not Found!</div></li>`;
        scheduleList.html(notFoundHtml);
    }

    function handleError(xhr, status, error) {
        console.error(error);
    }
});
