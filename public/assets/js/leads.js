$(document).ready(function () {
    $(".lead_tab").on("click", function (e) {
        e.preventDefault();
        $(".lead_tab").removeClass("active");
        $(this).addClass("active");
        var id = $(this).data("bs-target");
        $(".lead_pane").removeClass("active");
        $("#" + id).addClass("active");
    });

    $("#campaign").on("change", function (e) {
        var campaign_id = $(this).val();
        $.ajax({
            url: leadsCampaignFilterPath.replace(":id", campaign_id),
            type: "GET",
            success: function (response) {
                if (response.success) {
                    var leads = response.leads;
                    var html = ``;
                    for (var key in leads) {
                        html += `<tr><td><div class="switch_box"><input type="checkbox" class="switch"`;
                        if (leads[key]["is_active"] == 1) {
                            html += ` checked `;
                        }
                        html += `id="` + leads[key]["id"] + `"><label for="`;
                        html +=
                            leads[key]["id"] + `">Toggle</label></div></td>`;
                        html +=
                            `<td class="title_cont">` +
                            leads[key]["contact"] +
                            `</td>`;
                        html +=
                            `<td class="title_comp">` +
                            leads[key]["title_company"] +
                            `</td>`;
                        html += `<td class="">`;
                        if (leads[key]["send_connections"] == "1") {
                            html += `<div class="per connected">Connected</div>`;
                        } else {
                            html += `<div class="per discovered">Discovered</div>`;
                        }
                        html += `</td><td>23</td>`;
                        html += `<td>` + leads[key]["next_step"] + `</td>`;
                        html += `<td><div class="">2 days ago</div></td>`;
                        html += `<td><a href="javascript:;" type="button" class="setting setting_btn"`;
                        html += `id=""><i class="fa-solid fa-gear"></i></a>`;
                        html += `<ul class="setting_list" style="display: block;">`;
                        html += `<li><a href="#">Edit</a></li><li><a href="#">Delete</a></li>`;
                        html += `</ul></td>`;
                        html += `</tr>`;
                    }
                    $(".leads_list table tbody").html(html);
                } else {
                    var html = ``;
                    html += `<tr><td colspan="8"><div class="text-center text-danger" `;
                    html += `style="font-size: 25px; font-weight: bold;`;
                    html += ` font-style: italic;">Not Found!</div></td></tr>`;
                    $(".leads_list table tbody").html(html);
                }
                $(".setting_btn").on("click", setting_list);
                $(".setting_list").hide();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });

    function setting_list() {
        $(".setting_list").hide();
        $(".setting_btn").on("click", function (e) {
            $(".setting_list").not($(this).siblings(".setting_list")).hide();
            $(this).siblings(".setting_list").toggle();
        });
        $(document).on("click", function (e) {
            if (!$(event.target).closest(".setting").length) {
                $(".setting_list").hide();
            }
        });
    }
});
