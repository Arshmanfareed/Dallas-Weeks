@extends('partials/head')
<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">


<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark justify-content-between dashboard_header">
            <a class="navbar-brand" href="#">Networked</a>

            <div class="right_nav">
                <ul class="d-flex list-unstyled">
                    <li><a href="#"><i class="fa-regular fa-envelope"></i></a></li>
                    <li><a href="#"><i class="fa-regular fa-bell"></i></a></li>
                    <li class="acc d-flex align-item-center"><img src="assets/img/acc.png" alt=""><span>John
                            Doe</span><i class="fa-solid fa-chevron-down"></i></li>
                    <li class="darkmode"><a href="#"><i class="fa-solid fa-sun"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="col bg-faded py-3 flex-grow-1">
        @yield('content')
    </main>
    <footer>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script>
            $("a.setting_btn").on('click', function() {
                $(this).siblings().toggle();
            });
            $('.setting_btn').each(function() {
                $(this).on('click', function() {
                    $(this).siblings('.setting_list').toggle();
                });
            });
        </script>
        @if (Str::contains(request()->url(), 'createcampaignfromscratch'))
            <script>
                $(document).ready(function() {
                    var linkedin_setting = {!! $linkedin_setting_json !!};
                    var choosedElement = null;
                    var inputElement = null;
                    var outputElement = null;
                    var elements_array = {};
                    var elements_data_array = {};
                    elements_array['step-1'] = {};
                    elements_array['step-1'][0] = '';
                    elements_array['step-1'][1] = '';
                    var count = 0;
                    var condition = '';

                    placeElement();
                    $('.element_change_output').on('click', attachOutputElement);

                    function placeElement(e) {
                        $('.element').on('mousedown', function(e) {
                            e.preventDefault();
                            var clone = $(this).clone().css({
                                'position': 'absolute',
                            });
                            $('body').append(clone);
                            choosedElement = clone;
                            id = choosedElement.attr('id') + '_' + ++count;
                            choosedElement.attr('id', id);
                            choosedElement.addClass('drop_element');
                            choosedElement.addClass('drop-pad-element');
                            choosedElement.removeClass('element');


                            $(document).on('mousemove', function(e) {
                                var x = e.pageX;
                                var y = e.pageY;
                                var element = $('.drop-pad').offset();
                                var element_x = element.left;
                                var max_x = element_x + $('.drop-pad').outerWidth() - choosedElement
                                    .width();
                                var element_y = element.top;
                                var max_y = element_y + $('.drop-pad').outerHeight() - choosedElement
                                    .height();
                                if (x < element_x && y < element_y) {
                                    choosedElement.css({
                                        left: element_x,
                                        top: element_y
                                    });
                                } else if (x < element_x && y > max_y) {
                                    choosedElement.css({
                                        left: element_x,
                                        top: max_y - 20
                                    });
                                    var newDropPadHeight = $('.drop-pad').height() + choosedElement
                                        .height();
                                    $('.drop-pad').css('height', newDropPadHeight + 'px');
                                    var choosedElementOffset = choosedElement.offset();
                                    window.scrollTo({
                                        top: choosedElementOffset.top,
                                        left: choosedElementOffset.left
                                    });
                                } else if (x < element_x && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: element_x,
                                        top: y
                                    });
                                } else if (y < element_y && (x > element_x && x < max_x)) {
                                    choosedElement.css({
                                        left: x,
                                        top: element_y
                                    });
                                } else if (y > max_y && (x > element_x && x < max_x)) {
                                    choosedElement.css({
                                        left: element_x,
                                        top: max_y - 20
                                    });
                                    var newDropPadHeight = $('.drop-pad').height() + choosedElement
                                        .height();
                                    $('.drop-pad').css('height', newDropPadHeight + 'px');
                                    var choosedElementOffset = choosedElement.offset();
                                    window.scrollTo({
                                        top: choosedElementOffset.top,
                                        left: choosedElementOffset.left
                                    });
                                } else if ((x > element_x && x < max_x) && (y > element_y && y <
                                        max_y)) {
                                    choosedElement.css({
                                        left: x,
                                        top: y
                                    });
                                } else if (x > max_x && y > max_y) {
                                    choosedElement.css({
                                        left: element_x - 20,
                                        top: max_y - 20
                                    });
                                    var newDropPadHeight = $('.drop-pad').height() + choosedElement
                                        .height();
                                    $('.drop-pad').css('height', newDropPadHeight + 'px');
                                    var choosedElementOffset = choosedElement.offset();
                                    window.scrollTo({
                                        top: choosedElementOffset.top,
                                        left: choosedElementOffset.left
                                    });
                                } else if (x > max_x && y < element_y) {
                                    choosedElement.css({
                                        left: max_x - 20,
                                        top: element_y
                                    });
                                } else if (x > max_x && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: max_x - 20,
                                        top: y
                                    });
                                } else {
                                    choosedElement.css({
                                        left: element_x,
                                        top: element_y
                                    });
                                }
                            });
                        });

                        $(document).on('mouseup', function(e) {
                            if (choosedElement) {
                                choosedElement.css({
                                    'align-items': 'stretch',
                                });
                                choosedElement.removeClass('drop_element');
                                attach_in = choosedElement.find('.element_change_input');
                                attach_in.css({
                                    'display': 'flex',
                                    'justify-content': 'center',
                                    'align-items': 'center'
                                });
                                attach_on = choosedElement.find('.element_change_output');
                                attach_on.css({
                                    'display': 'flex',
                                    'justify-content': 'center',
                                    'align-items': 'center'
                                });
                                cancel_icon = choosedElement.find('.cancel-icon');
                                cancel_icon.css({
                                    'display': 'flex',
                                });
                                var x = e.pageX;
                                var y = e.pageY;
                                var element = $('.drop-pad').offset();
                                var element_x = element.left;
                                var max_x = element_x + $('.drop-pad').outerWidth() - choosedElement.width();
                                var element_y = element.top;
                                var max_y = element_y + $('.drop-pad').outerHeight() - choosedElement
                                    .height();
                                if (x < element_x && y < element_y) {
                                    choosedElement.css({
                                        left: 0,
                                        top: 0,
                                        'border': 'none',
                                    });
                                } else if (x < element_x && y > max_y) {
                                    choosedElement.css({
                                        left: 0,
                                        top: max_y - 310,
                                        'border': 'none',
                                    });
                                } else if (x > max_x && y > max_y) {
                                    choosedElement.css({
                                        left: max_x - 130,
                                        top: max_y - 310,
                                        'border': 'none',
                                    });
                                } else if (x < element_x && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: 0,
                                        top: y - 330,
                                        'border': 'none',
                                    });
                                } else if (y < element_y && (x > element_x && x < max_x)) {
                                    choosedElement.css({
                                        left: x - 210,
                                        top: 0,
                                        'border': 'none',
                                    });
                                } else if (y > max_y && (x > element_x && x < max_x)) {
                                    choosedElement.css({
                                        left: x - 210,
                                        top: max_y - 310,
                                        'border': 'none',
                                    });
                                } else if ((x > element_x && x < max_x) && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: x - 210,
                                        top: y - 330,
                                        'border': 'none',
                                    });
                                } else if (x > max_x && y < element_y) {
                                    choosedElement.css({
                                        left: max_x - 130,
                                        top: 0,
                                        'border': 'none',
                                    });
                                } else if (x > max_x && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: max_x - 130,
                                        top: y - 330,
                                        'border': 'none',
                                    });
                                } else {
                                    choosedElement.css({
                                        left: 0,
                                        top: 0,
                                        'border': 'none',
                                    });
                                }
                                $(document).off('mousemove');
                                $('.task-list').append(choosedElement);
                                $('.cancel-icon').on('click', removeElement);
                                $('.element_change_output').on('click', attachOutputElement);
                                $('.element_change_input').on('click', attachInputElement);
                                $('.drop-pad-element').on('click', elementProperties);
                                choosedElement.on('mousedown', startDragging);
                                id = choosedElement.attr('id');
                                elements_array[id] = {};
                                elements_array[id][0] = '';
                                elements_array[id][1] = '';
                                choosedElement = null;
                            }
                        });
                    }

                    function removeElement(e) {
                        var element = $(this).parent();
                        var id = element.attr('id');
                        if (elements_array[id]) {
                            var next_false = elements_array[id][0];
                            if (next_false != '') {
                                next_element = $('#' + next_false).find('.element_change_input');
                                next_element.closest('.selected').removeClass('selected');
                            }
                            $('#' + id + '-to-' + next_false).remove();
                            var next_true = elements_array[id][1];
                            if (next_true != '') {
                                next_element = $('#' + next_true).find('.element_change_input');
                                next_element.closest('.selected').removeClass('selected');
                            }
                            $('#' + id + '-to-' + next_true).remove();
                        }
                        var prev = find_element(id);
                        if (elements_array[prev]) {
                            if (elements_array[prev][0] == id) {
                                var prev_element = $('#' + prev).find('.element_change_output.condition_false');
                                prev_element.closest('.selected').removeClass('selected');
                                elements_array[prev][0] = '';
                            } else if (elements_array[prev][1] == id) {
                                var prev_element = $('#' + prev).find('.element_change_output.condition_true');
                                prev_element.closest('.selected').removeClass('selected');
                                elements_array[prev][1] = '';
                            }
                            $('#' + prev + '-to-' + id).remove();
                        }
                        delete elements_array[id];
                        delete elements_data_array[id];
                        $(this).parent().remove();
                    }

                    function removePath(e) {
                        var element = $(this).parent().attr('id');
                        var index = element.indexOf('-to-');
                        var prev_element_id = element.substring(0, index);
                        var prev_element = $('#' + prev_element_id);
                        var next_element_id = element.substring(index + 4);
                        var next_element = $('#' + next_element_id);
                        next_element = next_element.find('.element_change_input');
                        next_element.closest('.selected').removeClass('selected');
                        if (elements_array[prev_element_id][0] == next_element_id) {
                            elements_array[prev_element_id][0] = '';
                            prev_element = prev_element.find('.element_change_output.condition_false');
                            prev_element.closest('.selected').removeClass('selected');
                        } else if (elements_array[prev_element_id][1] == next_element_id) {
                            elements_array[prev_element_id][1] = '';
                            prev_element = prev_element.find('.element_change_output.condition_true');
                            prev_element.closest('.selected').removeClass('selected');
                        }
                        $(this).parent().remove();
                    }

                    function attachOutputElement(e) {
                        if (inputElement == null && outputElement == null) {
                            var attachDiv = $(this);
                            attachDiv.addClass('selected');
                            if (attachDiv.hasClass('condition_true')) {
                                condition = 'True';
                            } else if (attachDiv.hasClass('condition_false')) {
                                condition = 'False';
                            } else {
                                condition = '';
                            }
                            outputElement = attachDiv.closest('.element_item');
                        }
                    }

                    function attachInputElement(e) {
                        if (outputElement != null && outputElement.attr('id') != $(this).parent().attr('id')) {
                            var attachDiv = $(this);
                            attachDiv.addClass('selected');
                            inputElement = attachDiv.closest('.element_item');
                            if (outputElement && inputElement) {
                                var outputElementId = outputElement.attr('id');
                                var inputElementId = inputElement.attr('id');
                                if (condition == 'True') {
                                    elements_array[outputElementId][1] = inputElementId;
                                    var attachOutputElement = $(outputElement).find(
                                        '.element_change_output.condition_true');
                                } else if (condition == 'False') {
                                    elements_array[outputElementId][0] = inputElementId;
                                    var attachOutputElement = $(outputElement).find(
                                        '.element_change_output.condition_false');
                                } else {
                                    $('#' + inputElementId).css({
                                        'border': '1px solid red',
                                    });
                                }
                                $('.drop-pad').append('<div class="line" id="' + outputElement.attr('id') + '-to-' +
                                    inputElement.attr('id') +
                                    '"><div class="path-cancel-icon"><i class="fa-solid fa-xmark"></i></div></div>');
                                $('.path-cancel-icon').on('click', removePath);
                                $('.line').css({
                                    'position': 'absolute',
                                    'background-color': 'white',
                                    'height': '2px',
                                    'transform-origin': 'left center'
                                });
                                var attachInputElement = $(inputElement).find('.element_change_input');
                                if (attachInputElement && attachOutputElement) {
                                    var inputPosition = attachInputElement.offset();
                                    var outputPosition = attachOutputElement.offset();

                                    var x1 = inputPosition.left;
                                    var y1 = inputPosition.top;
                                    var x2 = outputPosition.left;
                                    var y2 = outputPosition.top;

                                    var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                                    var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);

                                    var lineId = outputElement.attr('id') + '-to-' + inputElement.attr('id');
                                    var line = $('#' + lineId);

                                    line.css({
                                        'width': distance + 'px',
                                        'transform': 'rotate(' + angle + 'deg)',
                                        'top': y1 - 320 + 'px',
                                        'left': x1 - 207 + 'px'
                                    });
                                    inputElement = null;
                                    outputElement = null;
                                }
                            }
                            $('.drop-pad-element').on('click', elementProperties);
                        }
                    }

                    $('.element-btn').on('click', function() {
                        var targetTab = $(this).data('tab');
                        $('.element-content').removeClass('active');
                        $('#' + targetTab).addClass('active');
                        $('.element-btn').removeClass('active');
                        $(this).addClass('active');
                    });

                    $('#save-changes').on('click', function() {
                        $.ajax({
                            url: "{{ route('createCampaign') }}",
                            type: 'POST',
                            dataType: 'json',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                'final_data': elements_data_array,
                                'final_array': elements_array,
                                'linkedin_setting': linkedin_setting
                            }),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    window.location = "{{ route('campaigns') }}";
                                } else {
                                    toastr.error(response.properties);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    });

                    function onSave() {
                        var property = $('.element_properties');
                        var elements = property.find('.property_item');
                        var element_name = property.find('.element_name').attr('id');
                        elements.each(function(index, element) {
                            var input = $(element).find('input').val();
                            var p = $(element).find('p').text();
                            elements_data_array[element_name][p] = input;
                        });
                        if (true) {
                            toastr.options = {
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": false,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "timeOut": "5000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            }
                            toastr.success('Properties updated succesfully');
                        } else {
                            toastr.error('Properties can not be updated');
                        }
                    }

                    function elementProperties(e) {
                        $('#properties').empty();
                        var item = $(this);
                        $('.drop-pad-element').css({
                            "z-index": "0",
                            "border": "none",
                        });
                        item.css({
                            "z-index": "999",
                            "border": "1px solid rgb(23, 172, 203)",
                        });
                        var item_slug = item.data('filterName');
                        var item_name = item.find('.item_name').text();
                        var list_icon = item.find('.list-icon').html();
                        var item_id = item.attr('id');
                        var name_html = '';
                        if (!elements_data_array[item_id]) {
                            $.ajax({
                                url: "{{ route('getcampaignelementbyslug', ':slug') }}".replace(':slug', item_slug),
                                type: 'GET',
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        name_html += '<div class="element_properties">';
                                        name_html += '<div class="element_name" id="' + item_id + '">' +
                                            list_icon +
                                            '<p>' + item_name + '</p></div>';
                                        arr = {};
                                        response.properties.forEach(property => {
                                            name_html += '<div class="property_item">';
                                            name_html += '<p>' + property['property_name'] + '</p>';
                                            if (property['data_type'] == 'text') {
                                                name_html += '<input type="' + property['data_type'] +
                                                    '" placeholder="Enter your ' +
                                                    property['property_name'] +
                                                    '" class="property_input"';
                                                if (property['optional'] == '1') {
                                                    name_html += 'required';
                                                }
                                                name_html += '>';
                                                name_html += '</div>';
                                                arr[property['property_name']] = '';
                                            } else {
                                                name_html += '<input type="' + property['data_type'] +
                                                    '" placeholder="0" class="property_input"';
                                                if (property['optional'] == '1') {
                                                    name_html += 'required';
                                                }
                                                name_html += '>';
                                                name_html += '</div>';
                                                arr[property['property_name']] = 0;
                                            }
                                        });
                                        elements_data_array[item_id] = arr;
                                        name_html +=
                                            '</div><div class="save-btns"><button id="save">Save</button></div>';
                                    } else {
                                        name_html += '<div class="element_properties">';
                                        name_html += '<div class="element_name">' + list_icon +
                                            '<p>' + item_name + '</p></div>';
                                        name_html += '<div class="text-center">' + response.message +
                                            '</div></div>';
                                    }
                                    $('#properties').html(name_html);
                                    $('#save').on('click', onSave);
                                    $('#element-list').removeClass('active');
                                    $('#properties').addClass('active');
                                    $('#element-list-btn').removeClass('active');
                                    $('#properties-btn').addClass('active');
                                },
                                error: function(xhr, status, error) {
                                    console.error(xhr.responseText);
                                },
                            });
                        } else {
                            name_html += '<div class="element_properties">';
                            name_html += '<div class="element_name" id="' + item_id + '">' +
                                list_icon +
                                '<p>' + item_name + '</p></div>';
                            elements = elements_data_array[item_id];
                            var ajaxRequests = [];
                            for (const key in elements) {
                                ajaxRequests.push($.ajax({
                                    url: "{{ route('getPropertyDatatype', [':name', ':element_slug']) }}"
                                        .replace(':name', key).replace(':element_slug', item_slug),
                                    type: 'GET',
                                    dataType: 'json'
                                }).then(function(response) {
                                    if (response.success) {
                                        const value = elements[key];
                                        name_html += '<div class="property_item">';
                                        name_html += '<p>' + key + '</p>';
                                        if (response.properties == 'text') {
                                            name_html += '<input type="' + response.properties +
                                                '" value="' + value +
                                                '" class="property_input"';
                                            if (response.optional == '1') {
                                                name_html += 'required';
                                            }
                                            name_html += '>';
                                        } else {
                                            name_html += '<input type="' + response.properties +
                                                '" value="' + value + '" class="property_input"';
                                            if (response.optional == '1') {
                                                name_html += 'required';
                                            }
                                            name_html += '>';
                                        }
                                        name_html += '</div>';
                                    } else {
                                        name_html += '<div class="property_item">';
                                        name_html += '<p>' + key + '</p>';
                                        name_html += '<input type="text" placeholder="' + value +
                                            '" class="property_input">';
                                        name_html += '</div>';
                                    }
                                }));
                            }
                            $.when.apply($, ajaxRequests).then(function() {
                                name_html += '</div><div class="save-btns"><button id="save">Save</button></div>';
                                $('#properties').html(name_html);
                                $('#save').on('click', onSave);
                                $('#element-list').removeClass('active');
                                $('#properties').addClass('active');
                                $('#element-list-btn').removeClass('active');
                                $('#properties-btn').addClass('active');
                            });
                        }
                    }

                    function startDragging(e) {
                        e.preventDefault();
                        var currentElement = $(this);
                        $(document).on('mousemove', function(e) {
                            var x = e.pageX;
                            var y = e.pageY;
                            var element = $('.drop-pad').offset();
                            var element_x = element.left;
                            var max_x = element_x + $('.drop-pad').outerWidth() - currentElement.width();
                            var element_y = element.top;
                            var max_y = element_y + $('.drop-pad').outerHeight() - currentElement.height();
                            if (x < element_x && y < element_y) {
                                currentElement.css({
                                    left: 0,
                                    top: 0,
                                    'border': 'none',
                                });
                            } else if (x < element_x && y > max_y) {
                                currentElement.css({
                                    left: 0,
                                    top: max_y - 310,
                                    'border': 'none',
                                });
                                var newDropPadHeight = $('.drop-pad').height() + currentElement
                                    .height();
                                $('.drop-pad').css('height', newDropPadHeight + 'px');
                                var currentElementOffset = currentElement.offset();
                                window.scrollTo({
                                    top: currentElementOffset.top,
                                    left: currentElementOffset.left
                                });
                            } else if (x > max_x && y > max_y) {
                                currentElement.css({
                                    left: max_x - 240,
                                    top: max_y - 310,
                                    'border': 'none',
                                });
                                var newDropPadHeight = $('.drop-pad').height() + currentElement
                                    .height();
                                $('.drop-pad').css('height', newDropPadHeight + 'px');
                                var currentElementOffset = currentElement.offset();
                                window.scrollTo({
                                    top: currentElementOffset.top,
                                    left: currentElementOffset.left
                                });
                            } else if (x < element_x && (y > element_y && y < max_y)) {
                                currentElement.css({
                                    left: 0,
                                    top: y - 350,
                                    'border': 'none',
                                });
                            } else if (y < element_y && (x > element_x && x < max_x)) {
                                currentElement.css({
                                    left: x - 210,
                                    top: 0,
                                    'border': 'none',
                                });
                            } else if (y > max_y && (x > element_x && x < max_x)) {
                                currentElement.css({
                                    left: x - 210,
                                    top: max_y - 350,
                                    'border': 'none',
                                });
                                var newDropPadHeight = $('.drop-pad').height() + currentElement
                                    .height();
                                $('.drop-pad').css('height', newDropPadHeight + 'px');
                                var currentElementOffset = currentElement.offset();
                                window.scrollTo({
                                    top: currentElementOffset.top,
                                    left: currentElementOffset.left
                                });
                            } else if (x > element_x && x < max_x && y > element_y && y < max_y) {
                                currentElement.css({
                                    left: x - 210,
                                    top: y - 350,
                                    'border': 'none',
                                });
                            } else if (x > max_x && y < element_y) {
                                currentElement.css({
                                    left: max_x - 240,
                                    top: 0,
                                    'border': 'none',
                                });
                            } else if (x > max_x && (y > element_y && y < max_y)) {
                                currentElement.css({
                                    left: max_x - 240,
                                    top: y - 350,
                                    'border': 'none',
                                });
                            } else {
                                currentElement.css({
                                    left: 0,
                                    top: 0,
                                    'border': 'none',
                                });
                            }
                            var current_element_id = currentElement.attr('id');
                            var next_false_element_id = elements_array[current_element_id][0];
                            var next_true_element_id = elements_array[current_element_id][1];
                            var prev_element_id = find_element(currentElement.attr('id'));
                            if (prev_element_id && current_element_id) {
                                if ($('.drop-pad').find('#' + prev_element_id + '-to-' + current_element_id)
                                    .length > 0) {
                                    var attachInputElement = $('#' + current_element_id).find(
                                        '.element_change_input');
                                    var attachOutputElement;
                                    if (elements_array[prev_element_id][0] == current_element_id) {
                                        attachOutputElement = $('#' + prev_element_id).find(
                                            '.element_change_output.condition_false');
                                    } else if (elements_array[prev_element_id][1] == current_element_id) {
                                        attachOutputElement = $('#' + prev_element_id).find(
                                            '.element_change_output.condition_true');
                                    }
                                    if (attachInputElement.length && attachOutputElement.length) {
                                        var inputPosition = attachInputElement.offset();
                                        var outputPosition = attachOutputElement.offset();
                                        var x1 = inputPosition.left;
                                        var y1 = inputPosition.top;
                                        var x2 = outputPosition.left;
                                        var y2 = outputPosition.top;
                                        var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                                        var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                                        var lineId = prev_element_id + '-to-' + current_element_id;
                                        var line = $('#' + lineId);
                                        line.css({
                                            'width': distance + 'px',
                                            'transform': 'rotate(' + angle + 'deg)',
                                            'top': y1 - 320 + 'px',
                                            'left': x1 - 207 + 'px'
                                        });
                                    }
                                }
                            }
                            if (current_element_id && next_true_element_id) {
                                if ($('.drop-pad').find('#' + current_element_id + '-to-' + next_true_element_id)
                                    .length > 0) {
                                    var attachInputElement = $('#' + next_true_element_id).find(
                                        '.element_change_input');
                                    var attachOutputElement = $('#' + current_element_id).find(
                                        '.element_change_output.condition_true');
                                    if (attachInputElement.length && attachOutputElement.length) {
                                        var inputPosition = attachInputElement.offset();
                                        var outputPosition = attachOutputElement.offset();

                                        var x1 = inputPosition.left;
                                        var y1 = inputPosition.top;
                                        var x2 = outputPosition.left;
                                        var y2 = outputPosition.top;

                                        var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                                        var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                                        var lineId = current_element_id + '-to-' + next_true_element_id;
                                        var line = $('#' + lineId);
                                        line.css({
                                            'width': distance + 'px',
                                            'transform': 'rotate(' + angle + 'deg)',
                                            'top': y1 - 320 + 'px',
                                            'left': x1 - 207 + 'px'
                                        });
                                    }
                                }
                            }
                            if (current_element_id && next_false_element_id) {
                                if ($('.drop-pad').find('#' + current_element_id + '-to-' + next_false_element_id)
                                    .length > 0) {
                                    var attachInputElement = $('#' + next_false_element_id).find(
                                        '.element_change_input');
                                    var attachOutputElement = $('#' + current_element_id).find(
                                        '.element_change_output.condition_false');
                                    if (attachInputElement.length && attachOutputElement.length) {
                                        var inputPosition = attachInputElement.offset();
                                        var outputPosition = attachOutputElement.offset();

                                        var x1 = inputPosition.left;
                                        var y1 = inputPosition.top;
                                        var x2 = outputPosition.left;
                                        var y2 = outputPosition.top;

                                        var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                                        var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                                        var lineId = current_element_id + '-to-' + next_false_element_id;
                                        var line = $('#' + lineId);
                                        line.css({
                                            'width': distance + 'px',
                                            'transform': 'rotate(' + angle + 'deg)',
                                            'top': y1 - 320 + 'px',
                                            'left': x1 - 207 + 'px'
                                        });
                                    }
                                }
                            }
                        });
                        $(document).on('mouseup', function() {
                            $(document).off('mousemove');
                        });
                    }

                    function find_element(element_id) {
                        for (var key in elements_array) {
                            if (elements_array[key][0] == element_id || elements_array[key][1] == element_id) {
                                return key;
                            }
                        }
                    }
                });
            </script>
        @elseif (Str::contains(request()->url(), 'createcampaign'))
            <script>
                $(document).ready(function() {
                    var campaign_details = JSON.parse(sessionStorage.getItem('campaign_details')) || {};
                    if (campaign_details['campaign_type'] == undefined) {
                        campaign_details['campaign_type'] = 'linkedin';
                    }
                    var campaign_pane = $('.campaign_pane');
                    for (var i = 0; i < campaign_pane.length; i++) {
                        var campaignType = $(campaign_pane[i]).find('#campaign_type').val();
                        if (campaignType == campaign_details['campaign_type']) {
                            $(campaign_pane[i]).addClass('active');
                            $('[data-bs-target="' + $(campaign_pane[i]).attr('id') + '"]').addClass('active');
                        }
                    }
                    if (campaign_details['campaign_name'] == undefined || campaign_details['campaign_url'] == undefined ||
                        campaign_details['connections'] == undefined) {
                        campaign_details['campaign_name'] = '';
                        campaign_details['campaign_url'] = '';
                        campaign_details['connections'] = '1';
                    } else {
                        var active_form = $('.campaign_pane.active').find('form');
                        active_form.find('#campaign_name').val(campaign_details['campaign_name']);
                        if (active_form.attr('id') != 'campaign_form_4') {
                            active_form.find('#campaign_url').val(campaign_details['campaign_url']);
                        }
                        if (active_form.attr('id') != 'campaign_form_4' && active_form.attr('id') != 'campaign_form_3') {
                            active_form.find('#connections').val(campaign_details['connections']);
                        }
                    }
                    $('.campaign_name').on('change', function(e) {
                        campaign_details['campaign_name'] = $(this).val();
                        sessionStorage.setItem('campaign_details', JSON.stringify(campaign_details));
                    });
                    $('.campaign_url').on('change', function(e) {
                        campaign_details['campaign_url'] = $(this).val();
                        sessionStorage.setItem('campaign_details', JSON.stringify(campaign_details));
                    });
                    $('.connections').on('change', function(e) {
                        campaign_details['connections'] = $(this).val();
                        sessionStorage.setItem('campaign_details', JSON.stringify(campaign_details));
                    });
                    $('.campaign_tab').on('click', function(e) {
                        e.preventDefault();
                        $('.campaign_tab').removeClass('active');
                        $(this).addClass('active');
                        var id = $(this).data('bs-target');
                        $('.campaign_pane').removeClass('active');
                        $('#' + id).addClass('active');
                        var new_form = $('#' + id).find('form');
                        campaign_details['campaign_type'] = new_form.find('#campaign_type').val();
                        sessionStorage.setItem('campaign_details', JSON.stringify(campaign_details));
                        new_form.find('#campaign_name').val(campaign_details['campaign_name']);
                        if (new_form.attr('id') != 'campaign_form_4') {
                            new_form.find('#campaign_url').val(campaign_details['campaign_url']);
                        }
                        if (new_form.attr('id') != 'campaign_form_4' && new_form.attr('id') != 'campaign_form_3') {
                            new_form.find('#connections').val(campaign_details['connections']);
                        }
                    });
                    $('.nxt_btn').on('click', function(e) {
                        e.preventDefault();
                        var form = $('.campaign_pane.active').find('form');
                        form.submit();
                    });
                });
            </script>
        @elseif (Str::contains(request()->url(), 'campaigninfo'))
            <script>
                $(document).ready(function() {
                    var campaign_details = {!! $campaign_details_json !!};
                    var form = $('#linkedin_settings');
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_type').val(campaign_details[
                        'campaign_type']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_name').val(campaign_details[
                        'campaign_name']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_url').val(campaign_details[
                        'campaign_url']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'connections').val(campaign_details[
                        'connections']));
                    $('#create_sequence').on('click', function(e) {
                        e.preventDefault();
                        var form = $('#linkedin_settings');
                        form.submit();
                    });
                    $('.next_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').next().click();
                    });
                    $('.prev_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').prev().click();
                    });
                    $('.schedule-btn').on('click', function() {
                        var targetTab = $(this).data('tab');
                        $('.schedule-content').removeClass('active');
                        $('#' + targetTab).addClass('active');
                        $('.schedule-btn').removeClass('active');
                        $(this).addClass('active');
                    });
                });
            </script>
        @endif
    </footer>
</body>

</html>
