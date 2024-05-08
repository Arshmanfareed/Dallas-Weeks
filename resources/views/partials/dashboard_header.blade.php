<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap-grid.min.css"
        integrity="sha512-ZuRTqfQ3jNAKvJskDAU/hxbX1w25g41bANOVd1Co6GahIe2XjM6uVZ9dh0Nt3KFCOA061amfF2VeL60aJXdwwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css"
        integrity="sha512-W/zrbCncQnky/EzL+/AYwTtosvrM+YG/V6piQLSe2HuKS6cmbw89kjYkp3tWFn1dkWV7L1ruvJyKbLz73Vlgfg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('assets/js/custom_dashboard.js') }}"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    @if (request()->is('accdashboard', 'report', 'leads'))
        <script src="{{ asset('assets/js/chart_query.js') }}"></script>
    @endif

    <title>Dashboard</title>
</head>



<body>
    <style>
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
            /* Initially hide the loader */
        }

        .loader-inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        window.addEventListener("load", function() {
            // When the page is fully loaded, hide the loader
            var loader = document.getElementById("loader");
            loader.style.display = "none";
        });

        document.addEventListener("DOMContentLoaded", function() {
            // When DOM content is loaded (before images and other resources), show the loader
            var loader = document.getElementById("loader");
            loader.style.display = "block";
        });
    </script>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark justify-content-between dashboard_header">
            <a class="navbar-brand" href="{{ route('dashobardz') }}">Networked</a>

            <div class="right_nav">
                <ul class="d-flex list-unstyled">
                    <li><a href="#"><i class="fa-regular fa-envelope"></i></a></li>
                    <li><a href="#"><i class="fa-regular fa-bell"></i></a></li>
                    <li class="acc d-flex align-item-center">
                        <img src="{{ asset('/assets/img/acc.png') }}" alt="">
                        @php
                            $user = auth()->user();
                        @endphp
                        <span>{{ $user->name }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </li>
                    <li class="darkmode"><a href="javascript:;" id="darkModeToggle"><i class="fa-solid fa-sun"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="col bg-faded py-3 flex-grow-1">
        @yield('content')
    </main>
    <footer>
        <div id="loader">
            <div class="loader-inner"></div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        @if (Str::contains(request()->url(), URL('campaign/createcampaignfromscratch')))
            <script>
                $(document).ready(function() {
                    var settings = {!! $settings !!};
                    var choosedElement = null;
                    var inputElement = null;
                    var outputElement = null;
                    var elements_array = localStorage.getItem('elements_array');
                    var elements_data_array = localStorage.getItem('elements_data_array');
                    var condition = '';

                    if (elements_data_array) {
                        elements_data_array = JSON.parse(elements_data_array);
                    } else {
                        elements_data_array = {};
                        localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
                    }

                    if (elements_array) {
                        elements_array = JSON.parse(elements_array);
                        var maxDropPadHeight = 0;
                        for (var key in elements_array) {
                            if (elements_array.hasOwnProperty(key) && key != 'step-1') {
                                var value = elements_array[key];
                                var hyphenIndex = key.lastIndexOf('_');
                                var new_key = key.slice(0, hyphenIndex);
                                var clone = $('#' + new_key).clone();
                                clone.css({
                                    'position': 'absolute',
                                });
                                clone.attr('id', key);
                                clone.addClass('drop_element');
                                clone.addClass('drop-pad-element');
                                clone.addClass('placedElement');
                                clone.removeClass('drop_element');
                                clone.removeClass('element');
                                $('.task-list').append(clone);
                                $('.cancel-icon').on('click', removeElement);
                                $('.element_change_output').on('click', attachOutputElement);
                                $('.element_change_input').on('click', attachInputElement);
                                $('.drop-pad-element').on('click', elementProperties);
                                if (elements_data_array[key]['Days']) {
                                    clone.find('.item_days').html(elements_data_array[key]['Days']);
                                } else {
                                    clone.find('.item_days').html('0');
                                }
                                if (elements_data_array[key]['Hours']) {
                                    clone.find('.item_hours').html(elements_data_array[key]['Hours']);
                                } else {
                                    clone.find('.item_hours').html('0');
                                }
                                clone.on('mousedown', startDragging);
                                clone.css({
                                    left: value['position_x'] - 214,
                                    top: value['position_y'] - 345,
                                    border: 'none'
                                });
                                var newDropPadHeight = parseInt(clone.css('top')) + parseInt(clone.css('height')) + 30;
                                if (maxDropPadHeight < newDropPadHeight) {
                                    maxDropPadHeight = newDropPadHeight;
                                    $('.drop-pad').css('height', maxDropPadHeight + "px");
                                }
                            }
                        }

                        for (var key in elements_array) {
                            current_element = key;
                            if (elements_array[current_element]['0'] != '') {
                                $('#' + current_element).find('.condition_false').on("click", function(e) {
                                    e.stopPropagation();
                                    attachOutputElement();
                                }).trigger("click");
                                $('#' + elements_array[current_element]['0']).find('.element_change_input').on("click",
                                    function(e) {
                                        e.stopPropagation();
                                        attachInputElement();
                                    }).trigger("click");
                            }
                            if (elements_array[current_element]['1'] != '') {
                                $('#' + current_element).find('.condition_true').on("click", function(e) {
                                    e.stopPropagation();
                                    attachOutputElement();
                                }).trigger("click");
                                $('#' + elements_array[current_element]['1']).find('.element_change_input').on("click",
                                    function(e) {
                                        e.stopPropagation();
                                        attachInputElement();
                                    }).trigger("click");
                            }
                            $('#' + current_element).css({
                                left: '-=20px',
                            });
                            if ($('#' + current_element).width() > 365) {
                                $('#' + current_element).css({
                                    left: '-=10px',
                                });
                            }
                            $('#properties').removeClass('active');
                            $('#properties-btn').removeClass('active');
                            $('#element-list-btn').addClass('active');
                            $('#element-list').addClass('active');
                        }
                    } else {
                        elements_array = {};
                        elements_data_array = {};
                        elements_array['step-1'] = {};
                        elements_array['step-1'][0] = '';
                        elements_array['step-1'][1] = '';
                        localStorage.setItem('elements_array', JSON.stringify(elements_array));
                        localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
                    }

                    placeElement();
                    $('.element_change_output').on('click', attachOutputElement);

                    $('.placedElement').css({
                        'border': 'none',
                    });

                    $('.placedElement .cancel-icon').css({
                        'display': 'none',
                    });

                    function placeElement(e) {
                        $('.element').on('mousedown', function(e) {
                            e.preventDefault();
                            var clone = $(this).clone().css({
                                'position': 'absolute',
                            });
                            $('body').append(clone);
                            choosedElement = clone;
                            id = choosedElement.attr('id') + '_' + Math.floor(10000 + Math.random() * 90000);
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
                                } else if ((x > element_x && x < max_x) && (y > element_y && y < max_y)) {
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
                                choosedElement.addClass('placedElement');
                                choosedElement.removeClass('drop_element');
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
                                } else if (x < element_x && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: 0,
                                        top: y - 330,
                                    });
                                } else if (y < element_y && (x > element_x && x < max_x)) {
                                    choosedElement.css({
                                        left: x - 210,
                                        top: 0,
                                    });
                                } else if (y > max_y && (x > element_x && x < max_x)) {
                                    choosedElement.css({
                                        left: x - 210,
                                        top: max_y - 310,
                                    });
                                } else if ((x > element_x && x < max_x) && (y > element_y && y < max_y)) {
                                    choosedElement.css({
                                        left: x - 210,
                                        top: y - 330,
                                    });
                                } else if (x > max_x && y < element_y) {
                                    choosedElement.css({
                                        left: max_x - 130,
                                        top: 0,
                                    });
                                } else if (x > max_x && (y > element_y && y < max_y)) {
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
                                elements_array[id]['position_x'] = choosedElement.offset().left;
                                elements_array[id]['position_y'] = choosedElement.offset().top;
                                localStorage.setItem('elements_array', JSON.stringify(elements_array));
                                localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
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
                        localStorage.setItem('elements_array', JSON.stringify(elements_array));
                        localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
                        $(this).parent().remove();
                        $('.element-content').removeClass('active');
                        $('#element-list').addClass('active');
                        $('.element-btn').removeClass('active');
                        $('#element-list-btn').addClass('active');
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
                        localStorage.setItem('elements_array', JSON.stringify(elements_array));
                        localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
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
                            localStorage.setItem('elements_array', JSON.stringify(elements_array));
                            localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
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
                        html2canvas(document.getElementById('capture')).then(function(canvas) {
                            var img = canvas.toDataURL();
                            elements_array = JSON.parse(JSON.stringify(elements_array));
                            elements_data_array = JSON.parse(JSON.stringify(elements_data_array));
                            $('.drop-pad-element .cancel-icon').css({
                                'display': 'none',
                            });
                            $('.drop-pad-element').css({
                                "z-index": "0",
                                "border": "none",
                            });
                            $.ajax({
                                url: "{{ route('createCampaign') }}",
                                type: 'POST',
                                dataType: 'json',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    'final_data': elements_data_array,
                                    'final_array': elements_array,
                                    'settings': settings,
                                    'img_url': img
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
                    });

                    function onSave() {
                        var property = $('.element_properties');
                        var elements = property.find('.property_item');
                        var element_name = property.find('.element_name').data('bs-target');
                        elements.each(function(index, element) {
                            var input = $(element).find('.property_input').val();
                            $(element).find('.property_input').css({
                                'border': '2px solid #ddd',
                                'box-shadow': 'none',
                            });
                            var p = $(element).find('.property_input').attr('name');
                            elements_data_array[element_name][p] = input;
                            localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
                        });
                        $('#' + element_name).css({
                            'border': '1px solid rgb(23, 172, 203)',
                        });
                        $('#' + element_name).find('.item_name').css({
                            'color': '#fff',
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
                        $('#element-list').removeClass('active');
                        $('#properties').addClass('active');
                        $('#element-list-btn').removeClass('active');
                        $('#properties-btn').addClass('active');
                        var property_input = $('.property_input');
                        if (property_input.length > 0 && $(this).prop('id') != $('.element_name').data('bs-target')) {
                            for (var i = 0; i < property_input.length; i++) {
                                var input = property_input.eq(i);
                                var target_element = $(property_input[0]).closest('.element_properties').find(
                                    '.element_name').data('bs-target');
                                if (input.prop('required') && input.val() == '') {
                                    input.addClass('error');
                                    $('#' + target_element).addClass('error');
                                    $('#' + target_element).find('.item_name').addClass('error');
                                } else {
                                    input.removeClass('error');
                                    $('#' + target_element).removeClass('error');
                                    $('#' + target_element).find('.item_name').removeClass('error');
                                    $('.drop-pad-element#' + target_element).addClass('success');
                                }
                            }
                        }
                        $(this).removeClass('error');
                        $(this).find('.item_name').removeClass('error');
                        $('.drop-pad-element .cancel-icon').css({
                            'display': 'none',
                        });
                        $('#properties').empty();
                        $('.drop-pad-element').css({
                            "z-index": "0",
                            "border": "none",
                        });
                        $(this).css({
                            "z-index": "999",
                            "border": "1px solid rgb(23, 172, 203)",
                        });
                        $(this).find('.cancel-icon').css({
                            'display': 'flex',
                        });
                        $(this).find('.item_name').css({
                            'color': '#fff',
                        });
                        var item_slug = $(this).data('filterName');
                        var item_name = $(this).find('.item_name').text();
                        var list_icon = $(this).find('.list-icon').html();
                        var item_id = $(this).attr('id');
                        var name_html = '';
                        if (elements_data_array[item_id] == null) {
                            $.ajax({
                                url: "{{ route('getcampaignelementbyslug', ':slug') }}".replace(':slug',
                                    item_slug),
                                type: 'GET',
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        name_html += '<div class="element_properties">';
                                        name_html += '<div class="element_name" data-bs-target="' +
                                            item_id +
                                            '">' +
                                            list_icon +
                                            '<p>' + item_name + '</p></div>';
                                        arr = {};
                                        response.properties.forEach(property => {
                                            name_html += '<div class="property_item">';
                                            name_html += '<p>' + property['property_name'] + '</p>';
                                            name_html += '<input type="' + property['data_type'] +
                                                '" placeholder="Enter the ' +
                                                property['property_name'] +
                                                '" class="property_input" name="' + property['id'] +
                                                '"';
                                            if (property['optional'] == '1') {
                                                name_html += 'required';
                                            }
                                            name_html += '>';
                                            name_html += '</div>';
                                            arr[property['id']] = '';
                                        });
                                        elements_data_array[item_id] = arr;
                                        localStorage.setItem('elements_data_array', JSON.stringify(
                                            elements_data_array));
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
                                    $('.property_input').on('input', propertyInput);
                                },
                                error: function(xhr, status, error) {
                                    console.error(xhr.responseText);
                                },
                            });
                        } else {
                            name_html += '<div class="element_properties">';
                            name_html += '<div class="element_name" data-bs-target="' + item_id + '">' +
                                list_icon +
                                '<p>' + item_name + '</p></div>';
                            elements = elements_data_array[item_id];
                            var ajaxRequests = [];
                            for (const key in elements) {
                                ajaxRequests.push($.ajax({
                                    url: "{{ route('getPropertyDatatype', [':id', ':element_slug']) }}"
                                        .replace(':id', key).replace(':element_slug', item_slug),
                                    type: 'GET',
                                    dataType: 'json'
                                }).then(function(response) {
                                    if (response.success) {
                                        const value = elements[key];
                                        name_html += '<div class="property_item">';
                                        name_html += '<p>' + response.property['property_name'] + '</p>';
                                        name_html += '<input type="' + response.property['data_type'];
                                        if (value == '') {
                                            name_html += '" placeholder="Enter the ' + response.property[
                                                    'property_name'] +
                                                '" class="property_input" name="' + key + '"';
                                        } else {
                                            name_html += '" value="' + value + '" class="property_input"';
                                        }
                                        if (response.optional == '1') {
                                            name_html += 'required';
                                        }
                                        name_html += '>';
                                        name_html += '</div>';
                                    } else {
                                        name_html += '<div class="property_item">';
                                        name_html += '<p>' + key + '</p>';
                                        name_html += '<input type="text" placeholder="' + value +
                                            '" class="property_input" name="' + key + '">';
                                        name_html += '</div>';
                                    }
                                }));
                            }
                            $.when.apply($, ajaxRequests).then(function() {
                                name_html +=
                                    '</div><div class="save-btns"><button id="save">Save</button></div>';
                                $('#properties').html(name_html);
                                $('#save').on('click', onSave);
                                $('.property_input').on('input', propertyInput);
                            });
                        }
                    }

                    function propertyInput(e) {
                        var element_id = $(this).parent().parent().find('.element_name').data('bs-target');
                        if (element_id != undefined) {
                            if ($(this).parent().find('p').text() == 'Days') {
                                if ($(this).val() != '') {
                                    $('#' + element_id).find('.item_days').html($(this).val());
                                } else {
                                    $('#' + element_id).find('.item_days').html(0);
                                }
                            } else if ($(this).parent().find('p').text() == 'Hours') {
                                if ($(this).val() != '') {
                                    $('#' + element_id).find('.item_hours').html($(this).val());
                                } else {
                                    $('#' + element_id).find('.item_hours').html(0);
                                }
                            }
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
                            currentElement.find('.cancel-icon').css({
                                'display': 'none',
                            });
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
                            id = currentElement.attr('id');
                            elements_array[id]['position_x'] = currentElement.offset().left;
                            elements_array[id]['position_y'] = currentElement.offset().top;
                            localStorage.setItem('elements_array', JSON.stringify(elements_array));
                            localStorage.setItem('elements_data_array', JSON.stringify(elements_data_array));
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
        @elseif (Str::contains(request()->url(), URL('campaign/createcampaign')))
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
        @elseif (Str::contains(request()->url(), URL('campaign/campaigninfo')))
            <script>
                $(document).ready(function() {
                    localStorage.removeItem('elements_array');
                    localStorage.removeItem('elements_data_array');
                    var campaign_details = {!! $campaign_details_json !!};
                    var form = $('#settings');
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_type').val(campaign_details[
                        'campaign_type']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_name').val(campaign_details[
                        'campaign_name']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_url').val(campaign_details[
                        'campaign_url']));
                    if (campaign_details['connections'] != undefined) {
                        form.append($('<input>').attr('type', 'hidden').attr('name', 'connections').val(campaign_details[
                            'connections']));
                    }
                    /* Before submitting make every checkbox having valing */
                    $('#create_sequence').on('click', function(e) {
                        e.preventDefault();
                        var form = $('#settings');
                        var inputs = form.find('input[type="checkbox"]');
                        inputs.each(function() {
                            if ($(this).is(':checked')) {
                                $(this).attr('value', 'yes');
                            } else {
                                $(this).prop('checked', true);
                                $(this).attr('value', 'no');
                            }
                        });
                        form.submit();
                    });
                    $('.next_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').next().click();
                    });
                    $('.prev_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').prev().click();
                    });
                    /* Changing tabs among settings */
                    $('.schedule-btn').on('click', function(e) {
                        e.preventDefault();
                        var targetTab = $('#' + $(this).data('tab'));
                        var parent = $(this).parent().parent();
                        $(parent).find('.schedule-content.active').removeClass('active');
                        $(targetTab).addClass('active');
                        $(parent).find('.schedule-btn.active').removeClass('active');
                        $(this).addClass('active');
                    });
                    $('.schedule_days').on('change', function(e) {
                        var day = $(this).val();
                        if ($(this).prop('checked')) {
                            $('#' + day + '_start_time').val('09:00:00');
                            $('#' + day + '_end_time').val('17:00:00');
                        } else {
                            $('#' + day + '_start_time').val('');
                            $('#' + day + '_end_time').val('');
                        }
                    });
                    $('.add_schedule').on('click', function(e) {
                        e.preventDefault();
                        var form = $('.schedule_form');
                        var csrfToken = "{{ csrf_token() }}";
                        var scheduleDays = form.find('input[type="checkbox"]');
                        scheduleDays.each(function() {
                            if ($(this).is(':checked')) {
                                $(this).attr('value', 'true');
                            } else {
                                $(this).prop('checked', true);
                                $(this).attr('value', 'false');
                            }
                        });
                        $.ajax({
                            url: "{{ route('createSchedule') }}",
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            data: form.serialize(),
                            success: function(response) {
                                if (response.success) {
                                    $('#schedule_modal').modal('hide');
                                    schedules = response.schedules;
                                    html = ``;
                                    for (var i = 0; i < schedules.length; i++) {
                                        schedule = schedules[i];
                                        html +=
                                            `<li><div class="row schedule_list_item"><div class="col-lg-1 schedule_item">`;
                                        html +=
                                            `<input type="radio" name="email_settings_schedule_id" class="schedule_id"`;
                                        if (schedule['user_id'] == 0) {
                                            html += `checked `;
                                        }
                                        html += `value=` + schedule['id'] + `></div>`;
                                        html += `<div class="col-lg-1 schedule_avatar">S</div>`;
                                        html +=
                                            `<div class="col-lg-3 schedule_name"><i class="fa-solid fa-circle-check"`;
                                        html += `style="color: #4bcea6;"></i>`;
                                        html += `<span>` + schedule['schedule_name'] + `</span></div>`;
                                        html += `<div class="col-lg-6 schedule_days">`;
                                        var schedule_days = schedule['Days'];
                                        html += `<ul class="schedule_day_list">`;
                                        for (var j = 0; j < schedule_days.length; j++) {
                                            html += `<li `;
                                            html += `class="schedule_day `;
                                            day = schedule_days[j];
                                            if (day['is_active'] == '1') {
                                                html += `selected_day`;
                                            }
                                            html += `">`;
                                            html += day['schedule_day'].toUpperCase() + `</li>`;
                                        }
                                        html += `<li class="schedule_time"><button href="javascript:;"`;
                                        html += `type="button" class="btn" data-bs-toggle="modal"`;
                                        html +=
                                            `data-bs-target="#time_modal"><i class="fa-solid fa-globe"`;
                                        html += `style="color: #16adcb;"></i></button></li></ul>`;
                                        html += `</div><div class="col-lg-1 schedule_menu_btn">`;
                                        html +=
                                            `<i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>`;
                                        html += `</div></div></li>`;
                                    }
                                    $('#schedule_list_1').html(html);
                                    $('#schedule_list_2').html(html.replace(
                                        'email_settings_schedule_id',
                                        'global_settings_schedule_id'));
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            },
                        });
                    });
                    $('.search_schedule').on('input', function(e) {
                        var schedule_input = $(this);
                        var search = $(this).val();
                        if (search === '') {
                            search = 'null';
                        }
                        $.ajax({
                            url: "{{ route('filterSchedule', ':search') }}".replace(':search', search),
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    schedules = response.schedules;
                                    var schedule_list = $(schedule_input).parent().next(
                                        '.schedule_list');
                                    html = ``;
                                    for (var i = 0; i < schedules.length; i++) {
                                        schedule = schedules[i];
                                        html +=
                                            `<li><div class="row schedule_list_item"><div class="col-lg-1 schedule_item">`;
                                        html +=
                                            `<input type="radio" name="email_settings_schedule_id" class="schedule_id"`;
                                        if (schedule['user_id'] == 0 || i == 0) {
                                            html += `checked `;
                                        }
                                        html += `value=` + schedule['id'] + `></div>`;
                                        html += `<div class="col-lg-1 schedule_avatar">S</div>`;
                                        html +=
                                            `<div class="col-lg-3 schedule_name"><i class="fa-solid fa-circle-check"`;
                                        html += `style="color: #4bcea6;"></i>`;
                                        html += `<span>` + schedule['schedule_name'] + `</span></div>`;
                                        html += `<div class="col-lg-6 schedule_days">`;
                                        var schedule_days = schedule['Days'];
                                        html += `<ul class="schedule_day_list">`;
                                        for (var j = 0; j < schedule_days.length; j++) {
                                            html += `<li `;
                                            html += `class="schedule_day `;
                                            day = schedule_days[j];
                                            if (day['is_active'] == '1') {
                                                html += `selected_day`;
                                            }
                                            html += `">`;
                                            html += day['schedule_day'].toUpperCase() + `</li>`;
                                        }
                                        html += `<li class="schedule_time"><button href="javascript:;"`;
                                        html += `type="button" class="btn" data-bs-toggle="modal"`;
                                        html +=
                                            `data-bs-target="#time_modal"><i class="fa-solid fa-globe"`;
                                        html += `style="color: #16adcb;"></i></button></li></ul>`;
                                        html += `</div><div class="col-lg-1 schedule_menu_btn">`;
                                        html +=
                                            `<i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>`;
                                        html += `</div></div></li>`;
                                    }
                                    if (schedule_list.attr('id') == 'schedule_list_2') {
                                        html = html.replace('email_settings_schedule_id',
                                            'global_settings_schedule_id');
                                        $('#schedule_list_2').html(html);
                                    } else {
                                        $('#schedule_list_1').html(html);
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                if (xhr.status == 404) {
                                    var schedule_list = $(schedule_input).parent().next(
                                        '.schedule_list');
                                    html = ``;
                                    html +=
                                        `<li><div class="text-center text-danger">Not Found!</div></li>`;
                                    if (schedule_list.attr('id') == 'schedule_list_2') {
                                        $('#schedule_list_2').html(html);
                                    } else {
                                        $('#schedule_list_1').html(html);
                                    }
                                }
                            },
                        });
                    });
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('campaign/campaignDetails')))
            <script>
                $(document).ready(function() {
                    /* Making every setting to unchangable */
                    $('.linkedin_setting_switch').prop('disabled', true);
                    var campaign_id = {{ $campaign->id }};
                    var inputElement = null;
                    var outputElement = null;
                    var condition = '';
                    var element_data_array = {};
                    $.ajax({
                        url: "{{ route('getElements', ':campaign_id') }}".replace(':campaign_id', campaign_id),
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                elements_array = response.elements_array;
                                path = response.path;
                                if (elements_array) {
                                    var maxDropPadHeight = 0;
                                    html = ``;
                                    html +=
                                        `<div class="step-1 element_item" id="step-1"><div class="list-icon">`;
                                    html +=
                                        `<i class="fa-solid fa-certificate"></i></div><div class="item_details">`;
                                    html +=
                                        `<p class="item_name">Lead Source (Step 1)</p><p class="item_desc">`;
                                    html +=
                                        `<i class="fa-solid fa-clock"></i>Wait for: <span class="item_days">0</span>`;
                                    html += ` days <span class="item_hours">0</span> hours</p></div>`;
                                    html +=
                                        `<div class="element_change_output attach-elements-out condition_true"></div></div>`;
                                    $('.task-list').append(html);
                                    for (var i = 0; i < elements_array.length; i++) {
                                        var element = elements_array[i]['original_element'];
                                        var original_properties = elements_array[i]['properties'];
                                        var days = 0;
                                        var hours = 0;
                                        for (var j = 0; j < original_properties.length; j++) {
                                            if (original_properties[j]['original_properties'][
                                                    'property_name'
                                                ] == "Hours") {
                                                hours = original_properties[j]['value'];
                                            } else if (original_properties[j]['original_properties'][
                                                    'property_name'
                                                ] == "Days") {
                                                days = original_properties[j]['value'];
                                            }
                                        }
                                        html = ``;
                                        if (element['is_conditional'] == '1') {
                                            html +=
                                                `<div class="element_item drop-pad-element placedElement" id="`;
                                            html += elements_array[i]['id'] + `"`;
                                            html += `data-filter-name="` + element['element_name'];
                                            html += `" style="position: absolute;">`;
                                            html +=
                                                `<div class="element_change_input conditional-elements conditional-elements-in"></div>`;
                                            html +=
                                                `<div class="cancel-icon"><i class="fa-solid fa-x"></i></div>`;
                                            html += `<div class="list-icon">` + element['element_icon'] +
                                                `</div>`;
                                            html += `<div class="item_details"><p class="item_name">`;
                                            html += element['element_name'] + `</p>`;
                                            html +=
                                                `<p class="item_desc"><i class="fa-solid fa-clock"></i>Check after: `;
                                            html +=
                                                `<span class="item_days">` + days + `</span> days `;
                                            html += `<span class="item_hours">` + hours + `</span> hours`;
                                            html += `</p></div>`;
                                            html +=
                                                `<div class="menu-icon"><i class="fa-solid fa-bars"></i></div>`;
                                            html +=
                                                `<div class="conditional-elements conditional-elements-out">`;
                                            html +=
                                                `<div class="element_change_output condition_true"><i class="fa-solid fa-check"></i>`;
                                            html += `</div><div class="element_change_output condition_false">`;
                                            html += `<i class="fa-solid fa-xmark"></i></div></div></div>`;
                                        } else {
                                            html +=
                                                `<div class="element_item drop-pad-element placedElement" id="`;
                                            html += elements_array[i]['id'] + `"`;
                                            html += `data-filter-name="` + element['element_name'];
                                            html += `" style="position: absolute;">`;
                                            html +=
                                                `<div class="element_change_input attach-elements attach-elements-in"></div>`;
                                            html +=
                                                `<div class="cancel-icon"><i class="fa-solid fa-x"></i></div>`;
                                            html += `<div class="list-icon">` + element['element_icon'] +
                                                `</div>`;
                                            html += `<div class="item_details"><p class="item_name">`;
                                            html += element['element_name'] + `</p>`;
                                            html +=
                                                `<p class="item_desc"><i class="fa-solid fa-clock"></i>Wait for: `;
                                            html +=
                                                `<span class="item_days">` + days + `</span> days `;
                                            html += `<span class="item_hours">` + hours +
                                                `</span> hours</p></div>`;
                                            html +=
                                                `<div class="menu-icon"><i class="fa-solid fa-bars"></i></div>`;
                                            html +=
                                                `<div class="element_change_output attach-elements attach-elements-out condition_true">`;
                                            html += `</div></div>`;
                                        }
                                        $('.task-list').append(html);
                                        var left = elements_array[i]['position_x'];
                                        var top = elements_array[i]['position_y'];
                                        var clone = $('#' + elements_array[i]['id']);
                                        $(clone).css({
                                            'left': left,
                                            'top': parseInt(top) + 30
                                        });
                                        var newDropPadHeight = parseInt($(clone).css('top')) + parseInt($(clone)
                                            .css('height')) + 30;
                                        if (maxDropPadHeight < newDropPadHeight) {
                                            maxDropPadHeight = newDropPadHeight;
                                            $('.drop-pad').css('height', maxDropPadHeight + "px");
                                        }
                                    }
                                    for (var i = 0; i < path.length; i++) {
                                        current_element = path[i]['current_element_id'];
                                        if (path[i]['next_false_element_id'] != '') {
                                            $('#' + current_element).find('.condition_false').on("click",
                                                attachOutputElement).trigger("click");
                                            $('#' + path[i]['next_false_element_id']).find(
                                                    '.element_change_input').on("click", attachInputElement)
                                                .trigger("click");
                                        }
                                        if (path[i]['next_true_element_id'] != '') {
                                            $('#' + current_element).find('.condition_true').on("click",
                                                attachOutputElement).trigger("click");
                                            $('#' + path[i]['next_true_element_id']).find(
                                                    '.element_change_input').on("click", attachInputElement)
                                                .trigger("click");
                                        }
                                        $('#' + current_element).css({
                                            left: '-=20px',
                                        });
                                        if ($('#' + current_element).width() > 365) {
                                            $('#' + current_element).css({
                                                left: '-=10px',
                                            });
                                        }
                                    }
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });

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
                                    var attachOutputElement = $(outputElement).find(
                                        '.element_change_output.condition_true');
                                } else if (condition == 'False') {
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
                                        'transform': 'rotate(' + (angle - 3) + 'deg)',
                                        'top': (y1 - 300) + 'px',
                                        'left': (x1 - 220) + 'px'
                                    });
                                    inputElement = null;
                                    outputElement = null;
                                }
                            }
                        }
                    }
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('campaign/editcampaign')))
            <script>
                $(document).ready(function() {
                    var edit_campaign_details = JSON.parse(sessionStorage.getItem('edit_campaign_details')) || {};
                    if (edit_campaign_details['campaign_type'] == undefined) {
                        edit_campaign_details['campaign_type'] = "{!! $campaign->campaign_type !!}";
                    }
                    var campaign_pane = $('.campaign_pane');
                    for (var i = 0; i < campaign_pane.length; i++) {
                        var campaignType = $(campaign_pane[i]).find('#campaign_type').val();
                        if (campaignType == edit_campaign_details['campaign_type']) {
                            $(campaign_pane[i]).addClass('active');
                            $('[data-bs-target="' + $(campaign_pane[i]).attr('id') + '"]').addClass('active');
                        }
                    }
                    if (edit_campaign_details['campaign_name'] == undefined || edit_campaign_details['campaign_url'] ==
                        undefined || edit_campaign_details['campaign_connection'] == undefined) {
                        edit_campaign_details['campaign_name'] = "{!! $campaign->campaign_name !!}";
                        edit_campaign_details['campaign_url'] = "{!! $campaign->campaign_url !!}";
                        edit_campaign_details['campaign_connection'] = "{!! $campaign->campaign_connection !!}";
                        $('.campaign_pane.active').find('form').find('#campaign_name').val(edit_campaign_details[
                            'campaign_name']);
                        if ($('.campaign_pane.active').find('form').attr('id') != 'campaign_form_4') {
                            $('.campaign_pane.active').find('form').find('#campaign_url').val(edit_campaign_details[
                                'campaign_url']);
                        }
                        if ($('.campaign_pane.active').find('form').attr('id') != 'campaign_form_4' && $(
                                '.campaign_pane.active').find('form').attr('id') != 'campaign_form_3') {
                            $('.campaign_pane.active').find('form').find('#connections').val(edit_campaign_details[
                                'campaign_connection']);
                        }
                    } else {
                        var active_form = $('.campaign_pane.active').find('form');
                        active_form.find('#campaign_name').val(edit_campaign_details['campaign_name']);
                        if (active_form.attr('id') != 'campaign_form_4') {
                            active_form.find('#campaign_url').val(edit_campaign_details['campaign_url']);
                        }
                        if (active_form.attr('id') != 'campaign_form_4' && active_form.attr('id') != 'campaign_form_3') {
                            active_form.find('#connections').val(edit_campaign_details['campaign_connection']);
                        }
                    }
                    $('.campaign_name').on('change', function(e) {
                        edit_campaign_details['campaign_name'] = $(this).val();
                        sessionStorage.setItem('edit_campaign_details', JSON.stringify(edit_campaign_details));
                    });
                    $('.campaign_url').on('change', function(e) {
                        edit_campaign_details['campaign_url'] = $(this).val();
                        sessionStorage.setItem('edit_campaign_details', JSON.stringify(edit_campaign_details));
                    });
                    $('.connections').on('change', function(e) {
                        edit_campaign_details['campaign_connection'] = $(this).val();
                        sessionStorage.setItem('edit_campaign_details', JSON.stringify(edit_campaign_details));
                    });
                    $('.campaign_tab').on('click', function(e) {
                        e.preventDefault();
                        $('.campaign_tab').removeClass('active');
                        $(this).addClass('active');
                        var id = $(this).data('bs-target');
                        $('.campaign_pane').removeClass('active');
                        $('#' + id).addClass('active');
                        var new_form = $('#' + id).find('form');
                        edit_campaign_details['campaign_type'] = new_form.find('#campaign_type').val();
                        sessionStorage.setItem('edit_campaign_details', JSON.stringify(edit_campaign_details));
                        new_form.find('#campaign_name').val(edit_campaign_details['campaign_name']);
                        if (new_form.attr('id') != 'campaign_form_4') {
                            new_form.find('#campaign_url').val(edit_campaign_details['campaign_url']);
                        }
                        if (new_form.attr('id') != 'campaign_form_4' && new_form.attr('id') != 'campaign_form_3') {
                            new_form.find('#connections').val(edit_campaign_details['campaign_connection']);
                        }
                    });
                    $('.nxt_btn').on('click', function(e) {
                        e.preventDefault();
                        var form = $('.campaign_pane.active').find('form');
                        form.submit();
                    });
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('campaign/editCampaignInfo')))
            <script>
                $(document).ready(function() {
                    localStorage.removeItem('elements_array');
                    localStorage.removeItem('elements_data_array');
                    var campaign_details = {!! $campaign_details_json !!};
                    var form = $('#settings');
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_type').val(campaign_details[
                        'campaign_type']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_name').val(campaign_details[
                        'campaign_name']));
                    form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_url').val(campaign_details[
                        'campaign_url']));
                    if (campaign_details['campaign_connection'] != undefined) {
                        form.append($('<input>').attr('type', 'hidden').attr('name', 'campaign_connection').val(
                            campaign_details[
                                'campaign_connection']));
                    }
                    /* Before submitting make every checkbox having valing */
                    $('#create_sequence').on('click', function(e) {
                        e.preventDefault();
                        var form = $('#settings');
                        var inputs = form.find('input[type="checkbox"]');
                        inputs.each(function() {
                            if ($(this).is(':checked')) {
                                $(this).attr('value', 'yes');
                            } else {
                                $(this).prop('checked', true);
                                $(this).attr('value', 'no');
                            }
                        });
                        form.submit();
                    });
                    $('.next_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').next().click();
                    });
                    $('.prev_tab').on('click', function(e) {
                        $(this).closest('.comp_tabs').find('.nav-tabs .nav-link.active').prev().click();
                    });
                    /* Changing tabs among settings */
                    $('.schedule-btn').on('click', function(e) {
                        e.preventDefault();
                        var targetTab = $('#' + $(this).data('tab'));
                        var parent = $(this).parent().parent();
                        $(parent).find('.schedule-content.active').removeClass('active');
                        $(targetTab).addClass('active');
                        $(parent).find('.schedule-btn.active').removeClass('active');
                        $(this).addClass('active');
                    });
                    $('.schedule_days').on('change', function(e) {
                        var day = $(this).val();
                        if ($(this).prop('checked')) {
                            $('#' + day + '_start_time').val('09:00:00');
                            $('#' + day + '_end_time').val('17:00:00');
                        } else {
                            $('#' + day + '_start_time').val('');
                            $('#' + day + '_end_time').val('');
                        }
                    });
                    $('.add_schedule').on('click', function(e) {
                        e.preventDefault();
                        var form = $('.schedule_form');
                        var csrfToken = "{{ csrf_token() }}";
                        var scheduleDays = form.find('input[type="checkbox"]');
                        scheduleDays.each(function() {
                            if ($(this).is(':checked')) {
                                $(this).attr('value', 'true');
                            } else {
                                $(this).prop('checked', true);
                                $(this).attr('value', 'false');
                            }
                        });
                        $.ajax({
                            url: "{{ route('createSchedule') }}",
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            data: form.serialize(),
                            success: function(response) {
                                if (response.success) {
                                    $('#schedule_modal').modal('hide');
                                    schedules = response.schedules;
                                    html = ``;
                                    for (var i = 0; i < schedules.length; i++) {
                                        schedule = schedules[i];
                                        html +=
                                            `<li><div class="row schedule_list_item"><div class="col-lg-1 schedule_item">`;
                                        html +=
                                            `<input type="radio" name="email_settings_schedule_id" class="schedule_id"`;
                                        if (schedule['user_id'] == 0) {
                                            html += `checked `;
                                        }
                                        html += `value=` + schedule['id'] + `></div>`;
                                        html += `<div class="col-lg-1 schedule_avatar">S</div>`;
                                        html +=
                                            `<div class="col-lg-3 schedule_name"><i class="fa-solid fa-circle-check"`;
                                        html += `style="color: #4bcea6;"></i>`;
                                        html += `<span>` + schedule['schedule_name'] + `</span></div>`;
                                        html += `<div class="col-lg-6 schedule_days">`;
                                        var schedule_days = schedule['Days'];
                                        html += `<ul class="schedule_day_list">`;
                                        for (var j = 0; j < schedule_days.length; j++) {
                                            html += `<li `;
                                            html += `class="schedule_day `;
                                            day = schedule_days[j];
                                            if (day['is_active'] == '1') {
                                                html += `selected_day`;
                                            }
                                            html += `">`;
                                            html += day['schedule_day'].toUpperCase() + `</li>`;
                                        }
                                        html += `<li class="schedule_time"><button href="javascript:;"`;
                                        html += `type="button" class="btn" data-bs-toggle="modal"`;
                                        html +=
                                            `data-bs-target="#time_modal"><i class="fa-solid fa-globe"`;
                                        html += `style="color: #16adcb;"></i></button></li></ul>`;
                                        html += `</div><div class="col-lg-1 schedule_menu_btn">`;
                                        html +=
                                            `<i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>`;
                                        html += `</div></div></li>`;
                                    }
                                    $('#schedule_list_1').html(html);
                                    $('#schedule_list_2').html(html.replace(
                                        'email_settings_schedule_id',
                                        'global_settings_schedule_id'));
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            },
                        });
                    });
                    $('.search_schedule').on('input', function(e) {
                        var schedule_input = $(this);
                        var search = $(this).val();
                        if (search === '') {
                            search = 'null';
                        }
                        $.ajax({
                            url: "{{ route('filterSchedule', ':search') }}".replace(':search', search),
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    schedules = response.schedules;
                                    var schedule_list = $(schedule_input).parent().next(
                                        '.schedule_list');
                                    html = ``;
                                    for (var i = 0; i < schedules.length; i++) {
                                        schedule = schedules[i];
                                        html +=
                                            `<li><div class="row schedule_list_item"><div class="col-lg-1 schedule_item">`;
                                        html +=
                                            `<input type="radio" name="email_settings_schedule_id" class="schedule_id"`;
                                        if (schedule['user_id'] == 0 || i == 0) {
                                            html += `checked `;
                                        }
                                        html += `value=` + schedule['id'] + `></div>`;
                                        html += `<div class="col-lg-1 schedule_avatar">S</div>`;
                                        html +=
                                            `<div class="col-lg-3 schedule_name"><i class="fa-solid fa-circle-check"`;
                                        html += `style="color: #4bcea6;"></i>`;
                                        html += `<span>` + schedule['schedule_name'] + `</span></div>`;
                                        html += `<div class="col-lg-6 schedule_days">`;
                                        var schedule_days = schedule['Days'];
                                        html += `<ul class="schedule_day_list">`;
                                        for (var j = 0; j < schedule_days.length; j++) {
                                            html += `<li `;
                                            html += `class="schedule_day `;
                                            day = schedule_days[j];
                                            if (day['is_active'] == '1') {
                                                html += `selected_day`;
                                            }
                                            html += `">`;
                                            html += day['schedule_day'].toUpperCase() + `</li>`;
                                        }
                                        html += `<li class="schedule_time"><button href="javascript:;"`;
                                        html += `type="button" class="btn" data-bs-toggle="modal"`;
                                        html +=
                                            `data-bs-target="#time_modal"><i class="fa-solid fa-globe"`;
                                        html += `style="color: #16adcb;"></i></button></li></ul>`;
                                        html += `</div><div class="col-lg-1 schedule_menu_btn">`;
                                        html +=
                                            `<i class="fa-solid fa-ellipsis-vertical" style="color: #ffffff;"></i>`;
                                        html += `</div></div></li>`;
                                    }
                                    if (schedule_list.attr('id') == 'schedule_list_2') {
                                        html = html.replace('email_settings_schedule_id',
                                            'global_settings_schedule_id');
                                        $('#schedule_list_2').html(html);
                                    } else {
                                        $('#schedule_list_1').html(html);
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                if (xhr.status == 404) {
                                    var schedule_list = $(schedule_input).parent().next(
                                        '.schedule_list');
                                    html = ``;
                                    html +=
                                        `<li><div class="text-center text-danger">Not Found!</div></li>`;
                                    if (schedule_list.attr('id') == 'schedule_list_2') {
                                        $('#schedule_list_2').html(html);
                                    } else {
                                        $('#schedule_list_1').html(html);
                                    }
                                }
                            },
                        });
                    });
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('campaign/editCampaignSequence')))
            <script>
                $(document).ready(function() {
                    /* Making every setting to unchangable */
                    // $('.linkedin_setting_switch').prop('disabled', true);
                    // var campaign_id = {!! $campaign_id !!};
                    // var settings = {!! $settings !!};
                    // var inputElement = null;
                    // var outputElement = null;
                    // var condition = '';
                    // var element_data_array = {};
                    // var elements_array = {};
                    // var choosedElement = null;

                    // $.ajax({
                    //     url: "{{ route('getElements', ':campaign_id') }}".replace(':campaign_id', campaign_id),
                    //     method: 'GET',
                    //     success: function(response) {
                    //         if (response.success) {
                    //             elements_array = response.elements_array;
                    //             path = response.path;
                    //             if (elements_array) {
                    //                 var maxDropPadHeight = 0;
                    //                 html = ``;
                    //                 html +=
                    //                     `<div class="step-1 element_item" id="step-1"><div class="list-icon">`;
                    //                 html +=
                    //                     `<i class="fa-solid fa-certificate"></i></div><div class="item_details">`;
                    //                 html +=
                    //                     `<p class="item_name">Lead Source (Step 1)</p><p class="item_desc">`;
                    //                 html +=
                    //                     `<i class="fa-solid fa-clock"></i>Wait for: <span class="item_days">0</span>`;
                    //                 html += ` days <span class="item_hours">0</span> hours</p></div>`;
                    //                 html +=
                    //                     `<div class="element_change_output attach-elements-out condition_true"></div></div>`;
                    //                 $('.task-list').append(html);
                    //                 for (var i = 0; i < elements_array.length; i++) {
                    //                     var element = elements_array[i]['original_element'];
                    //                     var original_properties = elements_array[i]['properties'];
                    //                     var days = 0;
                    //                     var hours = 0;
                    //                     for (var j = 0; j < original_properties.length; j++) {
                    //                         if (original_properties[j]['original_properties'][
                    //                                 'property_name'
                    //                             ] == "Hours") {
                    //                             hours = original_properties[j]['value'];
                    //                         } else if (original_properties[j]['original_properties'][
                    //                                 'property_name'
                    //                             ] == "Days") {
                    //                             days = original_properties[j]['value'];
                    //                         }
                    //                     }
                    //                     html = ``;
                    //                     if (element['is_conditional'] == '1') {
                    //                         html +=
                    //                             `<div class="element_item drop-pad-element placedElement" id="`;
                    //                         html += elements_array[i]['id'] + `"`;
                    //                         html += `data-filter-name="` + element['element_name'];
                    //                         html += `" style="position: absolute;">`;
                    //                         html +=
                    //                             `<div class="element_change_input conditional-elements conditional-elements-in"></div>`;
                    //                         html +=
                    //                             `<div class="cancel-icon"><i class="fa-solid fa-x"></i></div>`;
                    //                         html += `<div class="list-icon">` + element['element_icon'] +
                    //                             `</div>`;
                    //                         html += `<div class="item_details"><p class="item_name">`;
                    //                         html += element['element_name'] + `</p>`;
                    //                         html +=
                    //                             `<p class="item_desc"><i class="fa-solid fa-clock"></i>Check after: `;
                    //                         html +=
                    //                             `<span class="item_days">` + days + `</span> days `;
                    //                         html += `<span class="item_hours">` + hours + `</span> hours`;
                    //                         html += `</p></div>`;
                    //                         html +=
                    //                             `<div class="menu-icon"><i class="fa-solid fa-bars"></i></div>`;
                    //                         html +=
                    //                             `<div class="conditional-elements conditional-elements-out">`;
                    //                         html +=
                    //                             `<div class="element_change_output condition_true"><i class="fa-solid fa-check"></i>`;
                    //                         html += `</div><div class="element_change_output condition_false">`;
                    //                         html += `<i class="fa-solid fa-xmark"></i></div></div></div>`;
                    //                     } else {
                    //                         html +=
                    //                             `<div class="element_item drop-pad-element placedElement" id="`;
                    //                         html += elements_array[i]['id'] + `"`;
                    //                         html += `data-filter-name="` + element['element_name'];
                    //                         html += `" style="position: absolute;">`;
                    //                         html +=
                    //                             `<div class="element_change_input attach-elements attach-elements-in"></div>`;
                    //                         html +=
                    //                             `<div class="cancel-icon"><i class="fa-solid fa-x"></i></div>`;
                    //                         html += `<div class="list-icon">` + element['element_icon'] +
                    //                             `</div>`;
                    //                         html += `<div class="item_details"><p class="item_name">`;
                    //                         html += element['element_name'] + `</p>`;
                    //                         html +=
                    //                             `<p class="item_desc"><i class="fa-solid fa-clock"></i>Wait for: `;
                    //                         html +=
                    //                             `<span class="item_days">` + days + `</span> days `;
                    //                         html += `<span class="item_hours">` + hours +
                    //                             `</span> hours</p></div>`;
                    //                         html +=
                    //                             `<div class="menu-icon"><i class="fa-solid fa-bars"></i></div>`;
                    //                         html +=
                    //                             `<div class="element_change_output attach-elements attach-elements-out condition_true">`;
                    //                         html += `</div></div>`;
                    //                     }
                    //                     $('.task-list').append(html);
                    //                     var left = elements_array[i]['position_x'];
                    //                     var top = elements_array[i]['position_y'];
                    //                     var clone = $('#' + elements_array[i]['id']);
                    //                     $(clone).css({
                    //                         'left': left,
                    //                         'top': parseInt(top)
                    //                     });
                    //                     var newDropPadHeight = parseInt($(clone).css('top')) + parseInt($(clone)
                    //                         .css('height')) + 30;
                    //                     if (maxDropPadHeight < newDropPadHeight) {
                    //                         maxDropPadHeight = newDropPadHeight;
                    //                         $('.drop-pad').css('height', maxDropPadHeight + "px");
                    //                     }
                    //                 }
                    //                 $('#step-1').find('.condition_true').on("click", attachOutputElement)
                    //                     .trigger("click");
                    //                 var first_element = path[0]['current_element_id'];
                    //                 $('#' + first_element).find('.element_change_input').on("click",
                    //                     attachInputElement).trigger("click");
                    //                 for (var i = 0; i < path.length; i++) {
                    //                     current_element = path[i]['current_element_id'];
                    //                     if (path[i]['next_false_element_id'] != '') {
                    //                         $('#' + current_element).find('.condition_false').on("click",
                    //                             attachOutputElement).trigger("click");
                    //                         $('#' + path[i]['next_false_element_id']).find(
                    //                                 '.element_change_input').on("click", attachInputElement)
                    //                             .trigger("click");
                    //                     }
                    //                     if (path[i]['next_true_element_id'] != '') {
                    //                         $('#' + current_element).find('.condition_true').on("click",
                    //                             attachOutputElement).trigger("click");
                    //                         $('#' + path[i]['next_true_element_id']).find(
                    //                                 '.element_change_input').on("click", attachInputElement)
                    //                             .trigger("click");
                    //                     }
                    //                     $('#' + current_element).css({
                    //                         left: '-=20px',
                    //                     });
                    //                     if ($('#' + current_element).width() > 365) {
                    //                         $('#' + current_element).css({
                    //                             left: '-=10px',
                    //                         });
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //     },
                    //     error: function(xhr, status, error) {
                    //         console.error(error);
                    //     }
                    // });

                    // function attachOutputElement(e) {
                    //     if (inputElement == null && outputElement == null) {
                    //         var attachDiv = $(this);
                    //         attachDiv.addClass('selected');
                    //         if (attachDiv.hasClass('condition_true')) {
                    //             condition = 'True';
                    //         } else if (attachDiv.hasClass('condition_false')) {
                    //             condition = 'False';
                    //         } else {
                    //             condition = '';
                    //         }
                    //         outputElement = attachDiv.closest('.element_item');
                    //     }
                    // }

                    // function attachInputElement(e) {
                    //     if (outputElement != null && outputElement.attr('id') != $(this).parent().attr('id')) {
                    //         var attachDiv = $(this);
                    //         attachDiv.addClass('selected');
                    //         inputElement = attachDiv.closest('.element_item');
                    //         if (outputElement && inputElement) {
                    //             var outputElementId = outputElement.attr('id');
                    //             var inputElementId = inputElement.attr('id');
                    //             if (condition == 'True') {
                    //                 var attachOutputElement = $(outputElement).find(
                    //                     '.element_change_output.condition_true');
                    //             } else if (condition == 'False') {
                    //                 var attachOutputElement = $(outputElement).find(
                    //                     '.element_change_output.condition_false');
                    //             } else {
                    //                 $('#' + inputElementId).css({
                    //                     'border': '1px solid red',
                    //                 });
                    //             }
                    //             $('.drop-pad').append('<div class="line" id="' + outputElement.attr('id') + '-to-' +
                    //                 inputElement.attr('id') +
                    //                 '"><div class="path-cancel-icon"><i class="fa-solid fa-xmark"></i></div></div>');
                    //             var attachInputElement = $(inputElement).find('.element_change_input');
                    //             if (attachInputElement && attachOutputElement) {
                    //                 var inputPosition = attachInputElement.offset();
                    //                 var outputPosition = attachOutputElement.offset();
                    //                 var x1 = inputPosition.left;
                    //                 var y1 = inputPosition.top;
                    //                 var x2 = outputPosition.left;
                    //                 var y2 = outputPosition.top;

                    //                 var distance = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
                    //                 var angle = Math.atan2(y2 - y1, x2 - x1) * (180 / Math.PI);

                    //                 var lineId = outputElement.attr('id') + '-to-' + inputElement.attr('id');
                    //                 var line = $('#' + lineId);
                    //                 line.css({
                    //                     'width': distance + 'px',
                    //                     'transform': 'rotate(' + (angle) + 'deg)',
                    //                     'top': (y1 - 326) + 'px',
                    //                     'left': (x1 - 203) + 'px'
                    //                 });
                    //                 inputElement = null;
                    //                 outputElement = null;
                    //             }
                    //         }
                    //     }
                    // }
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('campaign')))
            <script>
                $(document).ready(function() {
                    /* Action toggle on Campaign list */
                    $(document).on('change', '.switch', function(e) {
                        var campaign_id = $(this).attr('id').replace('switch', '');
                        $.ajax({
                            url: "{{ route('changeCampaignStatus', ':campaign_id') }}".replace(
                                ':campaign_id', campaign_id),
                            type: 'GET',
                            success: function(response) {
                                if (response.success && response.active == 1) {
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
                                        "timeOut": "3000",
                                        "extendedTimeOut": "1000",
                                        "showEasing": "swing",
                                        "hideEasing": "linear",
                                        "showMethod": "fadeIn",
                                        "hideMethod": "fadeOut"
                                    }
                                    toastr.success('Campaign successfully Activated');
                                } else {
                                    toastr.info('Campaign successfully Deactivated');
                                }
                                if ($('#filterSelect').val() != 'archive') {
                                    $('#table_row_' + campaign_id).remove();
                                }
                                if ($('.campaign_table_row').length <= 0) {
                                    html = '';
                                    html += '<tr><td colspan="8">';
                                    html +=
                                        '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                                    html += '</td></tr>';
                                    $('#campaign_table_body').html(html);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            },
                        });
                    });
                    $(document).on('click', '.delete_campaign', function(e) {
                        if (confirm('Are you sure to delete this campaign?')) {
                            var campaign_id = $(this).attr('id').replace('delete', '');
                            $.ajax({
                                url: "{{ route('deleteCampaign', ':id') }}".replace(':id', campaign_id),
                                type: 'GET',
                                success: function(response) {
                                    if (response.success) {
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
                                            "timeOut": "3000",
                                            "extendedTimeOut": "1000",
                                            "showEasing": "swing",
                                            "hideEasing": "linear",
                                            "showMethod": "fadeIn",
                                            "hideMethod": "fadeOut"
                                        }
                                        toastr.success('Campaign successfully Deleted');
                                    } else {
                                        toastr.error('Campaign cannot be Deleted');
                                    }
                                    $('#table_row_' + campaign_id).remove();
                                    if ($('.campaign_table_row').length == 0) {
                                        html = '';
                                        html += '<tr><td colspan="8">';
                                        html +=
                                            '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                                        html += '</td></tr>';
                                        $('#campaign_table_body').html(html);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                },
                            });
                        }
                    });
                    $(document).on('click', '.archive_campaign', function(e) {
                        if (confirm('Are you sure to archive this campaign?')) {
                            var campaign_id = $(this).attr('id').replace('archive', '');
                            $.ajax({
                                url: "{{ route('archiveCampaign', ':id') }}".replace(':id', campaign_id),
                                type: 'GET',
                                success: function(response) {
                                    if (response.success && response.archive == 1) {
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
                                            "timeOut": "3000",
                                            "extendedTimeOut": "1000",
                                            "showEasing": "swing",
                                            "hideEasing": "linear",
                                            "showMethod": "fadeIn",
                                            "hideMethod": "fadeOut"
                                        }
                                        toastr.success('Campaign successfully Archived');
                                    } else {
                                        toastr.info('Campaign successfully Archived');
                                    }
                                    $('#table_row_' + campaign_id).remove();
                                    if ($('.campaign_table_row').length == 0) {
                                        html = '';
                                        html += '<tr><td colspan="8">';
                                        html +=
                                            '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                                        html += '</td></tr>';
                                        $('#campaign_table_body').html(html);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                },
                            });
                        }
                    });
                    $(document).on('click', '#filterToggle', function(e) {
                        e.preventDefault();
                        $('#filterSelect').toggle();
                    });

                    $('#filterSelect').on('change', filter_search);
                    $('#search_campaign').on('input', filter_search);

                    function filter_search(e) {
                        e.preventDefault();
                        var filter = $('#filterSelect').val();
                        var search = $('#search_campaign').val();
                        if (search === '') {
                            search = 'null';
                        }
                        $.ajax({
                            url: "{{ route('filterCampaign', [':filter', ':search']) }}".replace(':filter', filter)
                                .replace(':search', search),
                            type: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    var campaigns = response.campaigns;
                                    var html = ``;
                                    if (campaigns.length > 0) {
                                        for (let i = 0; i < campaigns.length; i++) {
                                            let campaign = campaigns[i];
                                            html += `<tr id="` + 'table_row_' + campaign['id'] +
                                                `" class="campaign_table_row"><td><div class="switch_box">`;
                                            if (campaign['is_active'] == 1) {
                                                html +=
                                                    `<input type="checkbox" class="switch" id="switch` +
                                                    campaign['id'] + `" checked>`;
                                            } else {
                                                html +=
                                                    `<input type="checkbox" class="switch" id="switch` +
                                                    campaign['id'] + `">`;
                                            }
                                            html += `<label for="switch` + campaign['id'] +
                                                `">Toggle</label></div></td>`;
                                            html += `<td>` + campaign['campaign_name'] + `</td>`;
                                            html += `<td>44</td>`;
                                            html += `<td>105</td>`;
                                            html +=
                                                `<td class="stats"><ul class="status_list d-flex align-items-center list-unstyled p-0 m-0">`;
                                            html +=
                                                `<li><span><img src="/assets/img/eye.svg" alt="">10</span></li>`;
                                            html +=
                                                `<li><span><img src="/assets/img/request.svg" alt="">42</span></li>`;
                                            html +=
                                                `<li><span><img src="/assets/img/mailmsg.svg" alt="">10</span></li>`;
                                            html +=
                                                `<li><span><img src="/assets/img/mailopen.svg" alt="">16</span></li></ul></td>`;
                                            html += `<td><div class="per up">34%</div></td>`;
                                            html += `<td><div class="per down">23%</div></td>`;
                                            html +=
                                                `<td><a href="javascript:;" type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>`;
                                            html += `<ul class="setting_list" style="display: none;">`;
                                            html += `<li><a href="/campaign/campaignDetails/` + campaign['id'] +
                                                `">Check campaign details</a></li>`;
                                            html += `<li><a href="/campaign/editcampaign/` + campaign['id'] +
                                                `">Edit campaign</a></li>`;
                                            // html += '<li><a href="#">Duplicate campaign steps</a></li>';
                                            // html += '<li><a href="javascript:;" data-bs-toggle="modal" data-bs-target="#add_new_leads_modal">Add new leads</a></li>';
                                            // html += '<li><a href="#">Export data</a></li>';
                                            html += `<li><a class="archive_campaign" id="archive` + campaign[
                                                'id'] + `">Archive campaign</a></li>`;
                                            html += `<li><a class="delete_campaign" id="delete` + campaign[
                                                'id'] + `">Delete campaign</a></li>`;
                                            html += `</ul></td></tr>`;
                                        }
                                    }
                                    $('#campaign_table_body').html(html);
                                    $('.setting_btn').on('click', setting_list);
                                } else {
                                    var html = ``;
                                    html += '<tr><td colspan="8">';
                                    html +=
                                        '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                                    html += '</td></tr>';
                                    $('#campaign_table_body').html(html);
                                }
                                if ($('#filterSelect').val() == 'archive') {
                                    $('.archive_campaign').html('Remove From Archive');
                                } else {
                                    $('.archive_campaign').html('Archive campaign');
                                }
                            },
                            error: function(xhr, status, error) {
                                var html = ``;
                                html += '<tr><td colspan="8">';
                                html +=
                                    '<div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">Not Found!</div>';
                                html += '</td></tr>';
                                $('#campaign_table_body').html(html);
                            },
                        });
                    }
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('accdashboard')))
            <script>
                $(document).ready(function() {
                    $('.switch').prop('disabled', true);
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('leads')))
            <script>
                $('.lead_tab').on('click', function(e) {
                    e.preventDefault();
                    $('.lead_tab').removeClass('active');
                    $(this).addClass('active');
                    var id = $(this).data('bs-target');
                    $('.lead_pane').removeClass('active');
                    $('#' + id).addClass('active');
                });
            </script>
        @elseif (Str::contains(request()->url(), URL('setting')))
            <script>
                $('.setting_tab').on('click', function(e) {
                    e.preventDefault();
                    $('.setting_tab').removeClass('active');
                    $(this).addClass('active');
                    var id = $(this).data('bs-target');
                    $('.setting_pane').removeClass('active');
                    $('#' + id).addClass('active');
                });
                $('.linkedin_setting').on('click', function(e) {
                    e.preventDefault();
                    $('.linkedin_setting').removeClass('active');
                    $(this).addClass('active');
                    var id = $(this).data('bs-target');
                    $('.linkedin_pane').removeClass('active');
                    $('#' + id).addClass('active');
                });
            </script>
        @endif
    </footer>
</body>

</html>
