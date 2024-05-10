$(document).ready(function () {
    var choosedElement = null;
    var inputElement = null;
    var outputElement = null;
    var elements_array = localStorage.getItem("elements_array");
    var elements_data_array = localStorage.getItem("elements_data_array");
    var condition = "";

    if (elements_data_array) {
        elements_data_array = JSON.parse(elements_data_array);
    } else {
        elements_data_array = {};
        localStorage.setItem(
            "elements_data_array",
            JSON.stringify(elements_data_array)
        );
    }

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
                if (
                    elements_data_array[key] &&
                    elements_data_array[key]["Days"]
                ) {
                    clone
                        .find(".item_days")
                        .html(elements_data_array[key]["Days"]);
                } else {
                    clone.find(".item_days").html("0");
                }
                if (
                    elements_data_array[key] &&
                    elements_data_array[key]["Hours"]
                ) {
                    clone
                        .find(".item_hours")
                        .html(elements_data_array[key]["Hours"]);
                } else {
                    clone.find(".item_hours").html("0");
                }
                clone.on("mousedown", startDragging);
                var top = value["position_y"];
                var step1_top = $("#step-1").position().top;
                var subtract = $('.custom-center').outerHeight(true) + $("#step-1").outerHeight(true) + $('.drop-pad h5').outerHeight(true);
                if (top - subtract < 0) {
                    top = 0;
                } else {
                    top = top - subtract;
                }
                // var left = value["position_x"];
                // var step1_left = $("#step-1").position().left;
                // console.log(step1_left);
                // var subtract = parseInt(elements_array[0]["position_x"]) - step1_left;
                // if (parseInt(left) - subtract < 0) {
                //     left = 0;
                // } else if (
                //     parseInt(left) + $(clone).width() <
                //     $(".drop-pad").width()
                // ) {
                //     left = parseInt(left) - subtract;
                // } else {
                //     left =
                //         $(".drop-pad").width() - $(clone).width() - step1_top;
                // }
                clone.css({
                    left: value["position_x"] - 214,
                    top: value["position_y"] - 345,
                    border: "none",
                });
                console.log(elements_array);
                // var newDropPadHeight =
                //     parseInt(clone.css("top")) +
                //     parseInt(clone.css("height")) +
                //     30;
                // if (maxDropPadHeight < newDropPadHeight) {
                //     maxDropPadHeight = newDropPadHeight;
                //     $(".drop-pad").css("height", maxDropPadHeight + "px");
                // }
            }
        }

        // for (var key in elements_array) {
        //     current_element = key;
        //     if (elements_array[current_element]["0"] != "") {
        //         $("#" + current_element)
        //             .find(".condition_false")
        //             .on("click", function (e) {
        //                 e.stopPropagation();
        //                 attachOutputElement();
        //             })
        //             .trigger("click");
        //         $("#" + elements_array[current_element]["0"])
        //             .find(".element_change_input")
        //             .on("click", function (e) {
        //                 e.stopPropagation();
        //                 attachInputElement();
        //             })
        //             .trigger("click");
        //     }
        //     if (elements_array[current_element]["1"] != "") {
        //         $("#" + current_element)
        //             .find(".condition_true")
        //             .on("click", function (e) {
        //                 e.stopPropagation();
        //                 attachOutputElement();
        //             })
        //             .trigger("click");
        //         $("#" + elements_array[current_element]["1"])
        //             .find(".element_change_input")
        //             .on("click", function (e) {
        //                 e.stopPropagation();
        //                 attachInputElement();
        //             })
        //             .trigger("click");
        //     }
        //     $("#" + current_element).css({
        //         left: "-=20px",
        //     });
        //     if ($("#" + current_element).width() > 365) {
        //         $("#" + current_element).css({
        //             left: "-=10px",
        //         });
        //     }
        //     $("#properties").removeClass("active");
        //     $("#properties-btn").removeClass("active");
        //     $("#element-list-btn").addClass("active");
        //     $("#element-list").addClass("active");
        // }
    } else {
        elements_array = {};
        elements_data_array = {};
        elements_array["step-1"] = {};
        elements_array["step-1"][0] = "";
        elements_array["step-1"][1] = "";
        localStorage.setItem("elements_array", JSON.stringify(elements_array));
        localStorage.setItem(
            "elements_data_array",
            JSON.stringify(elements_data_array)
        );
    }

    placeElement();
    $(".element_change_output").on("click", attachOutputElement);

    $(".placedElement").css({
        border: "none",
    });

    $(".placedElement .cancel-icon").css({
        display: "none",
    });

    function placeElement(e) {
        $(".element").on("mousedown", function (e) {
            e.preventDefault();
            var clone = $(this).clone().css({
                position: "absolute",
            });
            $("body").append(clone);
            choosedElement = clone;
            var id = choosedElement.attr("id") + "_" + Math.floor(10000 + Math.random() * 90000);
            choosedElement.attr("id", id);
            choosedElement.addClass("drop_element");
            choosedElement.addClass("drop-pad-element");
            choosedElement.removeClass("element");
            $(document).on("mousemove", function (e) {
                var cursor_x = e.pageX;
                var cursor_y = e.pageY;
                var drop_pad = $(".drop-pad").offset();
                var drop_pad_x = drop_pad.left;
                var drop_pad_max_x = drop_pad_x + $(".drop-pad").outerWidth();
                var drop_pad_y = drop_pad.top;
                var drop_pad_max_y = drop_pad_y + $(".drop-pad").outerHeight();
                if (cursor_y < drop_pad_y && (cursor_x > drop_pad_x && cursor_x < drop_pad_max_x)) {
                    var subtract = cursor_x - choosedElement.outerWidth(true);
                    if (subtract < drop_pad_x) {
                        choosedElement.css({
                            left: drop_pad_x,
                            top: drop_pad_y
                        });
                    } else {
                        choosedElement.css({
                            left: subtract,
                            top: drop_pad_y
                        });
                    }
                } else if (cursor_x < drop_pad_x && (cursor_y > drop_pad_y && cursor_y < drop_pad_max_y)) {
                    var subtract = cursor_y - choosedElement.outerHeight(true);
                    if (subtract < drop_pad_y) {
                        choosedElement.css({
                            left: drop_pad_x,
                            top: drop_pad_y
                        });
                    } else {
                        choosedElement.css({
                            left: drop_pad_x,
                            top: subtract
                        });
                    }
                } else if (cursor_x > drop_pad_max_x && (cursor_y > drop_pad_y && cursor_y < drop_pad_max_y)) {
                    var subtract = cursor_y - choosedElement.outerHeight(true);
                    if (subtract < drop_pad_y) {
                        choosedElement.css({
                            left: drop_pad_max_x - choosedElement.outerWidth(true),
                            top: drop_pad_y
                        });
                    } else {
                        choosedElement.css({
                            left: drop_pad_max_x - choosedElement.outerWidth(true),
                            top: subtract
                        });
                    }
                } else if (cursor_x < drop_pad_x && cursor_y < drop_pad_y) {
                    choosedElement.css({
                        left: drop_pad_x,
                        top: drop_pad_y
                    });
                } else if (cursor_y < drop_pad_y && cursor_x > drop_pad_max_x) {
                    choosedElement.css({
                        left: drop_pad_max_x - choosedElement.outerWidth(true),
                        top: drop_pad_y
                    });
                } else if (cursor_y > drop_pad_max_y) {
                    if (cursor_x > drop_pad_x && cursor_x < drop_pad_max_x) {
                        if (cursor_x + choosedElement.outerWidth(true) > drop_pad_max_x) {
                            choosedElement.css({
                                left: drop_pad_max_x - choosedElement.outerWidth(true),
                                top: drop_pad_max_y - choosedElement.outerHeight(true)
                            });
                        } else {
                            choosedElement.css({
                                left: cursor_x,
                                top: drop_pad_max_y - choosedElement.outerHeight(true),
                            });
                        }
                    } else if (cursor_x < drop_pad_x) {
                        choosedElement.css({
                            left: drop_pad_x,
                            top: drop_pad_max_y - choosedElement.outerHeight(true)
                        });
                    } else if (cursor_x > drop_pad_max_x) {
                        choosedElement.css({
                            left: drop_pad_max_x - choosedElement.outerWidth(true),
                            top: drop_pad_max_y - choosedElement.outerHeight(true)
                        });
                    }
                    var newDropPadHeight = $(".task-list").outerHeight() + choosedElement.outerHeight();
                    $(".task-list").css({
                        'height': newDropPadHeight,
                    });
                    $('#capture').scrollTop($('#capture')[0].scrollHeight);
                } else {
                    if (cursor_x + choosedElement.outerWidth(true) > drop_pad_max_x  && cursor_y + choosedElement.outerHeight(true) < drop_pad_max_y) {
                        choosedElement.css({
                            left: drop_pad_max_x - choosedElement.outerWidth(true),
                            top: cursor_y
                        });
                    } else if (cursor_y + choosedElement.outerHeight(true) > drop_pad_max_y && cursor_x + choosedElement.outerWidth(true) < drop_pad_max_x) {
                        choosedElement.css({
                            left: cursor_x,
                            top: drop_pad_max_y - choosedElement.outerHeight(true)
                        });
                    } else if (cursor_x + choosedElement.outerWidth(true) > drop_pad_max_x && cursor_y + choosedElement.outerHeight(true) > drop_pad_max_y) {
                        choosedElement.css({
                            left: drop_pad_max_x - choosedElement.outerWidth(true),
                            top: drop_pad_max_y - choosedElement.outerHeight(true)
                        });
                    } else {
                        choosedElement.css({
                            left: cursor_x,
                            top: cursor_y
                        });
                    }
                }
            });
        });

        $(document).on("mouseup", function (e) {
            if (choosedElement) {
                choosedElement.addClass("placedElement");
                choosedElement.removeClass("drop_element");
                var drop_pad = $(".drop-pad").offset();
                var drop_pad_x = drop_pad.left;
                var drop_pad_y = drop_pad.top;
                var scrollTopValue = $('#capture').scrollTop();
                var scrollLeftValue = $('#capture').scrollLeft();
                choosedElement.css({
                    left: choosedElement.offset().left - drop_pad_x + scrollLeftValue,
                    top: choosedElement.offset().top - drop_pad_y + scrollTopValue
                });
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
                elements_array[id]["position_x"] = choosedElement.position().left;
                elements_array[id]["position_y"] = choosedElement.position().top;
                localStorage.setItem(
                    "elements_array",
                    JSON.stringify(elements_array)
                );
                localStorage.setItem(
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
        localStorage.setItem("elements_array", JSON.stringify(elements_array));
        localStorage.setItem(
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
        localStorage.setItem("elements_array", JSON.stringify(elements_array));
        localStorage.setItem(
            "elements_data_array",
            JSON.stringify(elements_data_array)
        );
        $(this).parent().remove();
    }

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
                    var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);

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
            localStorage.setItem(
                "elements_array",
                JSON.stringify(elements_array)
            );
            localStorage.setItem(
                "elements_data_array",
                JSON.stringify(elements_data_array)
            );
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
            $.ajax({
                url: "{{ route('createCampaign') }}",
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                data: JSON.stringify({
                    final_data: elements_data_array,
                    final_array: elements_array,
                    settings: settings,
                    img_url: img,
                }),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        window.location = "{{ route('campaigns') }}";
                    } else {
                        toastr.error(response.properties);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                },
            });
        });
    });

    function onSave() {
        var property = $(".element_properties");
        var elements = property.find(".property_item");
        var element_name = property.find(".element_name").data("bs-target");
        elements.each(function (index, element) {
            var input = $(element).find(".property_input").val();
            $(element).find(".property_input").css({
                border: "2px solid #ddd",
                "box-shadow": "none",
            });
            var p = $(element).find(".property_input").attr("name");
            elements_data_array[element_name][p] = input;
            localStorage.setItem(
                "elements_data_array",
                JSON.stringify(elements_data_array)
            );
        });
        $("#" + element_name).css({
            border: "1px solid rgb(23, 172, 203)",
        });
        $("#" + element_name)
            .find(".item_name")
            .css({
                color: "#fff",
            });
        if (true) {
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
            toastr.success("Properties updated succesfully");
        } else {
            toastr.error("Properties can not be updated");
        }
    }

    function elementProperties(e) {
        $("#element-list").removeClass("active");
        $("#properties").addClass("active");
        $("#element-list-btn").removeClass("active");
        $("#properties-btn").addClass("active");
        var property_input = $(".property_input");
        if (
            property_input.length > 0 &&
            $(this).prop("id") != $(".element_name").data("bs-target")
        ) {
            for (var i = 0; i < property_input.length; i++) {
                var input = property_input.eq(i);
                var target_element = $(property_input[0])
                    .closest(".element_properties")
                    .find(".element_name")
                    .data("bs-target");
                if (input.prop("required") && input.val() == "") {
                    input.addClass("error");
                    $("#" + target_element).addClass("error");
                    $("#" + target_element)
                        .find(".item_name")
                        .addClass("error");
                } else {
                    input.removeClass("error");
                    $("#" + target_element).removeClass("error");
                    $("#" + target_element)
                        .find(".item_name")
                        .removeClass("error");
                    $(".drop-pad-element#" + target_element).addClass(
                        "success"
                    );
                }
            }
        }
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
            $.ajax({
                url: "{{ route('getcampaignelementbyslug', ':slug') }}".replace(
                    ":slug",
                    item_slug
                ),
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
                            if (property["optional"] == "1") {
                                name_html += "required";
                            }
                            name_html += ">";
                            name_html += "</div>";
                            arr[property["id"]] = "";
                        });
                        elements_data_array[item_id] = arr;
                        localStorage.setItem(
                            "elements_data_array",
                            JSON.stringify(elements_data_array)
                        );
                        name_html +=
                            '</div><div class="save-btns"><button id="save">Save</button></div>';
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
                    $("#save").on("click", onSave);
                    $(".property_input").on("input", propertyInput);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                },
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
                        url: "{{ route('getPropertyDatatype', [':id', ':element_slug']) }}"
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
                name_html +=
                    '</div><div class="save-btns"><button id="save">Save</button></div>';
                $("#properties").html(name_html);
                $("#save").on("click", onSave);
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
            localStorage.setItem(
                "elements_array",
                JSON.stringify(elements_array)
            );
            localStorage.setItem(
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
});
