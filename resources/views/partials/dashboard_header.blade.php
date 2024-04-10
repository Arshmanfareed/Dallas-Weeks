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
                    var chooseElement = null;
                    var elementInput = null;
                    var elementOutput = null;
                    var count = 0;
                    var final_array = [];
                    var final_data = {};
                    var input_array = [];

                    move();
                    $('.attach-elements-out').on('click', attachElementOutput);

                    function attachElementOutput(e) {
                        if (!elementInput) {
                            var attachDiv = $(this);
                            attachDiv.css({
                                "background-color": "white"
                            });
                            elementOutput = attachDiv.parent();
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
                        if (final_array[0] != 'step-1' || final_array[1] == '') {
                            alert('Select Step 1 First');
                        } else {
                            $.ajax({
                                url: "{{ route('createCampaign') }}",
                                type: 'POST',
                                dataType: 'json',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    'final_data': final_data,
                                    'final_array': final_array,
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
                        }
                    });

                    function onSave() {
                        var property = $('.element_properties');
                        var elements = property.find('.property_item');
                        var element_name = property.find('.element_name').attr('id');
                        elements.each(function(index, element) {
                            var input = $(element).find('input').val();
                            var p = $(element).find('p').text();
                            final_data[element_name][p] = input;
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

                    function lowercaseWords(str) {
                        str = str.replace(' (optional)', '');
                        return str.split(' ').map(word => word.toLowerCase()).join(' ');
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
                        if (!final_data[item_id]) {
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
                                                    '" placeholder="Enter your ' + lowercaseWords(
                                                        property['property_name']) +
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
                                        final_data[item_id] = arr;
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
                            elements = final_data[item_id];
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

                    function removeElement(e) {
                        var element = $(this).parent();
                        if (final_array.includes(element.attr('id'))) {
                            let index = final_array.indexOf(element.attr('id'));
                            var current_element_id = final_array[index];
                            var current_element = $('#' + current_element_id);
                            var index_input_array = input_array.indexOf(element.attr('id'));
                            input_array.splice(index_input_array, 1);
                            index = final_array.indexOf(element.attr('id'));
                            if (index - 1 >= 0 && final_array[index - 1] != '' && current_element) {
                                var prev_element_id = final_array[index - 1];
                                var prev_element = $('#' + prev_element_id);
                                if (final_array.indexOf(prev_element_id) - 1 < 0) {
                                    final_array.splice(final_array.indexOf(prev_element_id), 1);
                                }
                                var line_id = prev_element_id + '-to-' + current_element_id;
                                var line = $('#' + line_id);
                                var first_item = prev_element.find('.attach-elements-out');
                                var last_item = current_element.find('.attach-elements-in');
                                first_item.css({
                                    'background-color': '#000',
                                });
                                last_item.css({
                                    'background-color': '#000',
                                });
                                line.remove();
                            }
                            index = final_array.indexOf(element.attr('id'));
                            if (index + 1 < final_array.length && final_array[index + 1] != '' && current_element) {
                                var next_element_id = final_array[index + 1];
                                var next_element = $('#' + next_element_id);
                                if (final_array.indexOf(next_element_id) + 1 >= final_array.length) {
                                    final_array.splice(final_array.indexOf(next_element_id), 1);
                                }
                                var line_id = current_element_id + '-to-' + next_element_id;
                                var line = $('#' + line_id);
                                var first_item = current_element.find('.attach-elements-out');
                                var last_item = next_element.find('.attach-elements-in');
                                first_item.css({
                                    'background-color': '#000',
                                });
                                last_item.css({
                                    'background-color': '#000',
                                });
                                line.remove();
                            }
                            final_array.splice(final_array.indexOf(element.attr('id')), 1);
                        }
                        $(this).parent().remove();
                    }

                    function removePath(e) {
                        var element = $(this).parent().attr('id');
                        var index = element.indexOf('-to-');
                        var prev_element_id = element.substring(0, index);
                        var prev_element = $('#' + prev_element_id);
                        prev_element = prev_element.find('.attach-elements-out');
                        prev_element.css({
                            'background-color': '#000',
                        });
                        var next_element_id = element.substring(index + 4);
                        var next_element = $('#' + next_element_id);
                        next_element = next_element.find('.attach-elements-in');
                        next_element.css({
                            'background-color': '#000',
                        });
                        var prev_index = final_array.indexOf(prev_element_id);
                        var next_index = final_array.indexOf(next_element_id);
                        if (prev_index >= 0 && next_index < final_array.length) {
                            if (final_array.includes(prev_element_id) && final_array.includes(next_element_id)) {
                                prev_index = final_array.indexOf(prev_element_id);
                                if (!final_array[prev_index - 1]) {
                                    final_array.splice(prev_index, 1);
                                }
                                next_index = final_array.indexOf(next_element_id);
                                if (!final_array[next_index + 1]) {
                                    final_array.splice(next_index, 1);
                                }
                                $(this).parent().remove();
                            }
                        }
                    }

                    function attachElementInput(e) {
                        if (elementOutput && elementOutput.attr('id') != $(this).parent().attr('id')) {
                            var attachDiv = $(this);
                            attachDiv.css({
                                "background-color": "white"
                            });
                            elementInput = attachDiv.parent();
                            if (elementOutput && elementInput) {
                                if (!final_array.includes(elementOutput.attr('id')) && !final_array.includes(elementInput
                                        .attr('id'))) {
                                    final_array.push(elementOutput.attr('id'));
                                    final_array.push(elementInput.attr('id'));
                                } else if (final_array.includes(elementOutput.attr('id')) && !final_array.includes(
                                        elementInput.attr('id'))) {
                                    let index = final_array.indexOf(elementOutput.attr('id'));
                                    var arr_len = final_array.length - 1;
                                    if (index == arr_len) {
                                        final_array.push(elementInput.attr('id'));
                                    } else if (final_array[index + 1] === '') {
                                        final_array[index + 1] = elementInput.attr('id')
                                    } else {
                                        var duplicate_array = [
                                            ...final_array.slice(0, index),
                                            elementInput.attr('id'),
                                            ...final_array.slice(index + 1)
                                        ];
                                        final_array = duplicate_array;
                                    }
                                } else if (!final_array.includes(elementOutput.attr('id')) && final_array.includes(
                                        elementInput.attr('id'))) {
                                    let index = final_array.indexOf(elementInput.attr('id'));
                                    if (index == 0) {
                                        var duplicate_array = [
                                            elementOutput.attr('id'),
                                            ...final_array.slice()
                                        ];
                                        final_array = duplicate_array;
                                    } else if (final_array[index - 1] == '') {
                                        final_array[index - 1] = elementOutput.attr('id');
                                    } else {
                                        var duplicate_array = [
                                            ...final_array.slice(0, index - 1),
                                            elementOutput.attr('id'),
                                            ...final_array.slice(index)
                                        ];
                                        final_array = duplicate_array;
                                    }
                                } else {
                                    first_item_index = final_array.indexOf(elementOutput.attr('id'));
                                    last_item_index = final_array.indexOf(elementInput.attr('id'));
                                    if (final_array[first_item_index + 1] == '' && final_array[last_item_index - 1] == '' &&
                                        first_item_index + 1 == last_item_index - 1) {
                                        var duplicate_array = [
                                            ...final_array.slice(0, first_item_index + 1),
                                            ...final_array.slice(last_item_index)
                                        ];
                                        final_array = duplicate_array;
                                    }
                                }
                                $('.drop-pad').append('<div class="line" id="' + elementOutput.attr('id') + '-to-' +
                                    elementInput.attr('id') +
                                    '"><div class="path-cancel-icon"><i class="fa-solid fa-xmark"></i></div></div>');
                                $('.path-cancel-icon').on('click', removePath);
                                $('.line').css({
                                    'position': 'absolute',
                                    'background-color': 'white',
                                    'height': '2px',
                                    'transform-origin': 'left center'
                                });
                                var attachElementInput = $(elementInput).find('.attach-elements-in');
                                var attachElementOutput = $(elementOutput).find('.attach-elements-out');
                                if (attachElementInput && attachElementOutput) {
                                    var inputPosition = attachElementInput.offset();
                                    var outputPosition = attachElementOutput.offset();

                                    var x1 = inputPosition.left;
                                    var y1 = inputPosition.top;
                                    var x2 = outputPosition.left;
                                    var y2 = outputPosition.top;

                                    var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                                    var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);

                                    var lineId = elementOutput.attr('id') + '-to-' + elementInput.attr('id');
                                    var line = $('#' + lineId);

                                    line.css({
                                        'width': distance + 'px',
                                        'transform': 'rotate(' + angle + 'deg)',
                                        'top': y1 - 320 + 'px',
                                        'left': x1 - 207 + 'px'
                                    });
                                    elementInput = null;
                                    elementOutput = null;
                                }
                            }
                            $('.drop-pad-element').on('click', elementProperties);
                        }
                    }

                    function move() {
                        $('.element').on('mousedown', function(e) {
                            e.preventDefault();
                            var clone = $(this).clone().css({
                                'position': 'absolute',
                            });
                            $('body').append(clone);
                            chooseElement = clone;
                            id = chooseElement.attr('id') + '_' + ++count;
                            input_array.push(id);
                            chooseElement.attr('id', id);
                            chooseElement.attr('class', 'drop-pad-element');
                            chooseElement.removeClass('element');
                            chooseElement.css({
                                'display': 'flex',
                                'justify-content': 'space-between',
                                'align-items': 'center',
                                'background-color': '#1c1e22',
                                'min-height': '100px',
                                'max-height': 'fit-content',
                                'width': '350px',
                                'padding': '7px',
                                'border-radius': '17px',
                                'margin': '14px 0',
                                'cursor': 'pointer',
                                'border': '1px solid rgb(23, 172, 203)',
                            });
                            p = chooseElement.find('p');
                            p.css({
                                'color': '#fff',
                                'width': 'fit-content',
                            });
                            list_icon = chooseElement.find('.list-icon');
                            list_icon.css({
                                'padding': '7px',
                                'min-height': '100%',
                                'display': 'flex',
                                'align-items': 'center',
                                'justify-content': 'center',
                                'background-color': '#15171c',
                                'border-radius': '25%',
                                'margin-right': '25px',
                            });
                            i = chooseElement.find('.list-icon').find('i');
                            i.css({
                                'color': '#17accb',
                            });
                            p = chooseElement.find('.item_details').find('p');
                            p.css({
                                'margin': '0',
                                'width': '80%',
                            });
                            desc = chooseElement.find('.item_desc');
                            desc.css({
                                'display': 'block',
                                'max-width': '100%',
                                'text-overflow': 'ellipsis',
                                'font-size': '15px',
                            });
                            menu_icon = chooseElement.find('.menu-icon');
                            menu_icon.css({
                                'display': 'none',
                            });
                            cancel_icon = chooseElement.find('.cancel-icon');
                            cancel_icon.css({
                                'display': 'none',
                            });
                            attach_in = chooseElement.find('.attach-elements-in');
                            attach_in.css({
                                'display': 'none',
                            });
                            attach_on = chooseElement.find('.attach-elements-out');
                            attach_on.css({
                                'display': 'none',
                            });
                            $(document).on('mousemove', function(e) {
                                var x = e.pageX;
                                var y = e.pageY;
                                var element = $('.drop-pad').offset();
                                var element_x = element.left;
                                var max_x = element_x + $('.drop-pad').outerWidth();
                                var element_y = element.top;
                                var max_y = element_y + $('.drop-pad').outerHeight();
                                if (x < element_x && y < element_y) {
                                    chooseElement.css({
                                        left: element_x,
                                        top: element_y
                                    });
                                } else if (x < element_x && y > max_y) {
                                    chooseElement.css({
                                        left: element_x,
                                        top: max_y - chooseElement.height() - 30
                                    });
                                } else if (x > max_x && y < element_y) {
                                    chooseElement.css({
                                        left: max_x - chooseElement.width() - 30,
                                        top: element_y
                                    });
                                } else if (x > max_x && y > max_y) {
                                    chooseElement.css({
                                        left: max_x - chooseElement.width() - 30,
                                        top: max_y - chooseElement.height() - 30
                                    });
                                } else if (x < element_x && y > element_y && y < max_y) {
                                    chooseElement.css({
                                        left: element_x,
                                        top: y
                                    });
                                } else if (y < element_y && x > element_x && x < max_x) {
                                    chooseElement.css({
                                        left: x,
                                        top: element_y
                                    });
                                } else if (x > max_x && y > element_y && y < max_y) {
                                    chooseElement.css({
                                        left: max_x - chooseElement.width() - 30,
                                        top: y
                                    });
                                } else if (y > max_y && x > element_x && x < max_x) {
                                    chooseElement.css({
                                        left: x,
                                        top: max_y - chooseElement.height() - 30
                                    });
                                } else if (x > element_x && x < max_x && y > element_y && y < max_y) {
                                    chooseElement.css({
                                        left: x,
                                        top: y
                                    });
                                } else {
                                    chooseElement.css({
                                        left: element_x,
                                        top: element_y
                                    });
                                }
                            });
                        });

                        $(document).on('mouseup', function(e) {
                            if (chooseElement) {
                                chooseElement.css({
                                    'align-items': 'stretch',
                                });
                                attach_in = chooseElement.find('.attach-elements-in');
                                attach_in.css({
                                    'display': 'block',
                                });
                                attach_on = chooseElement.find('.attach-elements-out');
                                attach_on.css({
                                    'display': 'block',
                                });
                                cancel_icon = chooseElement.find('.cancel-icon');
                                cancel_icon.css({
                                    'display': 'flex',
                                });
                                var x = e.pageX;
                                var y = e.pageY;
                                var element = $('.drop-pad').offset();
                                var element_x = element.left;
                                var max_x = element_x + $('.drop-pad').outerWidth();
                                var element_y = element.top;
                                var max_y = element_y + $('.drop-pad').outerHeight();
                                if (x < element_x && y < element_y) {
                                    chooseElement.css({
                                        left: 0,
                                        top: 0,
                                        'border': 'none',
                                    });
                                } else if (x < element_x && y > max_y) {
                                    chooseElement.css({
                                        left: 0,
                                        top: max_y - chooseElement.height() - 360,
                                        'border': 'none',
                                    });
                                } else if (x > max_x && y < element_y) {
                                    chooseElement.css({
                                        left: max_x - chooseElement.width() - 230,
                                        top: element_y - 350,
                                        'border': 'none',
                                    });
                                } else if (x > max_x && y > max_y) {
                                    chooseElement.css({
                                        left: max_x - chooseElement.width() - 230,
                                        top: max_y - chooseElement.height() - 350,
                                        'border': 'none',
                                    });
                                } else if (x < element_x && y > element_y && y < max_y) {
                                    chooseElement.css({
                                        left: element_x - 230,
                                        top: y - 350,
                                        'border': 'none',
                                    });
                                } else if (y < element_y && x > element_x && x < max_x) {
                                    chooseElement.css({
                                        left: x - 230,
                                        top: element_y - 350,
                                        'border': 'none',
                                    });
                                } else if (x > max_x && y > element_y && y < max_y) {
                                    chooseElement.css({
                                        left: max_x - chooseElement.width() - 230,
                                        top: y - 350,
                                        'border': 'none',
                                    });
                                } else if (y > max_y && x > element_x && x < max_x) {
                                    chooseElement.css({
                                        left: x - 230,
                                        top: max_y - chooseElement.height() - 350,
                                        'border': 'none',
                                    });
                                } else if (x > element_x && x < max_x && y > element_y && y < max_y) {
                                    chooseElement.css({
                                        left: x - 230,
                                        top: y - 350,
                                        'border': 'none',
                                    });
                                } else {
                                    chooseElement.css({
                                        left: 0,
                                        top: 30,
                                        'border': 'none',
                                    });
                                }
                                $(document).off('mousemove');
                                $('.task-list').append(chooseElement);
                                $('.cancel-icon').on('click', removeElement);
                                $('.attach-elements-out').on('click', attachElementOutput);
                                $('.attach-elements-in').on('click', attachElementInput);
                                $('.drop-pad-element').on('click', elementProperties);
                                chooseElement.on('mousedown', startDragging);
                                chooseElement = null;
                            }
                        });
                    }

                    function startDragging(e) {
                        e.preventDefault();
                        var currentElement = $(this);

                        $(document).on('mousemove', function(e) {
                            var x = e.pageX;
                            var y = e.pageY;
                            var element = $('.drop-pad').offset();
                            var element_x = element.left;
                            var max_x = element_x + $('.drop-pad').outerWidth();
                            var element_y = element.top;
                            var max_y = element_y + $('.drop-pad').outerHeight();

                            if (x < element_x && y < element_y) {
                                currentElement.css({
                                    left: 0,
                                    top: 0,
                                    'border': 'none',
                                });
                            } else if (x < element_x && y > max_y) {
                                currentElement.css({
                                    left: 0,
                                    top: max_y - currentElement.height() - 330,
                                    'border': 'none',
                                });
                            } else if (x > max_x && y < element_y) {
                                currentElement.css({
                                    left: max_x - currentElement.width() - 240,
                                    top: element_y - 330,
                                    'border': 'none',
                                });
                            } else if (x > max_x && y > max_y) {
                                currentElement.css({
                                    left: max_x - currentElement.width() - 240,
                                    top: max_y - currentElement.height() - 330,
                                    'border': 'none',
                                });
                            } else if (x < element_x && y > element_y && y < max_y) {
                                currentElement.css({
                                    left: element_x - 230,
                                    top: y - 350,
                                    'border': 'none',
                                });
                            } else if (y < element_y && x > element_x && x < max_x) {
                                currentElement.css({
                                    left: x - 230,
                                    top: element_y - 350,
                                    'border': 'none',
                                });
                            } else if (x > max_x && y > element_y && y < max_y) {
                                currentElement.css({
                                    left: max_x - currentElement.width() - 230,
                                    top: y - 350,
                                    'border': 'none',
                                });
                            } else if (y > max_y && x > element_x && x < max_x) {
                                currentElement.css({
                                    left: x - 230,
                                    top: max_y - currentElement.height() - 350,
                                    'border': 'none',
                                });
                            } else if (x > element_x && x < max_x && y > element_y && y < max_y) {
                                currentElement.css({
                                    left: x - 230,
                                    top: y - 350
                                });
                            } else {
                                currentElement.css({
                                    left: 0,
                                    top: 30
                                });
                            }

                            var index = final_array.indexOf(currentElement.attr('id'));
                            var prev_element_id = final_array[index - 1];
                            var current_element_id = final_array[index];
                            var next_element_id = final_array[index + 1];

                            if (prev_element_id && current_element_id) {
                                if ($('.drop-pad').find('#' + prev_element_id + '-to-' + current_element_id)
                                    .length > 0) {
                                    var attachElementInput = $('#' + current_element_id).find(
                                        '.attach-elements-in');
                                    var attachElementOutput = $('#' + prev_element_id).find('.attach-elements-out');
                                    if (attachElementInput.length && attachElementOutput.length) {
                                        var inputPosition = attachElementInput.offset();
                                        var outputPosition = attachElementOutput.offset();

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
                            if (current_element_id && next_element_id) {
                                if ($('.drop-pad').find('#' + current_element_id + '-to-' + next_element_id)
                                    .length > 0) {
                                    var attachElementInput = $('#' + next_element_id).find('.attach-elements-in');
                                    var attachElementOutput = $('#' + current_element_id).find(
                                        '.attach-elements-out');
                                    if (attachElementInput.length && attachElementOutput.length) {
                                        var inputPosition = attachElementInput.offset();
                                        var outputPosition = attachElementOutput.offset();

                                        var x1 = inputPosition.left;
                                        var y1 = inputPosition.top;
                                        var x2 = outputPosition.left;
                                        var y2 = outputPosition.top;

                                        var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                                        var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);
                                        var lineId = current_element_id + '-to-' + next_element_id;
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
                });
            </script>
        @elseif (Str::contains(request()->url(), 'createcampaign'))
            <script>
                $(document).ready(function() {
                    var details = {};
                    $('.tab-pane.active').find('.connections').on('change', changeConnections);
                    $('.tab-pane.active').find('.campaign_name').on('change', changeName);
                    $('.campaign_tab').on('click', function(e) {
                        e.preventDefault();
                        $('.campaign_tab').removeClass('active');
                        $(this).addClass('active');
                        var id = $(this).data('bs-target');
                        $('.campaign_pane').removeClass('active');
                        $('#' + id).addClass('active');
                        if (details['campaign_name']) {
                            $('#' + id).find('#campaign_name').val(details['campaign_name']);
                        }
                        if (details['connections']) {
                            $('#' + id).find('#connections').val(details['connections']);
                        }
                        $('.tab-pane.active').find('.connections').on('change', changeConnections);
                        $('.tab-pane.active').find('.campaign_name').on('change', changeName);
                    });

                    function changeConnections(e) {
                        details['connections'] = $(this).val();
                    }

                    function changeName(e) {
                        details['campaign_name'] = $(this).val();
                    }
                    $('.nxt_btn').on('click', function(e) {
                        e.preventDefault();
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        var form = document.createElement('form');
                        form.setAttribute('method', 'POST');
                        form.setAttribute('action', "{{ route('campaigninfo') }}");
                        var csrfInput = document.createElement('input');
                        csrfInput.setAttribute('type', 'hidden');
                        csrfInput.setAttribute('name', '_token');
                        csrfInput.setAttribute('value', csrfToken);
                        form.appendChild(csrfInput);
                        var active = $('.active > .campaign_form')[0];
                        var input = document.createElement('input');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('name', 'campaign_type');
                        input.setAttribute('value', $(active).find('#campaign_type').val());
                        form.appendChild(input);
                        var input = document.createElement('input');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('name', 'campaign_name');
                        input.setAttribute('value', $(active).find('#campaign_name').val());
                        form.appendChild(input);
                        var input = document.createElement('input');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('name', 'campaign_url');
                        input.setAttribute('value', $(active).find('#campaign_url').val());
                        form.appendChild(input);
                        var input = document.createElement('input');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('name', 'connections');
                        input.setAttribute('value', $(active).find('#connections').val());
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    });
                });
            </script>
        @elseif (Str::contains(request()->url(), 'campaigninfo'))
            <script>
                $(document).ready(function() {
                    var linkedin_settings = [];
                    $('.next_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').next().click();
                        linkedin_settings['discover_premium_linked_accounts_only'] = $(
                                '#discover_premium_linked_accounts_only')
                            .prop('checked');
                        linkedin_settings['discover_leads_with_open_profile_status_only'] = $(
                            '#discover_leads_with_open_profile_status_only').prop('checked');
                        linkedin_settings['collect_contact_information'] = $('#collect_contact_information').prop(
                            'checked');
                        linkedin_settings['remove_leads_with_pending_connections'] = $(
                                '#remove_leads_with_pending_connections')
                            .prop('checked');
                    });
                    $('.prev_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').prev().click();
                        linkedin_settings['discover_premium_linked_accounts_only'] = $(
                                '#discover_premium_linked_accounts_only')
                            .prop('checked');
                        linkedin_settings['discover_leads_with_open_profile_status_only'] = $(
                            '#discover_leads_with_open_profile_status_only').prop('checked');
                        linkedin_settings['collect_contact_information'] = $('#collect_contact_information').prop(
                            'checked');
                        linkedin_settings['remove_leads_with_pending_connections'] = $(
                                '#remove_leads_with_pending_connections')
                            .prop('checked');
                    });
                    $('.linkedin_setting_switch').on('change', function(e) {
                        linkedin_settings[$(this).attr('id')] = $(this).prop('checked');
                    });
                    $('#create_sequence').on('click', function(e) {
                        e.preventDefault();
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        var form = document.createElement('form');
                        form.setAttribute('method', 'POST');
                        form.setAttribute('action', "{{ route('createcampaignfromscratch') }}");
                        var csrfInput = document.createElement('input');
                        csrfInput.setAttribute('type', 'hidden');
                        csrfInput.setAttribute('name', '_token');
                        csrfInput.setAttribute('value', csrfToken);
                        form.appendChild(csrfInput);
                        for (let key in linkedin_settings) {
                            var input = document.createElement('input');
                            input.setAttribute('type', 'hidden');
                            input.setAttribute('name', key);
                            input.setAttribute('value', linkedin_settings[key]);
                            form.appendChild(input);
                        }
                        for (let key in campaign_details) {
                            var input = document.createElement('input');
                            input.setAttribute('type', 'hidden');
                            input.setAttribute('name', key);
                            input.setAttribute('value', campaign_details[key]);
                            form.appendChild(input);
                        }
                        document.body.appendChild(form);
                        form.submit();
                    });
                });
            </script>
        @endif
    </footer>
</body>

</html>
