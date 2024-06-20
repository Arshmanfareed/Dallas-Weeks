$(document).ready(function () {
    sessionStorage.removeItem("settings");
    sessionStorage.removeItem("elements_array");
    sessionStorage.removeItem("elements_data_array");

    initializeCampaignDetails();
    setEventListeners();

    function initializeCampaignDetails() {
        if (campaign_details["campaign_type"] === undefined) {
            campaign_details["campaign_type"] = "linkedin";
        }

        const campaignPane = $(".campaign_pane");
        campaignPane.each(function () {
            const campaignType = $(this).find("#campaign_type").val(); $(document).ready(function () {
                sessionStorage.removeItem("settings");
                sessionStorage.removeItem("elements_array");
                sessionStorage.removeItem("elements_data_array");

                initializeCampaignDetails();
                setEventListeners();

                function initializeCampaignDetails() {
                    if (campaign_details["campaign_type"] === undefined) {
                        campaign_details["campaign_type"] = "linkedin";
                    }

                    const campaignPane = $(".campaign_pane");
                    campaignPane.each(function () {
                        const campaignType = $(this).find("#campaign_type").val();
                        if (campaignType === campaign_details["campaign_type"]) {
                            $(this).addClass("active");
                            $('[data-bs-target="#' + $(this).attr("id") + '"]').addClass("active");
                        }
                    });

                    $(".campaign_tab.active").parent(".border_box").css({
                        "background-color": "#16adcb"
                    });

                    if (!campaign_details["campaign_name"]) campaign_details["campaign_name"] = "";
                    if (!campaign_details["campaign_url"]) campaign_details["campaign_url"] = "";
                    if (!campaign_details["connections"]) campaign_details["connections"] = "1";

                    const activeForm = $(".campaign_pane.active").find("form");
                    fillFormWithCampaignDetails(activeForm);
                }

                function setEventListeners() {
                    $(document).on("change", "#campaign_url", handleFileInputChange);
                    $(".campaign_name").on("change", handleInputChange("campaign_name"));
                    $(".campaign_url").on("change", handleInputChange("campaign_url"));
                    $(".connections").on("change", handleInputChange("connections"));
                    $(".campaign_tab").on("click", handleCampaignTabClick);
                    $(".nxt_btn").on("click", handleNextButtonClick);
                    $(".import_btn").on("click", handleImportButtonClick);
                }

                function handleFileInputChange(e) {
                    const file = e.target.files[0];
                    const importField = $(".import_field");
                    importField.find("label").remove();

                    if (file) {
                        importField.append('<label style="margin-bottom: 0px">' + file.name + "</label>");
                    } else {
                        importField.append(getDefaultFileInputLabel());
                    }
                }

                function handleInputChange(key) {
                    return function (e) {
                        campaign_details[key] = $(this).val();
                        sessionStorage.setItem("campaign_details", JSON.stringify(campaign_details));
                    }
                }

                function handleCampaignTabClick(e) {
                    e.preventDefault();
                    $(".campaign_tab").parent(".border_box").css("background-color", "rgb(17 19 23)");
                    $(".campaign_tab").removeClass("active");
                    $(this).addClass("active");

                    const id = $(this).data("bs-target");
                    $(".campaign_pane").removeClass("active");
                    $("#" + id).addClass("active");

                    $(".campaign_tab.active").parent(".border_box").css({
                        "background-color": "#16adcb"
                    });

                    const newForm = $("#" + id).find("form");
                    campaign_details["campaign_type"] = newForm.find("#campaign_type").val();
                    sessionStorage.setItem("campaign_details", JSON.stringify(campaign_details));
                    fillFormWithCampaignDetails(newForm);
                }

                function handleNextButtonClick(e) {
                    e.preventDefault();
                    const activeForm = $(".campaign_pane.active").find("form");

                    if (activeForm.attr("id") === "campaign_form_4") {
                        uploadCSVFile(activeForm);
                    } else {
                        activeForm.submit();
                    }
                }

                function handleImportButtonClick(e) {
                    e.preventDefault();
                    const activeForm = $(".campaign_pane.active").find("form");
                    activeForm.submit();
                }

                function fillFormWithCampaignDetails(form) {
                    form.find("#campaign_name").val(campaign_details["campaign_name"]);

                    if (form.attr("id") !== "campaign_form_4") {
                        form.find("#campaign_url").val(campaign_details["campaign_url"]);
                    }

                    if (form.attr("id") !== "campaign_form_4" && form.attr("id") !== "campaign_form_3") {
                        form.find("#connections").val(campaign_details["connections"]);
                    }
                }

                function uploadCSVFile(form) {
                    const fileInput = form.find("#campaign_url")[0].files[0];
                    const formData = new FormData();
                    formData.append("campaign_url", fileInput);
                    const csrfToken = $('meta[name="csrf-token"]').attr("content");

                    $.ajax({
                        url: importCSVPath,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: { "X-CSRF-TOKEN": csrfToken },
                        beforeSend: function () {
                            $("#loader").show();
                        },
                        success: handleCSVUploadSuccess,
                        error: handleCSVUploadError,
                        complete: function () {
                            $("#loader").hide();
                        }
                    });
                }

                function handleCSVUploadSuccess(response) {
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
                            .text(response.total_without_duplicate_blacklist + " leads");

                        $("#campaign_url_hidden").val(response.path);
                        campaign_details["campaign_url"] = response.path;
                        sessionStorage.setItem("campaign_details", JSON.stringify(campaign_details));
                        $("#sequance_modal").modal("show");
                    } else {
                        showErrorToast(response.message);
                    }
                }

                function handleCSVUploadError(xhr) {
                    if (xhr.status === 422) {
                        const response = JSON.parse(xhr.responseText);
                        const errorMessage = response.errors.campaign_url[0];
                        const form = $(".campaign_pane.active").find("form");

                        form.find("span.campaign_url").text(errorMessage);
                        form.find(".import_field").css({ border: "1px solid red", "margin-bottom": "7px !important" });
                        form.find(".file-input__label").css({ "background-color": "red" });
                    } else {
                        console.error("Upload failed:", error);
                    }
                }

                function getDefaultFileInputLabel() {
                    return `
                        <label class="file-input__label" for="file-input">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="upload" class="svg-inline--fa fa-upload fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="currentColor" d="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z">
                                </path>
                            </svg>
                            <span>Upload file</span>
                        </label>`;
                }

                function showErrorToast(message) {
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
                        hideMethod: "fadeOut"
                    };
                    toastr.error(message);
                }
            });

            if (campaignType === campaign_details["campaign_type"]) {
                $(this).addClass("active");
                $('[data-bs-target="#' + $(this).attr("id") + '"]').addClass("active");
            }
        });

        $(".campaign_tab.active").parent(".border_box").css({
            "background-color": "#16adcb"
        });

        if (!campaign_details["campaign_name"]) campaign_details["campaign_name"] = "";
        if (!campaign_details["campaign_url"]) campaign_details["campaign_url"] = "";
        if (!campaign_details["connections"]) campaign_details["connections"] = "1";

        const activeForm = $(".campaign_pane.active").find("form");
        fillFormWithCampaignDetails(activeForm);
    }

    function setEventListeners() {
        $(document).on("change", "#campaign_url", handleFileInputChange);
        $(".campaign_name").on("change", handleInputChange("campaign_name"));
        $(".campaign_url").on("change", handleInputChange("campaign_url"));
        $(".connections").on("change", handleInputChange("connections"));
        $(".campaign_tab").on("click", handleCampaignTabClick);
        $(".nxt_btn").on("click", handleNextButtonClick);
        $(".import_btn").on("click", handleImportButtonClick);
    }

    function handleFileInputChange(e) {
        const file = e.target.files[0];
        const importField = $(".import_field");
        importField.find("label").remove();

        if (file) {
            importField.append('<label style="margin-bottom: 0px">' + file.name + "</label>");
        } else {
            importField.append(getDefaultFileInputLabel());
        }
    }

    function handleInputChange(key) {
        return function (e) {
            campaign_details[key] = $(this).val();
            sessionStorage.setItem("campaign_details", JSON.stringify(campaign_details));
        }
    }

    function handleCampaignTabClick(e) {
        e.preventDefault();
        $(".campaign_tab").parent(".border_box").css("background-color", "rgb(17 19 23)");
        $(".campaign_tab").removeClass("active");
        $(this).addClass("active");

        const id = $(this).data("bs-target");
        $(".campaign_pane").removeClass("active");
        $("#" + id).addClass("active");

        $(".campaign_tab.active").parent(".border_box").css({
            "background-color": "#16adcb"
        });

        const newForm = $("#" + id).find("form");
        campaign_details["campaign_type"] = newForm.find("#campaign_type").val();
        sessionStorage.setItem("campaign_details", JSON.stringify(campaign_details));
        fillFormWithCampaignDetails(newForm);
    }

    function handleNextButtonClick(e) {
        e.preventDefault();
        const activeForm = $(".campaign_pane.active").find("form");

        if (activeForm.attr("id") === "campaign_form_4") {
            uploadCSVFile(activeForm);
        } else {
            activeForm.submit();
        }
    }

    function handleImportButtonClick(e) {
        e.preventDefault();
        const activeForm = $(".campaign_pane.active").find("form");
        activeForm.submit();
    }

    function fillFormWithCampaignDetails(form) {
        form.find("#campaign_name").val(campaign_details["campaign_name"]);

        if (form.attr("id") !== "campaign_form_4") {
            form.find("#campaign_url").val(campaign_details["campaign_url"]);
        }

        if (form.attr("id") !== "campaign_form_4" && form.attr("id") !== "campaign_form_3") {
            form.find("#connections").val(campaign_details["connections"]);
        }
    }

    function uploadCSVFile(form) {
        const fileInput = form.find("#campaign_url")[0].files[0];
        const formData = new FormData();
        formData.append("campaign_url", fileInput);
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: importCSVPath,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            beforeSend: function () {
                $("#loader").show();
            },
            success: handleCSVUploadSuccess,
            error: handleCSVUploadError,
            complete: function () {
                $("#loader").hide();
            }
        });
    }

    function handleCSVUploadSuccess(response) {
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
                .text(response.total_without_duplicate_blacklist + " leads");

            $("#campaign_url_hidden").val(response.path);
            campaign_details["campaign_url"] = response.path;
            sessionStorage.setItem("campaign_details", JSON.stringify(campaign_details));
            $("#sequance_modal").modal("show");
        } else {
            showErrorToast(response.message);
        }
    }

    function handleCSVUploadError(xhr) {
        if (xhr.status === 422) {
            const response = JSON.parse(xhr.responseText);
            const errorMessage = response.errors.campaign_url[0];
            const form = $(".campaign_pane.active").find("form");

            form.find("span.campaign_url").text(errorMessage);
            form.find(".import_field").css({ border: "1px solid red", "margin-bottom": "7px !important" });
            form.find(".file-input__label").css({ "background-color": "red" });
        } else {
            console.error("Upload failed:", error);
        }
    }

    function getDefaultFileInputLabel() {
        return `
            <label class="file-input__label" for="file-input">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="upload" class="svg-inline--fa fa-upload fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="currentColor" d="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z">
                    </path>
                </svg>
                <span>Upload file</span>
            </label>`;
    }

    function showErrorToast(message) {
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
            hideMethod: "fadeOut"
        };
        toastr.error(message);
    }
});
