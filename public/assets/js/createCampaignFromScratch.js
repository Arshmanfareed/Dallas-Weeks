$(document).ready(function () {
    var settings = JSON.parse(sessionStorage.getItem("settings"));
    var choosedElement = null;
    var inputElement = null;
    var outputElement = null;
    var elements_array = sessionStorage.getItem("elements_array");
    var elements_data_array = JSON.parse(sessionStorage.getItem("elements_data_array") || "{}");
    sessionStorage.setItem("elements_data_array", JSON.stringify(elements_data_array));
    var condition = "";

    if (elements_array) {
        elements_array = JSON.parse(elements_array);
        var maxDropPadHeight = 0;
        for (var key in elements_array) {
            if (elements_array.hasOwnProperty(key) && key != "step-1") {
                var value = elements_array[key];
                var hyphenIndex = key.lastIndexOf("_");
                var new_key = key.slice(0, hyphenIndex);
                var clone = $("#" + new_key).clone();
                clone.css({
                    position: "absolute",
                });
                clone.attr("id", key);
                clone.addClass("drop_element");
                clone.addClass("drop-pad-element");
                clone.addClass("placedElement");
                clone.removeClass("drop_element");
                clone.removeClass("element");
                $(".task-list").append(clone);
                $(".cancel-icon").on("click", removeElement);
                $(".element_change_output").on("click", attachOutputElement);
                $(".element_change_input").on("click", attachInputElement);
                $(".drop-pad-element").on("click", elementProperties);
                if (elements_data_array.hasOwnProperty(key)) {
                    var element_data = elements_data_array[key];
                    for (var prop_key in element_data) {
                        $("#loader").show();
                        $.ajax({
                            url: getPropertyRequiredPath.replace(
                                ":id",
                                prop_key
                            ),
                            async: false,
                            type: "GET",
                            success: function (response) {
                                if (response.success) {
                                    var property = response.property;
                                    if (property["property_name"] == "Days") {
                                        if (
                                            elements_data_array[key][
                                            prop_key
                                            ] == ""
                                        ) {
                                            clone.find(".item_days").html("0");
                                        } else {
                                            clone
                                                .find(".item_days")
                                                .html(
                                                    elements_data_array[key][
                                                    prop_key
                                                    ]
                                                );
                                        }
                                    } else if (
                                        property["property_name"] == "Hours"
                                    ) {
                                        if (
                                            elements_data_array[key][
                                            prop_key
                                            ] == ""
                                        ) {
                                            clone.find(".item_hours").html("0");
                                        } else {
                                            clone
                                                .find(".item_hours")
                                                .html(
                                                    elements_data_array[key][
                                                    prop_key
                                                    ]
                                                );
                                        }
                                    }
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText);
                            },
                            complete: function () {
                                $("#loader").hide();
                            }
                        });
                    }
                }
                clone.on("mousedown", startDragging);
                clone.css({
                    left: value["position_x"] - 214,
                    top: value["position_y"] - 345,
                    border: "none",
                });
                var newDropPadHeight =
                    parseInt(clone.css("top")) +
                    parseInt(clone.css("height")) +
                    30;
                if (maxDropPadHeight < newDropPadHeight) {
                    maxDropPadHeight = newDropPadHeight;
                    $(".drop-pad").css("height", maxDropPadHeight + "px");
                }
            }
        }

        for (var key in elements_array) {
            current_element = key;
            $("#" + current_element)
                .find(".attach-elements-out")
                .removeClass("selected");
            if (elements_array[current_element]["0"] != "") {
                $("#" + current_element)
                    .find(".condition_false")
                    .on("click", function (e) {
                        e.stopPropagation();
                        attachOutputElement();
                    })
                    .trigger("click");
                $("#" + elements_array[current_element]["0"])
                    .find(".element_change_input")
                    .on("click", function (e) {
                        e.stopPropagation();
                        attachInputElement();
                    })
                    .trigger("click");
            }
            if (elements_array[current_element]["1"] != "") {
                $("#" + current_element)
                    .find(".condition_true")
                    .on("click", function (e) {
                        e.stopPropagation();
                        attachOutputElement();
                    })
                    .trigger("click");
                $("#" + elements_array[current_element]["1"])
                    .find(".element_change_input")
                    .on("click", function (e) {
                        e.stopPropagation();
                        attachInputElement();
                    })
                    .trigger("click");
            }
            $("#" + current_element).css({
                left: "-=20px",
            });
            if ($("#" + current_element).width() > 365) {
                $("#" + current_element).css({
                    left: "-=10px",
                });
            }
            $("#properties").removeClass("active");
            $("#properties-btn").removeClass("active");
            $("#element-list-btn").addClass("active");
            $("#element-list").addClass("active");
        }
    } else {
        elements_array = {};
        elements_data_array = {};
        elements_array["step-1"] = {};
        elements_array["step-1"][0] = "";
        elements_array["step-1"][1] = "";
        sessionStorage.setItem(
            "elements_array",
            JSON.stringify(elements_array)
        );
        sessionStorage.setItem(
            "elements_data_array",
            JSON.stringify(elements_data_array)
        );
    }

    placeElement();
    $(".element_change_output").on("click", attachOutputElement);

    function placeElement(e) {
        $(".element").on("mousedown", function (e) {
            e.preventDefault();
            var clone = $(this).clone().css({
                position: "absolute",
            });
            $("body").append(clone);
            choosedElement = clone;
            id =
                choosedElement.attr("id") +
                "_" +
                Math.floor(10000 + Math.random() * 90000);
            choosedElement.attr("id", id);
            choosedElement.addClass("drop_element");
            choosedElement.addClass("drop-pad-element");
            choosedElement.removeClass("element");
            $(document).on("mousemove", function (e) {
                var x = e.pageX;
                var y = e.pageY;
                var element = $(".drop-pad").offset();
                var element_x = element.left;
                var max_x =
                    element_x +
                    $(".drop-pad").outerWidth() -
                    choosedElement.width();
                var element_y = element.top;
                var max_y =
                    element_y +
                    $(".drop-pad").outerHeight() -
                    choosedElement.height();
                if (x < element_x && y < element_y) {
                    choosedElement.css({
                        left: element_x,
                        top: element_y,
                    });
                } else if (x < element_x && y > max_y) {
                    choosedElement.css({
                        left: element_x,
                        top: max_y - 20,
                    });
                    var newDropPadHeight =
                        $(".drop-pad").height() + choosedElement.height();
                    $(".drop-pad").css("height", newDropPadHeight + "px");
                    var choosedElementOffset = choosedElement.offset();
                    window.scrollTo({
                        top: choosedElementOffset.top,
                        left: choosedElementOffset.left,
                    });
                } else if (x < element_x && y > element_y && y < max_y) {
                    choosedElement.css({
                        left: element_x,
                        top: y,
                    });
                } else if (y < element_y && x > element_x && x < max_x) {
                    choosedElement.css({
                        left: x,
                        top: element_y,
                    });
                } else if (y > max_y && x > element_x && x < max_x) {
                    choosedElement.css({
                        left: element_x,
                        top: max_y - 20,
                    });
                    var newDropPadHeight =
                        $(".drop-pad").height() + choosedElement.height();
                    $(".drop-pad").css("height", newDropPadHeight + "px");
                    var choosedElementOffset = choosedElement.offset();
                    window.scrollTo({
                        top: choosedElementOffset.top,
                        left: choosedElementOffset.left,
                    });
                } else if (
                    x > element_x &&
                    x < max_x &&
                    y > element_y &&
                    y < max_y
                ) {
                    choosedElement.css({
                        left: x,
                        top: y,
                    });
                } else if (x > max_x && y > max_y) {
                    choosedElement.css({
                        left: element_x - 20,
                        top: max_y - 20,
                    });
                    var newDropPadHeight =
                        $(".drop-pad").height() + choosedElement.height();
                    $(".drop-pad").css("height", newDropPadHeight + "px");
                    var choosedElementOffset = choosedElement.offset();
                    window.scrollTo({
                        top: choosedElementOffset.top,
                        left: choosedElementOffset.left,
                    });
                } else if (x > max_x && y < element_y) {
                    choosedElement.css({
                        left: max_x - 20,
                        top: element_y,
                    });
                } else if (x > max_x && y > element_y && y < max_y) {
                    choosedElement.css({
                        left: max_x - 20,
                        top: y,
                    });
                } else {
                    choosedElement.css({
                        left: element_x,
                        top: element_y,
                    });
                }
            });
        });

        $(document).on("mouseup", function (e) {
            if (choosedElement) {
                choosedElement.addClass("placedElement");
                choosedElement.removeClass("drop_element");
                var x = e.pageX;
                var y = e.pageY;
                var element = $(".drop-pad").offset();
                var element_x = element.left;
                var max_x =
                    element_x +
                    $(".drop-pad").outerWidth() -
                    choosedElement.width();
                var element_y = element.top;
                var max_y =
                    element_y +
                    $(".drop-pad").outerHeight() -
                    choosedElement.height();
                if (x < element_x && y < element_y) {
                    choosedElement.css({
                        left: 0,
                        top: 0,
                    });
                } else if (x < element_x && y > max_y) {
                    choosedElement.css({
                        left: 0,
                        top: max_y - 310,
                    });
                } else if (x > max_x && y > max_y) {
                    choosedElement.css({
                        left: max_x - 130,
                        top: max_y - 310,
                    });
                } else if (x < element_x && y > element_y && y < max_y) {
                    choosedElement.css({
                        left: 0,
                        top: y - 330,
                    });
                } else if (y < element_y && x > element_x && x < max_x) {
                    choosedElement.css({
                        left: x - 210,
                        top: 0,
                    });
                } else if (y > max_y && x > element_x && x < max_x) {
                    choosedElement.css({
                        left: x - 210,
                        top: max_y - 310,
                    });
                } else if (
                    x > element_x &&
                    x < max_x &&
                    y > element_y &&
                    y < max_y
                ) {
                    choosedElement.css({
                        left: x - 210,
                        top: y - 330,
                    });
                } else if (x > max_x && y < element_y) {
                    choosedElement.css({
                        left: max_x - 130,
                        top: 0,
                    });
                } else if (x > max_x && y > element_y && y < max_y) {
                    choosedElement.css({
                        left: max_x - 130,
                        top: y - 330,
                    });
                } else {
                    choosedElement.css({
                        left: 0,
                        top: 0,
                    });
                }
                $(document).off("mousemove");
                $(".task-list").append(choosedElement);
                $(".cancel-icon").on("click", removeElement);
                $(".element_change_output").on("click", attachOutputElement);
                $(".element_change_input").on("click", attachInputElement);
                $(".drop-pad-element").on("click", elementProperties);
                choosedElement.on("mousedown", startDragging);
                id = choosedElement.attr("id");
                elements_array[id] = {};
                elements_array[id][0] = "";
                elements_array[id][1] = "";
                elements_array[id]["position_x"] = choosedElement.offset().left;
                elements_array[id]["position_y"] = choosedElement.offset().top;
                sessionStorage.setItem(
                    "elements_array",
                    JSON.stringify(elements_array)
                );
                sessionStorage.setItem(
                    "elements_data_array",
                    JSON.stringify(elements_data_array)
                );
                choosedElement = null;
            }
        });
    }

    function removeElement(e) {
        var element = $(this).parent();
        var id = element.attr("id");
        if (elements_array[id]) {
            var next_false = elements_array[id][0];
            if (next_false != "") {
                next_element = $("#" + next_false).find(
                    ".element_change_input"
                );
                next_element.closest(".selected").removeClass("selected");
            }
            $("#" + id + "-to-" + next_false).remove();
            var next_true = elements_array[id][1];
            if (next_true != "") {
                next_element = $("#" + next_true).find(".element_change_input");
                next_element.closest(".selected").removeClass("selected");
            }
            $("#" + id + "-to-" + next_true).remove();
        }
        var prev = find_element(id);
        if (elements_array[prev]) {
            if (elements_array[prev][0] == id) {
                var prev_element = $("#" + prev).find(
                    ".element_change_output.condition_false"
                );
                prev_element.closest(".selected").removeClass("selected");
                elements_array[prev][0] = "";
            } else if (elements_array[prev][1] == id) {
                var prev_element = $("#" + prev).find(
                    ".element_change_output.condition_true"
                );
                prev_element.closest(".selected").removeClass("selected");
                elements_array[prev][1] = "";
            }
            $("#" + prev + "-to-" + id).remove();
        }
        delete elements_array[id];
        delete elements_data_array[id];
        sessionStorage.setItem(
            "elements_array",
            JSON.stringify(elements_array)
        );
        sessionStorage.setItem(
            "elements_data_array",
            JSON.stringify(elements_data_array)
        );
        $(this).parent().remove();
        $(".element-content").removeClass("active");
        $("#element-list").addClass("active");
        $(".element-btn").removeClass("active");
        $("#element-list-btn").addClass("active");
    }

    function removePath(e) {
        var element = $(this).parent().attr("id");
        var index = element.indexOf("-to-");
        var prev_element_id = element.substring(0, index);
        var prev_element = $("#" + prev_element_id);
        var next_element_id = element.substring(index + 4);
        var next_element = $("#" + next_element_id);
        next_element = next_element.find(".element_change_input");
        next_element.closest(".selected").removeClass("selected");
        if (elements_array[prev_element_id][0] == next_element_id) {
            elements_array[prev_element_id][0] = "";
            prev_element = prev_element.find(
                ".element_change_output.condition_false"
            );
            prev_element.closest(".selected").removeClass("selected");
        } else if (elements_array[prev_element_id][1] == next_element_id) {
            elements_array[prev_element_id][1] = "";
            prev_element = prev_element.find(
                ".element_change_output.condition_true"
            );
            prev_element.closest(".selected").removeClass("selected");
        }
        sessionStorage.setItem(
            "elements_array",
            JSON.stringify(elements_array)
        );
        sessionStorage.setItem(
            "elements_data_array",
            JSON.stringify(elements_data_array)
        );
        $(this).parent().remove();
    }

    function attachOutputElement(e) {
        if (!$(this).hasClass("selected")) {
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
    }

    function attachInputElement(e) {
        if (!$(this).hasClass("selected")) {
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
                        elements_array[outputElementId][1] = inputElementId;
                        var attachOutputElement = $(outputElement).find(
                            ".element_change_output.condition_true"
                        );
                    } else if (condition == "False") {
                        elements_array[outputElementId][0] = inputElementId;
                        var attachOutputElement = $(outputElement).find(
                            ".element_change_output.condition_false"
                        );
                    } else {
                        $("#" + inputElementId).css({
                            border: "1px solid red",
                        });
                    }
                    $(".drop-pad").append(
                        '<div class="line" id="' +
                        outputElement.attr("id") +
                        "-to-" +
                        inputElement.attr("id") +
                        '"><div class="path-cancel-icon"><i class="fa-solid fa-xmark"></i></div></div>'
                    );
                    $(".path-cancel-icon").on("click", removePath);
                    var attachInputElement = $(inputElement).find(
                        ".element_change_input"
                    );
                    if (attachInputElement && attachOutputElement) {
                        var inputPosition = attachInputElement.offset();
                        var outputPosition = attachOutputElement.offset();

                        var x1 = inputPosition.left;
                        var y1 = inputPosition.top;
                        var x2 = outputPosition.left;
                        var y2 = outputPosition.top;

                        var distance = Math.sqrt(
                            Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)
                        );
                        var angle =
                            Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);

                        var lineId =
                            outputElement.attr("id") +
                            "-to-" +
                            inputElement.attr("id");
                        var line = $("#" + lineId);
                        line.css({
                            width: distance + "px",
                            transform: "rotate(" + angle + "deg)",
                            top: y1 - 320 + "px",
                            left: x1 - 207 + "px",
                        });
                        inputElement = null;
                        outputElement = null;
                    }
                }
                sessionStorage.setItem(
                    "elements_array",
                    JSON.stringify(elements_array)
                );
                sessionStorage.setItem(
                    "elements_data_array",
                    JSON.stringify(elements_data_array)
                );
            }
        }
    }

    $(".element-btn").on("click", function () {
        var targetTab = $(this).data("tab");
        $(".element-content").removeClass("active");
        $("#" + targetTab).addClass("active");
        $(".element-btn").removeClass("active");
        $(this).addClass("active");
    });

    $("#save-changes").on("click", function () {
        html2canvas(document.getElementById("capture")).then(function (canvas) {
            var img = canvas.toDataURL();
            elements_array = JSON.parse(JSON.stringify(elements_array));
            elements_data_array = JSON.parse(
                JSON.stringify(elements_data_array)
            );
            $(".drop-pad-element .cancel-icon").css({
                display: "none",
            });
            $(".drop-pad-element").css({
                "z-index": "0",
                border: "none",
            });
            if (check_elements()) {
                $("#loader").show();
                $.ajax({
                    url: createCampaignPath,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/json",
                    data: JSON.stringify({
                        final_data: elements_data_array,
                        final_array: elements_array,
                        settings: campaign_details,
                        img_url: img,
                    }),
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (response) {
                        if (response.success) {
                            window.location = campaignsPath;
                        } else {
                            toastr.error(response.message);
                            console.log(response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $("#loader").hide();
                    }
                });
            }
        });
    });

    function elementProperties(e) {
        $("#element-list").removeClass("active");
        $("#properties").addClass("active");
        $("#element-list-btn").removeClass("active");
        $("#properties-btn").addClass("active");
        $(this).removeClass("error");
        $(this).find(".item_name").removeClass("error");
        $(".drop-pad-element .cancel-icon").css({
            display: "none",
        });
        $("#properties").empty();
        $(".drop-pad-element").css({
            "z-index": "0",
            border: "none",
        });
        $(this).css({
            "z-index": "999",
            border: "1px solid rgb(23, 172, 203)",
        });
        $(this).find(".cancel-icon").css({
            display: "flex",
        });
        $(this).find(".item_name").css({
            color: "#fff",
        });
        var item_slug = $(this).data("filterName");
        var item_name = $(this).find(".item_name").text();
        var list_icon = $(this).find(".list-icon").html();
        var item_id = $(this).attr("id");
        var name_html = "";
        if (elements_data_array[item_id] == null) {
            $("#loader").show();
            $.ajax({
                url: getCampaignElementPath.replace(":slug", item_slug),
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
                        arr = {};
                        response.properties.forEach((property) => {
                            name_html += '<div class="property_item">';
                            name_html +=
                                "<p>" + property["property_name"] + "</p>";
                            name_html +=
                                '<input type="' +
                                property["data_type"] +
                                '" placeholder="Enter the ' +
                                property["property_name"] +
                                '" class="property_input" name="' +
                                property["id"] +
                                '"';
                            if (property["optional"] == 1) {
                                name_html += "required";
                            }
                            name_html += ">";
                            name_html += "</div>";
                            arr[property["id"]] = "";
                        });
                        elements_data_array[item_id] = arr;
                        sessionStorage.setItem(
                            "elements_data_array",
                            JSON.stringify(elements_data_array)
                        );
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
                            '<div class="text-center">' +
                            response.message +
                            "</div></div>";
                    }
                    $("#properties").html(name_html);
                    $(".property_input").on("input", propertyInput);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                },
                complete: function () {
                    $("#loader").hide();
                }
            });
        } else {
            name_html += '<div class="element_properties">';
            name_html +=
                '<div class="element_name" data-bs-target="' +
                item_id +
                '">' +
                list_icon +
                "<p>" +
                item_name +
                "</p></div>";
            elements = elements_data_array[item_id];
            var ajaxRequests = [];
            for (const key in elements) {
                ajaxRequests.push(
                    $.ajax({
                        url: getPropertyDatatypePath
                            .replace(":id", key)
                            .replace(":element_slug", item_slug),
                        type: "GET",
                        dataType: "json",
                    }).then(function (response) {
                        if (response.success) {
                            const value = elements[key];
                            name_html += '<div class="property_item">';
                            name_html +=
                                "<p>" +
                                response.property["property_name"] +
                                "</p>";
                            name_html +=
                                '<input type="' +
                                response.property["data_type"];
                            if (value == "") {
                                name_html +=
                                    '" placeholder="Enter the ' +
                                    response.property["property_name"] +
                                    '" class="property_input" name="' +
                                    key +
                                    '"';
                            } else {
                                name_html +=
                                    '" value="' +
                                    value +
                                    '" class="property_input"';
                            }
                            if (response.optional == "1") {
                                name_html += "required";
                            }
                            name_html += ">";
                            name_html += "</div>";
                        } else {
                            name_html += '<div class="property_item">';
                            name_html += "<p>" + key + "</p>";
                            name_html +=
                                '<input type="text" placeholder="' +
                                value +
                                '" class="property_input" name="' +
                                key +
                                '">';
                            name_html += "</div>";
                        }
                    })
                );
            }
            $.when.apply($, ajaxRequests).then(function () {
                name_html += "</div>";
                $("#properties").html(name_html);
                $(".property_input").on("input", propertyInput);
            });
        }
    }

    function propertyInput(e) {
        var element_id = $(this)
            .parent()
            .parent()
            .find(".element_name")
            .data("bs-target");
        if (element_id != undefined) {
            var properties = $(this).attr("name");
            elements_data_array[element_id][properties] = $(this).val();
            sessionStorage.setItem(
                "elements_data_array",
                JSON.stringify(elements_data_array)
            );
            $("#" + element_id).css({
                border: "1px solid rgb(23, 172, 203)",
            });
            $("#" + element_id)
                .find(".item_name")
                .css({
                    color: "#fff",
                });
            if ($(this).parent().find("p").text() == "Days") {
                if ($(this).val() != "") {
                    $("#" + element_id)
                        .find(".item_days")
                        .html($(this).val());
                } else {
                    $("#" + element_id)
                        .find(".item_days")
                        .html(0);
                }
            } else if ($(this).parent().find("p").text() == "Hours") {
                if ($(this).val() != "") {
                    $("#" + element_id)
                        .find(".item_hours")
                        .html($(this).val());
                } else {
                    $("#" + element_id)
                        .find(".item_hours")
                        .html(0);
                }
            }
        }
    }

    function startDragging(e) {
        e.preventDefault();
        var currentElement = $(this);
        $(document).on("mousemove", function (e) {
            var x = e.pageX;
            var y = e.pageY;
            var element = $(".drop-pad").offset();
            var element_x = element.left;
            var max_x =
                element_x +
                $(".drop-pad").outerWidth() -
                currentElement.width();
            var element_y = element.top;
            var max_y =
                element_y +
                $(".drop-pad").outerHeight() -
                currentElement.height();
            currentElement.find(".cancel-icon").css({
                display: "none",
            });
            if (x < element_x && y < element_y) {
                currentElement.css({
                    left: 0,
                    top: 0,
                    border: "none",
                });
            } else if (x < element_x && y > max_y) {
                currentElement.css({
                    left: 0,
                    top: max_y - 310,
                    border: "none",
                });
                var newDropPadHeight =
                    $(".drop-pad").height() + currentElement.height();
                $(".drop-pad").css("height", newDropPadHeight + "px");
                var currentElementOffset = currentElement.offset();
                window.scrollTo({
                    top: currentElementOffset.top,
                    left: currentElementOffset.left,
                });
            } else if (x > max_x && y > max_y) {
                currentElement.css({
                    left: max_x - 240,
                    top: max_y - 310,
                    border: "none",
                });
                var newDropPadHeight =
                    $(".drop-pad").height() + currentElement.height();
                $(".drop-pad").css("height", newDropPadHeight + "px");
                var currentElementOffset = currentElement.offset();
                window.scrollTo({
                    top: currentElementOffset.top,
                    left: currentElementOffset.left,
                });
            } else if (x < element_x && y > element_y && y < max_y) {
                currentElement.css({
                    left: 0,
                    top: y - 350,
                    border: "none",
                });
            } else if (y < element_y && x > element_x && x < max_x) {
                currentElement.css({
                    left: x - 210,
                    top: 0,
                    border: "none",
                });
            } else if (y > max_y && x > element_x && x < max_x) {
                currentElement.css({
                    left: x - 210,
                    top: max_y - 350,
                    border: "none",
                });
                var newDropPadHeight =
                    $(".drop-pad").height() + currentElement.height();
                $(".drop-pad").css("height", newDropPadHeight + "px");
                var currentElementOffset = currentElement.offset();
                window.scrollTo({
                    top: currentElementOffset.top,
                    left: currentElementOffset.left,
                });
            } else if (
                x > element_x &&
                x < max_x &&
                y > element_y &&
                y < max_y
            ) {
                currentElement.css({
                    left: x - 210,
                    top: y - 350,
                    border: "none",
                });
            } else if (x > max_x && y < element_y) {
                currentElement.css({
                    left: max_x - 240,
                    top: 0,
                    border: "none",
                });
            } else if (x > max_x && y > element_y && y < max_y) {
                currentElement.css({
                    left: max_x - 240,
                    top: y - 350,
                    border: "none",
                });
            } else {
                currentElement.css({
                    left: 0,
                    top: 0,
                    border: "none",
                });
            }
            id = currentElement.attr("id");
            elements_array[id]["position_x"] = currentElement.offset().left;
            elements_array[id]["position_y"] = currentElement.offset().top;
            sessionStorage.setItem(
                "elements_array",
                JSON.stringify(elements_array)
            );
            sessionStorage.setItem(
                "elements_data_array",
                JSON.stringify(elements_data_array)
            );
            var current_element_id = currentElement.attr("id");
            var next_false_element_id = elements_array[current_element_id][0];
            var next_true_element_id = elements_array[current_element_id][1];
            var prev_element_id = find_element(currentElement.attr("id"));
            if (prev_element_id && current_element_id) {
                if (
                    $(".drop-pad").find(
                        "#" + prev_element_id + "-to-" + current_element_id
                    ).length > 0
                ) {
                    var attachInputElement = $("#" + current_element_id).find(
                        ".element_change_input"
                    );
                    var attachOutputElement;
                    if (
                        elements_array[prev_element_id][0] == current_element_id
                    ) {
                        attachOutputElement = $("#" + prev_element_id).find(
                            ".element_change_output.condition_false"
                        );
                    } else if (
                        elements_array[prev_element_id][1] == current_element_id
                    ) {
                        attachOutputElement = $("#" + prev_element_id).find(
                            ".element_change_output.condition_true"
                        );
                    }
                    if (
                        attachInputElement.length &&
                        attachOutputElement.length
                    ) {
                        var inputPosition = attachInputElement.offset();
                        var outputPosition = attachOutputElement.offset();
                        var x1 = inputPosition.left;
                        var y1 = inputPosition.top;
                        var x2 = outputPosition.left;
                        var y2 = outputPosition.top;
                        var distance = Math.sqrt(
                            Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)
                        );
                        var angle =
                            Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                        var lineId =
                            prev_element_id + "-to-" + current_element_id;
                        var line = $("#" + lineId);
                        line.css({
                            width: distance + "px",
                            transform: "rotate(" + angle + "deg)",
                            top: y1 - 320 + "px",
                            left: x1 - 207 + "px",
                        });
                    }
                }
            }
            if (current_element_id && next_true_element_id) {
                if (
                    $(".drop-pad").find(
                        "#" + current_element_id + "-to-" + next_true_element_id
                    ).length > 0
                ) {
                    var attachInputElement = $("#" + next_true_element_id).find(
                        ".element_change_input"
                    );
                    var attachOutputElement = $("#" + current_element_id).find(
                        ".element_change_output.condition_true"
                    );
                    if (
                        attachInputElement.length &&
                        attachOutputElement.length
                    ) {
                        var inputPosition = attachInputElement.offset();
                        var outputPosition = attachOutputElement.offset();

                        var x1 = inputPosition.left;
                        var y1 = inputPosition.top;
                        var x2 = outputPosition.left;
                        var y2 = outputPosition.top;

                        var distance = Math.sqrt(
                            Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)
                        );
                        var angle =
                            Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                        var lineId =
                            current_element_id + "-to-" + next_true_element_id;
                        var line = $("#" + lineId);
                        line.css({
                            width: distance + "px",
                            transform: "rotate(" + angle + "deg)",
                            top: y1 - 320 + "px",
                            left: x1 - 207 + "px",
                        });
                    }
                }
            }
            if (current_element_id && next_false_element_id) {
                if (
                    $(".drop-pad").find(
                        "#" +
                        current_element_id +
                        "-to-" +
                        next_false_element_id
                    ).length > 0
                ) {
                    var attachInputElement = $(
                        "#" + next_false_element_id
                    ).find(".element_change_input");
                    var attachOutputElement = $("#" + current_element_id).find(
                        ".element_change_output.condition_false"
                    );
                    if (
                        attachInputElement.length &&
                        attachOutputElement.length
                    ) {
                        var inputPosition = attachInputElement.offset();
                        var outputPosition = attachOutputElement.offset();

                        var x1 = inputPosition.left;
                        var y1 = inputPosition.top;
                        var x2 = outputPosition.left;
                        var y2 = outputPosition.top;

                        var distance = Math.sqrt(
                            Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)
                        );
                        var angle =
                            Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                        var lineId =
                            current_element_id + "-to-" + next_false_element_id;
                        var line = $("#" + lineId);
                        line.css({
                            width: distance + "px",
                            transform: "rotate(" + angle + "deg)",
                            top: y1 - 320 + "px",
                            left: x1 - 207 + "px",
                        });
                    }
                }
            }
        });
        $(document).on("mouseup", function () {
            $(document).off("mousemove");
        });
    }

    function find_element(element_id) {
        for (var key in elements_array) {
            if (
                elements_array[key][0] == element_id ||
                elements_array[key][1] == element_id
            ) {
                return key;
            }
        }
    }

    function capitalize(str) {
        if (typeof str !== "string") {
            return "";
        }
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    function check_elements() {
        for (var key in elements_array) {
            if (key !== "step-1") {
                if (find_element(key) == undefined) {
                    $("#" + key).addClass("error");
                    $("#" + key)
                        .find(".item_name")
                        .addClass("error");
                    key = key.replace(/[0-9]/g, "");
                    key = key.replace(/_/g, " ");
                    key = capitalize(key);
                    toastr.error(
                        key + " is not connected as campaign sequence."
                    );
                    return false;
                } else {
                    var element_data = elements_data_array[key];
                    for (var prop_key in element_data) {
                        var errorOccurred = false;
                        $("#loader").show();
                        $.ajax({
                            url: getPropertyRequiredPath.replace(
                                ":id",
                                prop_key
                            ),
                            async: false,
                            type: "GET",
                            success: function (response) {
                                if (response.success) {
                                    var property = response.property;
                                    if (
                                        element_data[prop_key] == "" &&
                                        property["optional"] == 1
                                    ) {
                                        $("#" + key).addClass("error");
                                        $("#" + key)
                                            .find(".item_name")
                                            .addClass("error");
                                        toastr.error(
                                            property["property_name"] +
                                            " is not filled as required."
                                        );
                                        errorOccurred = true;
                                    }
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText);
                            },
                            complete: function () {
                                $("#loader").hide();
                            }
                        });
                        if (errorOccurred) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
});
