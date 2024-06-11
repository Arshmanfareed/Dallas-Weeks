<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    @if (request()->is('accdashboard', 'report', 'leads'))
        <script src="{{ asset('assets/js/chart_query.js') }}"></script>
    @endif
    @if (request()->is('dashboard'))
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endif
</head>

<body>
    @if (request()->is('login', 'register'))
    @else
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-dark justify-content-between">
                <a class="navbar-brand" href="/">Networked</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() === URL('blacklist') ? 'active' : '' }}"
                                href="/blacklist">Blacklist</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() === URL('team') ? 'active' : '' }}"
                                href="/team">Team</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() === URL('invoice') ? 'active' : '' }}"
                                href="/invoice">Invoice</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() === URL('roles-and-permission-setting') ? 'active' : '' }}"
                                href="/roles-and-permission-setting">Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="right_nav">
                    <ul class="d-flex list-unstyled">
                        <li><a href="/setting"><i class="fa-solid fa-gear"></i></a></li>
                        <li><a href="#"><i class="fa-solid fa-arrow-up-from-bracket"></i></a></li>
                        <li class="acc d-flex align-item-center">
                            <img src="{{ asset('/assets/img/acc.png') }}" alt="">
                            @php
                                $user = auth()->user();
                            @endphp
                            <span>{{ $user->name }}</span>
                            <a type="button" class="user_toggle" id=""><i class="fa-solid fa-chevron-down"></i></a>
                            <ul class="user_toggle_list" style="display: none">
                                <li><a href="{{ route('logout-user') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                                        <form id="logout-form" action="{{ route('logout-user') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                </li>
                                
                            </ul>
                        </li>
                        <li class="darkmode"><a href="javascript:;" id="darkModeToggle"><i
                                    class="fa-solid fa-sun"></i></a></li>
                    </ul>
                </div>
            </nav>
        </header>
    @endif
    @yield('content')
    <footer>
    </footer>
    <script>
        $(".user_toggle").on("click", function (e) {
            $(".user_toggle_list").toggle();
        });
    </script>
</body>

</html>
