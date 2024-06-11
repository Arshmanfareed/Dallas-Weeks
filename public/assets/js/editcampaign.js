$(document).ready(function () {
    var edit_campaign_details = campaign;
    if (edit_campaign_details["campaign_type"] == undefined) {
        edit_campaign_details["campaign_type"] = "linkedin";
    }
    var campaign_pane = $(".campaign_pane");
    for (var i = 0; i < campaign_pane.length; i++) {
        var campaignType = $(campaign_pane[i]).find("#campaign_type").val();
        if (campaignType == edit_campaign_details["campaign_type"]) {
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
        edit_campaign_details["campaign_name"] == undefined ||
        edit_campaign_details["campaign_url"] == undefined ||
        edit_campaign_details["campaign_connection"] == undefined
    ) {
        edit_campaign_details["campaign_name"] = "";
        edit_campaign_details["campaign_url"] = "";
        edit_campaign_details["campaign_connection"] = "";
        $(".campaign_pane.active")
            .find("form")
            .find("#campaign_name")
            .val(edit_campaign_details["campaign_name"]);
        if (
            $(".campaign_pane.active").find("form").attr("id") !=
            "campaign_form_4"
        ) {
            $(".campaign_pane.active")
                .find("form")
                .find("#campaign_url")
                .val(edit_campaign_details["campaign_url"]);
        }
        if (
            $(".campaign_pane.active").find("form").attr("id") !=
                "campaign_form_4" &&
            $(".campaign_pane.active").find("form").attr("id") !=
                "campaign_form_3"
        ) {
            $(".campaign_pane.active")
                .find("form")
                .find("#connections")
                .val(edit_campaign_details["campaign_connection"]);
        }
    } else {
        var active_form = $(".campaign_pane.active").find("form");
        active_form
            .find("#campaign_name")
            .val(edit_campaign_details["campaign_name"]);
        if (active_form.attr("id") != "campaign_form_4") {
            active_form
                .find("#campaign_url")
                .val(edit_campaign_details["campaign_url"]);
        }
        if (
            active_form.attr("id") != "campaign_form_4" &&
            active_form.attr("id") != "campaign_form_3"
        ) {
            active_form
                .find("#connections")
                .val(edit_campaign_details["campaign_connection"]);
        }
    }
    $(".campaign_name").on("change", function (e) {
        edit_campaign_details["campaign_name"] = $(this).val();
        sessionStorage.setItem(
            "edit_campaign_details",
            JSON.stringify(edit_campaign_details)
        );
    });
    $(".campaign_url").on("change", function (e) {
        edit_campaign_details["campaign_url"] = $(this).val();
        sessionStorage.setItem(
            "edit_campaign_details",
            JSON.stringify(edit_campaign_details)
        );
    });
    $(".connections").on("change", function (e) {
        edit_campaign_details["campaign_connection"] = $(this).val();
        sessionStorage.setItem(
            "edit_campaign_details",
            JSON.stringify(edit_campaign_details)
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
        edit_campaign_details["campaign_type"] = new_form
            .find("#campaign_type")
            .val();
        sessionStorage.setItem(
            "edit_campaign_details",
            JSON.stringify(edit_campaign_details)
        );
        new_form
            .find("#campaign_name")
            .val(edit_campaign_details["campaign_name"]);
        if (new_form.attr("id") != "campaign_form_4") {
            new_form
                .find("#campaign_url")
                .val(edit_campaign_details["campaign_url"]);
        }
        if (
            new_form.attr("id") != "campaign_form_4" &&
            new_form.attr("id") != "campaign_form_3"
        ) {
            new_form
                .find("#connections")
                .val(edit_campaign_details["campaign_connection"]);
        }
    });
    $(".nxt_btn").on("click", function (e) {
        e.preventDefault();
        var form = $(".campaign_pane.active").find("form");
        form.submit();
    });
});
