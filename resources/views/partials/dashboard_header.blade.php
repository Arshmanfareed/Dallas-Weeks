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
                var chooseElement;
                var elementInput;
                var elementOutput;

                function move() {
                    $('.element').on('mousedown', function(e) {
                        e.preventDefault();
                        var clone = $(this).clone().css({
                            position: "absolute"
                        });
                        $('body').append(clone);
                        chooseElement = clone;
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
                            chooseElement.on('mousedown', startDragging);
                            $('.cancel-icon').on('click', removeElement);
                            $('.attach-elements-out').on('click', attachElementInput);
                            $('.attach-elements-in').on('click', attachElementOutput)
                            chooseElement = null;
                        }
                    });
                }

                function attachElementInput(e) {
                    var attachDiv = $(this);
                    attachDiv.css({
                        "background-color": "white"
                    });
                    elementInput = attachDiv[0];
                }

                function attachElementOutput(e) {
                    var attachDiv = $(this);
                    attachDiv.css({
                        "background-color": "white"
                    });
                    elementOutput = attachDiv[0];

                    if (elementInput && elementOutput) {
                        var svgNS = "http://www.w3.org/2000/svg";
                        var svg = document.createElementNS(svgNS, "svg");
                        var line = document.createElementNS(svgNS, "line");
                        var rect1 = elementInput.getBoundingClientRect();
                        var rect2 = elementOutput.getBoundingClientRect();
                        var x1 = rect1.left + rect1.width / 2;
                        var y1 = rect1.top + rect1.height / 2;
                        var x2 = rect2.left + rect2.width / 2;
                        var y2 = rect2.top + rect2.height / 2;
                        console.log("x1:", x1, "y1:", y1, "x2:", x2, "y2:", y2);
                        line.setAttribute("x1", x1);
                        line.setAttribute("y1", y1);
                        line.setAttribute("x2", x2);
                        line.setAttribute("y2", y2);
                        line.setAttribute("stroke", "pink");
                        line.setAttribute("stroke-width", "25");
                        svg.appendChild(line);
                        document.body.appendChild(svg);
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
            });
        </script>
    </footer>
</body>

</html>
