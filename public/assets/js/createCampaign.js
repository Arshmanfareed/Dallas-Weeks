$(document).ready(function () {
    sessionStorage.removeItem("settings");
    sessionStorage.removeItem("elements_array");
    sessionStorage.removeItem("elements_data_array");
    $(document).on("change", "#campaign_url", function (e) {
        var active_form = $(".campaign_pane.active").find("form");
        if (active_form.attr("id") == "campaign_form_4") {
            var file = e.target.files[0];
            if (file) {
                $(".import_field").find("label").remove();
                $(".import_field").append(
                    '<label style="margin-bottom: 0px">' +
                        file.name +
                        "</label>"
                );
            } else {
                $(".import_field").find("label").remove();
                html = "";
                html +=
                    '<label class="file-input__label" for="file-input"><svg aria-hidden="true"';
                html +=
                    'focusable="false" data-prefix="fas" data-icon="upload"';
                html += 'class="svg-inline--fa fa-upload fa-w-16"';
                html += 'role="img" xmlns="http://www.w3.org/2000/svg"';
                html += 'viewBox="0 0 512 512"> <path fill="currentColor"';
                html +=
                    'd="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z">';
                html += "</path></svg><span>Upload file</span></label>";
                $(".import_field").append(html);
            }
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
        var active_form = $(".campaign_pane.active").find("form");
        if (active_form.attr("id") == "campaign_form_2") {
            var queryString = $(".campaign_pane.active")
                .find("#campaign_url")
                .val();
            var decodedUrl = decodeURI(decodeURI(queryString));
            var queryParams = getQueryParams(decodedUrl);
            var query = queryParams.query;
            query = query.replaceAll("(", "{").replaceAll(")", "}");
            query = lister(query);
            query = removeExtraColons(query);
            query = wrapKeysInQuotes(query);
            query = wrapValuesInQuotes(query);
            try {
                queryJson = JSON.parse(query);
                queryParams.query = queryJson;
                var filters = queryParams.query.filters;
                var active_form = $(".campaign_pane.active").find("form");
                filters.forEach(function (filter) {
                    var type = filter["type"];
                    if (type == "RELATIONSHIP") {
                        var values = filter["values"];
                        values.forEach(function (value) {
                            if (value["text"].includes("1st")) {
                                active_form.find("#connections").val("1");
                            } else if (value["text"].includes("2nd")) {
                                active_form.find("#connections").val("2");
                            } else if (value["text"].includes("3rd")) {
                                active_form.find("#connections").val("3");
                            } else {
                                active_form.find("#connections").val("o");
                            }
                        });
                        active_form.find("#connections").prop("disabled", true);
                    }
                });
                var jsonString = JSON.stringify(queryParams);
                var jsonInput = $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "lead_details")
                    .val(jsonString);
                active_form.append(jsonInput);
            } catch (e) {
                $(".campaign_pane.active")
                    .find("form")
                    .find("#campaign_url")
                    .css({
                        border: "1px solid red",
                        "margin-bottom": "7px !important",
                    });
            }
        } else {
            campaign_details["campaign_url"] = $(this).val();
            sessionStorage.setItem(
                "campaign_details",
                JSON.stringify(campaign_details)
            );
        }
    });

    function getQueryParams(url) {
        var params = {};
        var parser = document.createElement("a");
        parser.href = url;
        var query = parser.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            var key = decodeURIComponent(pair[0]);
            var value = decodeURIComponent(pair[1]);
            if (key != "query") {
                if (value.includes("true")) {
                    value = true;
                } else if (value.includes("false")) {
                    value = false;
                }
            }
            params[key] = value;
        }
        return params;
    }

    function wrapValuesInQuotes(jsonString) {
        jsonString = jsonString.replace(/(\w+):/g, '"$1":');
        jsonString = jsonString.replace(
            /:\s*([^"\[{].*?)([,}\]])/g,
            function (match, p1, p2) {
                if (/^(true|false|\d+)$/.test(p1.trim())) {
                    return ": " + p1 + p2;
                } else {
                    return ': "' + p1 + '"' + p2;
                }
            }
        );
        jsonString = jsonString.replace(/:"(\{[^{}]*\})"/g, ":$1");
        jsonString = jsonString.replace(/:"(\[[^\[\]]*\])"/g, ":$1");
        return jsonString;
    }

    function wrapKeysInQuotes(jsonString) {
        jsonString = jsonString.replace(/(\w+):/g, '"$1":');
        jsonString = jsonString.replace(/(\{[^{}]*\}):/g, "$1:");
        jsonString = jsonString.replace(/(\[[^\[\]]*\]):/g, "$1:");
        return jsonString;
    }

    function lister(queryString) {
        if (queryString.includes("List{")) {
            var count = 1;
            var newQueryString = "";
            for (
                var i = queryString.indexOf("List{") + 5;
                i < queryString.length;
                i++
            ) {
                if (queryString[i] == "{") {
                    count++;
                } else if (queryString[i] == "}") {
                    count--;
                }
                if (count == 0) {
                    newQueryString =
                        queryString.substring(0, i) +
                        "]" +
                        queryString.substring(i + 1);
                    break;
                }
            }
            newQueryString = newQueryString.replace("List{", "[");
            queryString = lister(newQueryString);
        }
        return queryString;
    }

    function removeExtraColons(queryString) {
        for (var i = 0; i < queryString.length; i++) {
            if (queryString[i] == ":") {
                for (var j = i + 1; j < queryString.length; j++) {
                    if (queryString[j] == "," || queryString[j] == "{") {
                        break;
                    } else if (queryString[j] == ":") {
                        break;
                    }
                }
                if (queryString[j] == ":") {
                    queryString =
                        queryString.slice(0, j) +
                        " " +
                        queryString.slice(j + 1);
                    i--;
                }
            }
        }
        return queryString;
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
                headers: { "X-CSRF-TOKEN": csrfToken },
                beforeSend: function () {
                    $("#loader").show();
                },
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
                complete: function () {
                    $("#loader").hide();
                },
            });
        } else {
            form.submit();
        }
    });

    $(".import_btn").on("click", function (e) {
        e.preventDefault();
        var form = $(".campaign_pane.active").find("form");
        form.submit();
    });
});
