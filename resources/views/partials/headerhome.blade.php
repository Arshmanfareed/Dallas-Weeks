<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Networked</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style_home.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    </head>
    <body>
        <!-- Header Start -->
        <header id="masthead">
            <div class="container-fluid">
                <div class="topbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="phonelocation">
                                <ul>
                                    <li><i class="fa-solid fa-location-dot"></i>1010 New York, NY 10018 US.</li>
                                    <li><i class="fa-solid fa-phone"></i><a href="tel:">212 386 5575</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="language">
                                <select name="language" class="language">
                                    <option value="english">English</option>
                                    <option value="spanish">Spanish</option>
                                </select>
                                <select name="location" class="location">
                                    <option value="english">New York Office</option>
                                    <option value="spanish">Dallas Office</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active" aria-current="page" href="{{ URL('/') }}">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ URL('/about') }}">About Us</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;">Services</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ URL('/pricing') }}">Pricing</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ URL('/faq') }}">FAQ's</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="col-md-2">
                        <div class="logomain">
                            <a class="navbar-brand" href="{{ URL('/') }}"><img src="assets/images/logo.png" alt=""></a>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="socialicons">
                            <ul>
                                <li><a href="javascript:;" target="_blank"><i class="fa-brands fa-facebook"></i></a></li>
                                <li><a href="javascript:;" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                                <li><a href="javascript:;" target="_blank"><i class="fa-brands fa-linkedin"></i></a></li>
                                <li><a href="javascript:;" target="_blank"><i class="fa-brands fa-twitter"></i></a></li>
                            </ul>
                            <div class="btn btn-blue">
                                <a href="/login">Log in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->
        @yield('content')
        @extends('partials/footerhome')
