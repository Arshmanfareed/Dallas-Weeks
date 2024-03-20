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
                $('.element').on('click', function(e) {

                });
                var chooseElement;

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
                                    left: task_list.x + 250,
                                    top: task_list.y + 250
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
                            chooseElement = null;
                        }
                    });
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
