@extends('partials/head')
<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">


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
            jQuery('.setting_btn').each(function() {
                jQuery(this).on('click', function() {
                    jQuery(this).siblings('.setting_list').toggle();
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.attach-elements-out').on('click', attachElementOutput);

                var chooseElement;
                var elementInput;
                var elementOutput;
                var count = 0;
                var final_array = [];

                function move() {
                    $('.element').on('mousedown', function(e) {
                        e.preventDefault();
                        var clone = $(this).clone().css({
                            position: "absolute"
                        });
                        $('body').append(clone);
                        chooseElement = clone;
                        id = chooseElement.attr('id') + '_' + ++count;
                        chooseElement.attr('id', id);
                        chooseElement.attr('class', 'drop-pad-element');
                        chooseElement.removeClass('element');
                        var task_list = $('.task-list').get(0).getBoundingClientRect();
                        $(document).on('mousemove', function(e) {
                            var x = e.pageX;
                            var y = e.pageY;
                            if (x > task_list.x && y > task_list.y) {
                                chooseElement.css({
                                    left: x - 350,
                                    top: y - 350
                                });
                            } else {
                                chooseElement.css({
                                    left: task_list.x,
                                    top: task_list.y
                                });
                            }
                        });
                    });

                    $(document).on('mouseup', function() {
                        if (chooseElement) {
                            $(document).off('mousemove');
                            $('.task-list').append(chooseElement);
                            $('.cancel-icon').on('click', removeElement);
                            $('.drop-pad-element').on('click', elementProperties);
                            $('.attach-elements-out').on('click', attachElementOutput);
                            $('.attach-elements-in').on('click', attachElementInput)
                            chooseElement.on('mousedown', startDragging);
                            chooseElement = null;
                        }
                    });
                }

                function elementProperties(e) {
                    $('#properties').empty();
                    var item = $(this);
                    var item_name = item.find('.item_name').text();
                    var list_icon = item.find('.list-icon').html();
                    var name_html = '<div class="element_properties">' + list_icon + '<p>' + item_name + '</p></div>';
                    $('#properties').append(name_html);
                    $('#element-list').removeClass('active');
                    $('#properties').addClass('active');
                    $('#element-list-btn').removeClass('active');
                    $('#properties-btn').addClass('active');
                }

                function attachElementOutput(e) {
                    if (!elementInput) {
                        var attachDiv = $(this);
                        attachDiv.css({
                            "background-color": "white"
                        });
                        elementOutput = attachDiv.parent();
                    }
                }

                function attachElementInput(e) {
                    if (elementOutput && elementOutput.attr('id') != $(this).parent().attr('id')) {
                        var attachDiv = $(this);
                        attachDiv.css({
                            "background-color": "white"
                        });
                        elementInput = attachDiv.parent();
                        if (elementInput && elementOutput) {
                            if (!final_array.includes(elementOutput.attr('id'))) {
                                final_array.push(elementOutput.attr('id'));
                                final_array.push(elementInput.attr('id'));
                            } else if (final_array.includes(elementOutput.attr('id'))) {
                                final_array.push(elementInput.attr('id'));
                            }
                        }
                        $('.task-list').append('<div class="line" id="' + elementOutput.attr('id') + '-' + elementInput
                            .attr('id') +
                            '"></div>');
                        var attachElementInput = elementInput.find('.attach-elements-in');
                        var attachElementOutput = elementOutput.find('.attach-elements-out');
                        var rect1 = attachElementOutput.get(0).getBoundingClientRect();
                        var rect2 = attachElementInput.get(0).getBoundingClientRect();
                        if (rect1 && rect2) {
                            var x1 = rect1.left + (rect1.width / 2);
                            var y1 = rect1.top + (rect1.height / 2);
                            var x2 = rect2.left + (rect2.width / 2);
                            var y2 = rect2.top + (rect2.height / 2);
                            console.log(rect1);
                            console.log(rect2);
                            var lineId = elementOutput.attr('id') + '-' + elementInput.attr('id');
                            create_line(x1, y1, x2, y2, lineId);
                        }

                        elementInput = null;
                        elementOutput = null;
                    }
                }

                function removeElement(e) {
                    $(this).parent().remove()
                }

                function startDragging(e) {
                    e.preventDefault();
                    var currentElement = $(this);
                    var initialX = e.clientX - currentElement.offset().left;
                    var initialY = e.clientY - currentElement.offset().top;

                    $(document).on('mousemove', function(e) {
                        var x = e.clientX - initialX;
                        var y = e.clientY - initialY;
                        currentElement.css({
                            left: x - 350,
                            top: y - 350
                        });
                    });

                    $(document).on('mouseup', function() {
                        $(document).off('mousemove');
                    });
                }
                move();
                $('.element-btn').on('click', function() {
                    var targetTab = $(this).data('tab');
                    $('.element-content').removeClass('active');
                    $('#' + targetTab).addClass('active');
                    $('.element-btn').removeClass('active');
                    $(this).addClass('active');
                });
                $('#save-changes').on('click', function() {
                    if (final_array[0] != 'step-1') {
                        alert('Select Step 1 First');
                        location.reload();
                    } else {
                        console.log(final_array);
                    }
                });

                function create_line(x1, y1, x2, y2, lineId) {
                    var line = $('#' + lineId);
                    line.css({
                        'position': 'absolute',
                        'background': 'none',
                        'border-radius': 0,
                        'border-right': '3px solid black',
                        'left': x1 + 'px',
                        'top': y1 + 'px',
                        'width': 1 + 'px',
                        'height': (y2 - y1) + 'px',
                    });
                }
            });
        </script>
    </footer>
</body>

</html>
