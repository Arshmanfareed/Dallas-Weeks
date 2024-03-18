<!DOCTYPE html>
<html lang="en">
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
 @extends('partials/head')

<body>
@if(request()->is('login','register'))
@else
    <header>    
        <nav class="navbar navbar-expand-lg navbar-light bg-dark justify-content-between">
            <a class="navbar-brand" href="/">Networked</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/blacklist">Blacklist</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/team">Team</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/invoice">Invoice</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/roles-and-permission-setting">Settings</a>
                    </li>
                    <?php $user = auth()->user(); ?>
                    @if ($user) 
                    <a href="{{ route('logout-user') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        LOGOUT
                    </a>
                    @endif

                    <form id="logout-form" action="{{ route('logout-user') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </div>
            <div class="right_nav">
                <ul class="d-flex list-unstyled">
                    <li><a href="/setting"><i class="fa-solid fa-gear"></i></a></li>
                    <li><a href="#"><i class="fa-solid fa-arrow-up-from-bracket"></i></a></li>
                    <li class="darkmode"><a href="javascript:;" id="darkModeToggle"><i class="fa-solid fa-sun"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>
@endif


@yield('content')
<footer>

</footer>
</body>
</html>