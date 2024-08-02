$(document).ready(function () {
    /* Making every setting to unchangable */
    $(".linkedin_setting_switch").prop("disabled", true);
    var inputElement = null;
    var outputElement = null;
    var condition = "";
    var elements_data_array = {};
    var elements_array = {};
    var choosedElement = null;
    var message = "";
    campaign_time = new Date(campaign_time);
    campaign_time.setMonth(campaign_time.getMonth() + 1);
    campaign_time = campaign_time.toLocaleDateString("en-US", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });
    var current_time = new Date().toLocaleDateString("en-US", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });

    if (current_time >= campaign_time) {
        placeElement();
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
                id =
                    choosedElement.attr("id") +
                    "_" +
                    Math.floor(10000 + Math.random() * 90000);
                choosedElement.attr("id", id);
                choosedElement.addClass("drop_element");
                choosedElement.addClass("drop-pad-element");
                choosedElement.removeClass("element");
                $(document).on("mousemove", function (e) {
                    var cursor_x = e.pageX;
                    var cursor_y = e.pageY;
                    var drop_pad = $(".drop-pad").offset();
                    var drop_pad_x = drop_pad.left;
                    var drop_pad_max_x =
                        drop_pad_x + $(".drop-pad").outerWidth();
                    var drop_pad_y = drop_pad.top;
                    var drop_pad_max_y =
                        drop_pad_y + $(".drop-pad").outerHeight();
                    if (
                        cursor_y < drop_pad_y &&
                        cursor_x > drop_pad_x &&
                        cursor_x < drop_pad_max_x
                    ) {
                        var subtract =
                            cursor_x - choosedElement.outerWidth(true);
                        if (subtract < drop_pad_x) {
                            choosedElement.css({
                                left: drop_pad_x,
                                top: drop_pad_y,
                            });
                        } else {
                            choosedElement.css({
                                left: subtract,
                                top: drop_pad_y,
                            });
                        }
                    } else if (
                        cursor_x < drop_pad_x &&
                        cursor_y > drop_pad_y &&
                        cursor_y < drop_pad_max_y
                    ) {
                        var subtract =
                            cursor_y - choosedElement.outerHeight(true);
                        if (subtract < drop_pad_y) {
                            choosedElement.css({
                                left: drop_pad_x,
                                top: drop_pad_y,
                            });
                        } else {
                            choosedElement.css({
                                left: drop_pad_x,
                                top: subtract,
                            });
                        }
                    } else if (
                        cursor_x > drop_pad_max_x &&
                        cursor_y > drop_pad_y &&
                        cursor_y < drop_pad_max_y
                    ) {
                        var subtract =
                            cursor_y - choosedElement.outerHeight(true);
                        if (subtract < drop_pad_y) {
                            choosedElement.css({
                                left:
                                    drop_pad_max_x -
                                    choosedElement.outerWidth(true),
                                top: drop_pad_y,
                            });
                        } else {
                            choosedElement.css({
                                left:
                                    drop_pad_max_x -
                                    choosedElement.outerWidth(true),
                                top: subtract,
                            });
                        }
                    } else if (cursor_x < drop_pad_x && cursor_y < drop_pad_y) {
                        choosedElement.css({
                            left: drop_pad_x,
                            top: drop_pad_y,
                        });
                    } else if (
                        cursor_y < drop_pad_y &&
                        cursor_x > drop_pad_max_x
                    ) {
                        choosedElement.css({
                            left:
                                drop_pad_max_x -
                                choosedElement.outerWidth(true),
                            top: drop_pad_y,
                        });
                    } else if (cursor_y > drop_pad_max_y) {
                        if (
                            cursor_x > drop_pad_x &&
                            cursor_x < drop_pad_max_x
                        ) {
                            if (
                                cursor_x + choosedElement.outerWidth(true) >
                                drop_pad_max_x
                            ) {
                                choosedElement.css({
                                    left:
                                        drop_pad_max_x -
                                        choosedElement.outerWidth(true),
                                    top:
                                        drop_pad_max_y -
                                        choosedElement.outerHeight(true),
                                });
                            } else {
                                choosedElement.css({
                                    left: cursor_x,
                                    top:
                                        drop_pad_max_y -
                                        choosedElement.outerHeight(true),
                                });
                            }
                        } else if (cursor_x < drop_pad_x) {
                            choosedElement.css({
                                left: drop_pad_x,
                                top:
                                    drop_pad_max_y -
                                    choosedElement.outerHeight(true),
                            });
                        } else if (cursor_x > drop_pad_max_x) {
                            choosedElement.css({
                                left:
                                    drop_pad_max_x -
                                    choosedElement.outerWidth(true),
                                top:
                                    drop_pad_max_y -
                                    choosedElement.outerHeight(true),
                            });
                        }
                        var newDropPadHeight =
                            $(".task-list").outerHeight() +
                            choosedElement.outerHeight();
                        $(".task-list").css({
                            height: newDropPadHeight,
                        });
                        $("#capture").scrollTop($("#capture")[0].scrollHeight);
                    } else {
                        if (
                            cursor_x + choosedElement.outerWidth(true) >
                                drop_pad_max_x &&
                            cursor_y + choosedElement.outerHeight(true) <
                                drop_pad_max_y
                        ) {
                            choosedElement.css({
                                left:
                                    drop_pad_max_x -
                                    choosedElement.outerWidth(true),
                                top: cursor_y,
                            });
                        } else if (
                            cursor_y + choosedElement.outerHeight(true) >
                                drop_pad_max_y &&
                            cursor_x + choosedElement.outerWidth(true) <
                                drop_pad_max_x
                        ) {
                            choosedElement.css({
                                left: cursor_x,
                                top:
                                    drop_pad_max_y -
                                    choosedElement.outerHeight(true),
                            });
                        } else if (
                            cursor_x + choosedElement.outerWidth(true) >
                                drop_pad_max_x &&
                            cursor_y + choosedElement.outerHeight(true) >
                                drop_pad_max_y
                        ) {
                            choosedElement.css({
                                left:
                                    drop_pad_max_x -
                                    choosedElement.outerWidth(true),
                                top:
                                    drop_pad_max_y -
                                    choosedElement.outerHeight(true),
                            });
                        } else {
                            choosedElement.css({
                                left: cursor_x,
                                top: cursor_y,
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
                    var scrollTopValue = $("#capture").scrollTop();
                    var scrollLeftValue = $("#capture").scrollLeft();
                    choosedElement.css({
                        left:
                            choosedElement.offset().left -
                            drop_pad_x +
                            scrollLeftValue,
                        top:
                            choosedElement.offset().top -
                            drop_pad_y +
                            scrollTopValue,
                    });
                    $(document).off("mousemove");
                    $(".task-list").append(choosedElement);
                    $(".cancel-icon").on("click", removeElement);
                    $(".element_change_output").on(
                        "click",
                        attachOutputElement
                    );
                    $(".element_change_input").on("click", attachInputElement);
                    $(".drop-pad-element").on("click", elementProperties);
                    choosedElement.on("mousedown", startDragging);
                    id = choosedElement.attr("id");
                    elements_array[id] = {};
                    elements_array[id][0] = "";
                    elements_array[id][1] = "";
                    elements_array[id]["position_x"] =
                        choosedElement.position().left;
                    elements_array[id]["position_y"] =
                        choosedElement.position().top;
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
                    next_element = $("#" + next_true).find(
                        ".element_change_input"
                    );
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
                var next_false_element_id =
                    elements_array[current_element_id][0];
                var next_true_element_id =
                    elements_array[current_element_id][1];
                var prev_element_id = find_element(currentElement.attr("id"));
                if (prev_element_id && current_element_id) {
                    if (
                        $(".drop-pad").find(
                            "#" + prev_element_id + "-to-" + current_element_id
                        ).length > 0
                    ) {
                        var attachInputElement = $(
                            "#" + current_element_id
                        ).find(".element_change_input");
                        var attachOutputElement;
                        if (
                            elements_array[prev_element_id][0] ==
                            current_element_id
                        ) {
                            attachOutputElement = $("#" + prev_element_id).find(
                                ".element_change_output.condition_false"
                            );
                        } else if (
                            elements_array[prev_element_id][1] ==
                            current_element_id
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
                            "#" +
                                current_element_id +
                                "-to-" +
                                next_true_element_id
                        ).length > 0
                    ) {
                        var attachInputElement = $(
                            "#" + next_true_element_id
                        ).find(".element_change_input");
                        var attachOutputElement = $(
                            "#" + current_element_id
                        ).find(".element_change_output.condition_true");
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
                                current_element_id +
                                "-to-" +
                                next_true_element_id;
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
                        var attachOutputElement = $(
                            "#" + current_element_id
                        ).find(".element_change_output.condition_false");
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
                                current_element_id +
                                "-to-" +
                                next_false_element_id;
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
                sessionStorage.setItem(
                    "elements_data_array",
                    JSON.stringify(elements_data_array)
                );
                console.log(elements_data_array);
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
    } else {
        $(".crt_cmp_r")
            .find(".align-items-center")
            .append(
                '<span class="text-danger h5">You can not update sequence within a month!</span>'
            );
        message = "You can not update sequence after a month!";
    }

    $(".element-btn").on("click", function () {
        var targetTab = $(this).data("tab");
        $(".element-content").removeClass("active");
        $("#" + targetTab).addClass("active");
        $(".element-btn").removeClass("active");
        $(this).addClass("active");
    });

    // $.ajax({
    //     url: getElementsRoute.replace(":campaign_id", campaign_id),
    //     method: "GET",
    //     success: function (response) {
    //         if (response.success) {
    //             elements_array = response.elements_array;
    //             path = response.path;
    //             if (elements_array) {
    //                 var maxDropPadHeight = 0;
    //                 html = ``;
    //                 html += `<div class="step-1 element_item" id="step-1"><div class="list-icon">`;
    //                 html += `<i class="fa-solid fa-certificate"></i></div><div class="item_details">`;
    //                 html += `<p class="item_name">Lead Source (Step 1)</p><p class="item_desc">`;
    //                 html += `<i class="fa-solid fa-clock"></i>Wait for: <span class="item_days">0</span>`;
    //                 html += ` days <span class="item_hours">0</span> hours</p></div>`;
    //                 html += `<div class="element_change_output attach-elements-out condition_true"></div></div>`;
    //                 $(".task-list").append(html);
    //                 for (var i = 0; i < elements_array.length; i++) {
    //                     var element = elements_array[i]["original_element"];
    //                     var original_properties =
    //                         elements_array[i]["properties"];
    //                     var days = 0;
    //                     var hours = 0;
    //                     for (var j = 0; j < original_properties.length; j++) {
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
    //                         html += elements_array[i]["id"] + `"`;
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
    //                         html += elements_array[i]["id"] + `"`;
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
    //                     var clone = $("#" + elements_array[i]["id"]);
    //                     clone.css({
    //                         position: "absolute",
    //                     });
    //                     var left = elements_array[i]["position_x"];
    //                     var top = elements_array[i]["position_y"];
    //                     // var left = elements_array[i]["position_x"];
    //                     // var step1_left = $("#step-1").position().left;
    //                     // var subtract =
    //                     //     parseInt(elements_array[0]["position_x"]) -
    //                     //     step1_left;
    //                     // if (parseInt(left) - subtract < 0) {
    //                     //     left = 0;
    //                     // } else if (
    //                     //     parseInt(left) + $(clone).width() <
    //                     //     $(".drop-pad").width()
    //                     // ) {
    //                     //     left = parseInt(left) - subtract;
    //                     // } else {
    //                     //     left =
    //                     //         $(".drop-pad").width() -
    //                     //         $(clone).width() -
    //                     //         step1_top;
    //                     // }
    //                     // var top = elements_array[i]["position_y"];
    //                     // var step1_top = $("#step-1").position().top;
    //                     // var step1_height = $("#step-1").outerHeight(true);
    //                     // subtract =
    //                     //     parseInt(elements_array[0]["position_y"]) -
    //                     //     step1_height -
    //                     //     step1_top;
    //                     // if (parseInt(top) - subtract < 0) {
    //                     //     top = 0;
    //                     // } else {
    //                     //     top = parseInt(top) - subtract;
    //                     // }
    //                     clone.css({
    //                         left: left,
    //                         top: top,
    //                     });
    //                     // var newDropPadHeight =
    //                     //     parseInt($(clone).css("top")) +
    //                     //     parseInt($(clone).css("height")) +
    //                     //     step1_top;
    //                     // if (maxDropPadHeight < newDropPadHeight) {
    //                     //     maxDropPadHeight = newDropPadHeight;
    //                     //     $(".drop-pad").css(
    //                     //         "height",
    //                     //         maxDropPadHeight + "px"
    //                     //     );
    //                     // }
    //                 }
    //                 $("#step-1")
    //                     .find(".condition_true")
    //                     .on("click", attachOutputElement)
    //                     .trigger("click");
    //                 var first_element = path[0]["current_element_id"];
    //                 $("#" + first_element)
    //                     .find(".element_change_input")
    //                     .on("click", attachInputElement)
    //                     .trigger("click");
    //                 for (var i = 0; i < path.length; i++) {
    //                     current_element = path[i]["current_element_id"];
    //                     if (path[i]["next_false_element_id"] != "") {
    //                         $("#" + current_element)
    //                             .find(".condition_false")
    //                             .on("click", attachOutputElement)
    //                             .trigger("click");
    //                         $("#" + path[i]["next_false_element_id"])
    //                             .find(".element_change_input")
    //                             .on("click", attachInputElement)
    //                             .trigger("click");
    //                     }
    //                     if (path[i]["next_true_element_id"] != "") {
    //                         $("#" + current_element)
    //                             .find(".condition_true")
    //                             .on("click", attachOutputElement)
    //                             .trigger("click");
    //                         $("#" + path[i]["next_true_element_id"])
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
        var item_name = $(this).find(".item_name").text();
        var list_icon = $(this).find(".list-icon").html();
        var item_id = $(this).attr("id");
        var name_html = "";
        $("#loader").show();
        $.ajax({
            url: getElementByIdRoute.replace(":element_id", item_id),
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    name_html += '<div class="element_properties">';
                    name_html +=
                        '<div class="element_name" data-bs-target="' +
                        item_id +
                        '">';
                    name_html += list_icon + "<p>" + item_name + "</p></div>";
                    properties = response.properties;
                    arr = {};
                    for (var i = 0; i < properties.length; i++) {
                        original_properties =
                            properties[i]["original_properties"];
                        arr[properties[i]["id"]] = properties[i]["value"];
                        name_html += '<div class="property_item">';
                        name_html +=
                            "<p>" + original_properties["property_name"];
                        name_html += "</p>";
                        name_html +=
                            '<input type="' + original_properties["data_type"];
                        name_html +=
                            '" placeholder="Enter the ' +
                            original_properties["property_name"];
                        name_html +=
                            '" class="property_input" name="' +
                            properties[i]["id"];
                        name_html += '" value="' + properties[i]["value"] + '"';
                        if (original_properties["optional"] == "1") {
                            name_html += "required";
                        }
                        if (message != "") {
                            name_html += " readonly";
                        }
                        name_html += ">";
                        name_html += "</div>";
                    }
                    elements_data_array[item_id] = arr;
                    name_html += "</div>";
                    if (message != "") {
                        name_html += '<div class="text-danger text-center">';
                        name_html += message + "</div>";
                    }
                } else {
                    name_html += '<div class="element_properties">';
                    name_html +=
                        '<div class="element_name" data-bs-target="' +
                        item_id +
                        '">';
                    name_html += list_icon + "<p>" + item_name + "</p></div>";
                    properties = response.properties;
                    arr = {};
                    for (var i = 0; i < properties.length; i++) {
                        arr[properties[i]["id"]] = "";
                        name_html += '<div class="property_item">';
                        name_html += "<p>" + properties[i]["property_name"];
                        name_html += "</p>";
                        name_html +=
                            '<input type="' + properties[i]["data_type"];
                        name_html +=
                            '" placeholder="Enter the ' +
                            properties[i]["property_name"];
                        name_html +=
                            '" class="property_input" name="' + item_id;
                        name_html += '"';
                        if (properties[i]["optional"] == "1") {
                            name_html += "required";
                        }
                        name_html += ">";
                        name_html += "</div>";
                    }
                    elements_data_array[item_id] = arr;
                    sessionStorage.setItem(
                        "elements_data_array",
                        JSON.stringify(elements_data_array)
                    );
                    name_html += "</div>";
                    name_html +=
                        '<div class="save-btns"><button id="save">Save</button></div>';
                }
                $("#properties").html(name_html);
                $("#save").on("click", onSave);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    }

    $("#save-changes").on("click", function () {
        $(".drop-pad-element .cancel-icon").css({
            display: "none",
        });
        $(".drop-pad-element").css({
            "z-index": "0",
            border: "none",
        });
        $("#loader").show();
        $.ajax({
            url: updateCampaignRoute.replace(":campaign_id", campaign_id),
            type: "POST",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify({
                settings: settings,
            }),
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    window.location = campaignRoute;
                } else {
                    toastr.error(response.properties);
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
});
