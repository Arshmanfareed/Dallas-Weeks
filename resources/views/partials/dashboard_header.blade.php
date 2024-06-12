<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap-grid.min.css" integrity="sha512-ZuRTqfQ3jNAKvJskDAU/hxbX1w25g41bANOVd1Co6GahIe2XjM6uVZ9dh0Nt3KFCOA061amfF2VeL60aJXdwwQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css" integrity="sha512-W/zrbCncQnky/EzL+/AYwTtosvrM+YG/V6piQLSe2HuKS6cmbw89kjYkp3tWFn1dkWV7L1ruvJyKbLz73Vlgfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
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
                        <a type="button" class="user_toggle" id=""><i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="user_toggle_list" style="display: none">
                            <li><a href="{{ route('logout-user') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                                <form id="logout-form" action="{{ route('logout-user') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>

                        </ul>
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
        <script src="{{ asset('assets/js/createCampaignFromScratch.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign/createcampaign')))
        <script src="{{ asset('assets/js/createCampaign.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign/campaigninfo')))
        <script src="{{ asset('assets/js/campaignInfo.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign/campaignDetails')))
        <script src="{{ asset('assets/js/campaignDetails.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign/editcampaign')))
        <script src="{{ asset('assets/js/editcampaign.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign/editCampaignInfo')))
        <script src="{{ asset('assets/js/editCampaignInfo.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign/editCampaignSequence')))
        <script src="{{ asset('assets/js/editCampaignSequence.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('campaign')))
        <script src="{{ asset('assets/js/campaign.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('leads')))
        <script src="{{ asset('assets/js/leads.js') }}"></script>
        @elseif (Str::contains(request()->url(), URL('setting')))
        <script src="{{ asset('assets/js/setting.js') }}"></script>
        @endif
    </footer>
    <script>
        $(".user_toggle").on("click", function(e) {
            $(".user_toggle_list").toggle();
        });
    </script>
</body>

</html>