$(document).ready(function () {
    /* Making every setting to unchangable */
    $(".linkedin_setting_switch").prop("disabled", true);
    var inputElement = null;
    var outputElement = null;
    var condition = "";
    var elements_array = {};
    var element_data_array = {};

    // $.ajax({
    //     url: getElementPath.replace(":campaign_id", campaign_id),
    //     method: "GET",
    //     success: function (response) {
    //         if (response.success) {
    //             var response_elements_array = response.elements_array;
    //             var response_path = response.path;
    //             if (response.elements_array) {
    //                 var maxDropPadHeight = 0;
    //                 html = ``;
    //                 html += `<div class="step-1 element_item" id="step-1"><div class="list-icon">`;
    //                 html += `<i class="fa-solid fa-certificate"></i></div><div class="item_details">`;
    //                 html += `<p class="item_name">Lead Source (Step 1)</p><p class="item_desc">`;
    //                 html += `<i class="fa-solid fa-clock"></i>Wait for: <span class="item_days">0</span>`;
    //                 html += ` days <span class="item_hours">0</span> hours</p></div>`;
    //                 html += `<div class="element_change_output attach-elements-out condition_true"></div></div>`;
    //                 $(".task-list").append(html);
    //                 for (var i = 0; i < response_elements_array.length; i++) {
    //                     var arr = {};
    //                     var data_arr = {};
    //                     var element =
    //                         response_elements_array[i]["original_element"];
    //                     var original_properties =
    //                         response_elements_array[i]["properties"];
    //                     var days = 0;
    //                     var hours = 0;
    //                     for (var j = 0; j < original_properties.length; j++) {
    //                         data_arr[original_properties[j]["id"]] =
    //                             original_properties[j]["value"];
    //                         if (
    //                             original_properties[j]["original_properties"][
    //                                 "property_name"
    //                             ] == "Hours"
    //                         ) {
    //                             hours = original_properties[j]["value"];
    //                         } else if (
    //                             original_properties[j]["original_properties"][
    //                                 "property_name"
    //                             ] == "Days"
    //                         ) {
    //                             days = original_properties[j]["value"];
    //                         }
    //                     }
    //                     html = ``;
    //                     if (element["is_conditional"] == "1") {
    //                         html += `<div class="element_item drop-pad-element placedElement" id="`;
    //                         html += response_elements_array[i]["id"] + `"`;
    //                         html +=
    //                             `data-filter-name="` + element["element_name"];
    //                         html += `" style="position: absolute;">`;
    //                         html += `<div class="element_change_input conditional-elements conditional-elements-in"></div>`;
    //                         html += `<div class="cancel-icon"><i class="fa-solid fa-x"></i></div>`;
    //                         html +=
    //                             `<div class="list-icon">` +
    //                             element["element_icon"] +
    //                             `</div>`;
    //                         html += `<div class="item_details"><p class="item_name">`;
    //                         html += element["element_name"] + `</p>`;
    //                         html += `<p class="item_desc"><i class="fa-solid fa-clock"></i>Check after: `;
    //                         html +=
    //                             `<span class="item_days">` +
    //                             days +
    //                             `</span> days `;
    //                         html +=
    //                             `<span class="item_hours">` +
    //                             hours +
    //                             `</span> hours`;
    //                         html += `</p></div>`;
    //                         html += `<div class="menu-icon"><i class="fa-solid fa-bars"></i></div>`;
    //                         html += `<div class="conditional-elements conditional-elements-out">`;
    //                         html += `<div class="element_change_output condition_true"><i class="fa-solid fa-check"></i>`;
    //                         html += `</div><div class="element_change_output condition_false">`;
    //                         html += `<i class="fa-solid fa-xmark"></i></div></div></div>`;
    //                     } else {
    //                         html += `<div class="element_item drop-pad-element placedElement" id="`;
    //                         html += response_elements_array[i]["id"] + `"`;
    //                         html +=
    //                             `data-filter-name="` + element["element_name"];
    //                         html += `" style="position: absolute;">`;
    //                         html += `<div class="element_change_input attach-elements attach-elements-in"></div>`;
    //                         html += `<div class="cancel-icon"><i class="fa-solid fa-x"></i></div>`;
    //                         html +=
    //                             `<div class="list-icon">` +
    //                             element["element_icon"] +
    //                             `</div>`;
    //                         html += `<div class="item_details"><p class="item_name">`;
    //                         html += element["element_name"] + `</p>`;
    //                         html += `<p class="item_desc"><i class="fa-solid fa-clock"></i>Wait for: `;
    //                         html +=
    //                             `<span class="item_days">` +
    //                             days +
    //                             `</span> days `;
    //                         html +=
    //                             `<span class="item_hours">` +
    //                             hours +
    //                             `</span> hours</p></div>`;
    //                         html += `<div class="menu-icon"><i class="fa-solid fa-bars"></i></div>`;
    //                         html += `<div class="element_change_output attach-elements attach-elements-out condition_true">`;
    //                         html += `</div></div>`;
    //                     }
    //                     $(".task-list").append(html);
    //                     var clone = $("#" + response_elements_array[i]["id"]);
    //                     var left = response_elements_array[i]["position_x"];
    //                     arr["position_x"] =
    //                         response_elements_array[i]["position_x"];
    //                     var step1_left = $("#step-1").position().left;
    //                     var subtract =
    //                         parseInt(response_elements_array[0]["position_x"]) -
    //                         step1_left;
    //                     if (parseInt(left) - subtract < 0) {
    //                         left = 0;
    //                     } else if (
    //                         parseInt(left) + $(clone).width() <
    //                         $(".drop-pad").width()
    //                     ) {
    //                         left = parseInt(left) - subtract;
    //                     } else {
    //                         left =
    //                             $(".drop-pad").width() -
    //                             $(clone).width() -
    //                             step1_top;
    //                     }
    //                     var top = response_elements_array[i]["position_y"];
    //                     arr["position_y"] =
    //                         response_elements_array[i]["position_y"];
    //                     var step1_top = $("#step-1").position().top;
    //                     var step1_height = $("#step-1").outerHeight(true);
    //                     subtract =
    //                         parseInt(response_elements_array[0]["position_y"]) -
    //                         step1_height -
    //                         step1_top;
    //                     if (parseInt(top) - subtract < 0) {
    //                         top = 0;
    //                     } else {
    //                         top = parseInt(top) - subtract;
    //                     }
    //                     $(clone).css({
    //                         left: left,
    //                         top: top,
    //                     });
    //                     var newDropPadHeight =
    //                         parseInt($(clone).css("top")) +
    //                         parseInt($(clone).css("height")) +
    //                         step1_top;
    //                     if (maxDropPadHeight < newDropPadHeight) {
    //                         maxDropPadHeight = newDropPadHeight;
    //                         $(".drop-pad").css(
    //                             "height",
    //                             maxDropPadHeight + "px"
    //                         );
    //                     }
    //                     arr["0"] = "";
    //                     arr["1"] = "";
    //                     elements_array[response_elements_array[i]["id"]] = arr;
    //                     element_data_array[response_elements_array[i]["id"]] =
    //                         data_arr;
    //                 }
    //                 $("#step-1")
    //                     .find(".condition_true")
    //                     .on("click", attachOutputElement)
    //                     .trigger("click");
    //                 var first_element = response_path[0]["current_element_id"];
    //                 $("#" + first_element)
    //                     .find(".element_change_input")
    //                     .on("click", attachInputElement)
    //                     .trigger("click");
    //                 for (var i = 0; i < response_path.length; i++) {
    //                     current_element =
    //                         response_path[i]["current_element_id"];
    //                     elements_array[response_path[i]["current_element_id"]][
    //                         "1"
    //                     ] = response_path[i]["next_true_element_id"];
    //                     elements_array[response_path[i]["current_element_id"]][
    //                         "0"
    //                     ] = response_path[i]["next_false_element_id"];
    //                     if (response_path[i]["next_false_element_id"] != "") {
    //                         $("#" + current_element)
    //                             .find(".condition_false")
    //                             .on("click", attachOutputElement)
    //                             .trigger("click");
    //                         $("#" + response_path[i]["next_false_element_id"])
    //                             .find(".element_change_input")
    //                             .on("click", attachInputElement)
    //                             .trigger("click");
    //                     }
    //                     if (response_path[i]["next_true_element_id"] != "") {
    //                         $("#" + current_element)
    //                             .find(".condition_true")
    //                             .on("click", attachOutputElement)
    //                             .trigger("click");
    //                         $("#" + response_path[i]["next_true_element_id"])
    //                             .find(".element_change_input")
    //                             .on("click", attachInputElement)
    //                             .trigger("click");
    //                     }
    //                 }
    //                 $(".drop-pad-element").on("click", elementProperties);
    //             }
    //         }
    //     },
    //     error: function (xhr, status, error) {
    //         console.error(error);
    //     },
    // });

    function attachOutputElement(e) {
        if (inputElement == null && outputElement == null) {
            var attachDiv = $(this);
            attachDiv.addClass("selected");
            if (attachDiv.hasClass("condition_true")) {
                condition = "True";
            } else if (attachDiv.hasClass("condition_false")) {
                condition = "False";
            } else {
                condition = "";
            }
            outputElement = attachDiv.closest(".element_item");
        }
    }

    function attachInputElement(e) {
        if (
            outputElement != null &&
            outputElement.attr("id") != $(this).parent().attr("id")
        ) {
            var attachDiv = $(this);
            attachDiv.addClass("selected");
            inputElement = attachDiv.closest(".element_item");
            if (outputElement && inputElement) {
                var outputElementId = outputElement.attr("id");
                var inputElementId = inputElement.attr("id");
                if (condition == "True") {
                    var attachOutputElement = $(outputElement).find(
                        ".element_change_output.condition_true"
                    );
                } else if (condition == "False") {
                    var attachOutputElement = $(outputElement).find(
                        ".element_change_output.condition_false"
                    );
                } else {
                    $("#" + inputElementId).css({
                        border: "1px solid red",
                    });
                }
                var attachInputElement = $(inputElement).find(
                    ".element_change_input"
                );
                if (attachInputElement && attachOutputElement) {
                    var lineId =
                        outputElement.attr("id") +
                        "-to-" +
                        inputElement.attr("id");
                    $("body").append(
                        '<div class="line" id="' + lineId + '"></div>'
                    );
                    var x1 =
                        attachOutputElement.offset().left +
                        attachOutputElement.outerWidth() / 2;
                    var y1 =
                        attachOutputElement.offset().top +
                        attachOutputElement.outerHeight() / 2;
                    var x2 =
                        attachInputElement.offset().left +
                        attachInputElement.outerWidth() / 2;
                    var y2 =
                        attachInputElement.offset().top +
                        attachInputElement.outerHeight() / 2;
                    var distance = Math.sqrt(
                        Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)
                    );
                    var extra_left =
                        ($(".drop-pad").outerWidth(true) -
                            $(".drop-pad").width()) /
                        2;
                    var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                    var line = $("#" + lineId);
                    line.css({
                        width: distance + "px",
                        transform: "rotate(" + angle + "deg)",
                        top: y1 + "px",
                        left: x1 + extra_left + "px",
                    });
                    inputElement = null;
                    outputElement = null;
                }
            }
        }
    }

    function elementProperties(e) {
        var item_name = $(this).find(".item_name").text();
        var list_icon = $(this).find(".list-icon").html();
        var item_id = $(this).attr("id");
        var name_html = "";
        $.ajax({
            url: getCampaignElementPath.replace(":element_id", item_id),
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    name_html += '<div class="element_properties">';
                    name_html +=
                        '<div class="element_name" data-bs-target="' +
                        item_id +
                        '">' +
                        list_icon +
                        "<p>" +
                        item_name +
                        "</p></div>";
                    properties = response.properties;
                    for (var i = 0; i < properties.length; i++) {
                        name_html += '<div class="property_item">';
                        name_html +=
                            "<p>" +
                            properties[i]["original_properties"][
                                "property_name"
                            ] +
                            ": <br>";
                        name_html +=
                            ' <span style="font-style: italic;">' +
                            properties[i]["value"] +
                            "</span></p>";
                        name_html += "</div>";
                    }
                    name_html += "</div>";
                } else {
                    name_html += '<div class="element_properties">';
                    name_html +=
                        '<div class="element_name">' +
                        list_icon +
                        "<p>" +
                        item_name +
                        "</p></div>";
                    name_html +=
                        '<div class="text-center"> Not Found </div></div>';
                }
                $(".drop-pad-element").css({
                    border: "none",
                });
                $("#" + item_id).css({
                    "z-index": "999999",
                    border: "1px solid #16adcb",
                });
                $("#properties").html(name_html);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }
});
