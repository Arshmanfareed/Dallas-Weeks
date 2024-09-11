@extends('partials/master')
@section('content')
    <style>
        #payment-form input.form-control {
            color: white !important;
        }

        .alert.alert-success.text-center {
            background: #e3c935;
            color: #000;
            border: none;
            border-radius: 30px;
            padding: 20px;
            width: 100%;
            margin: 20px auto;
            margin-bottom: 50px;
        }

        .alert.alert-success.text-center p {
            margin: 0;
            color: #000;
            font-weight: 600;
            text-transform: uppercase;
        }

        .alert.alert-success.text-center a.close {
            width: 50px;
            height: 50px;
            position: absolute;
            top: 7px;
            right: 1%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 100%;
            background: #0b3b6a;
            opacity: 1;
            color: #fff;
            font-weight: 400;
        }

        #update_seat .accordion .accordion-item .accordion-header button {
            background: #1C1E22 !important;
            width: 100%;
            border-radius: 30px !important;
            color: #fff;
            /* border: 1px solid #fff; */
            padding: 20px 15px;
            font-size: 18px;
        }


        #update_seat div#accordionExample {
            padding: 20px;
            padding-bottom: 50px;
        }

        #update_seat .accordion .accordion-item .accordion-header .accordion-button::after {
            color: #e3c935 !important;
            filter: invert(1);
        }

        #update_seat .accordion .accordion-item .accordion-header .accordion-button i {
            color: #e3c935 !important;
            font-size: 20px;
        }

        #update_seat .accordion .accordion-item .accordion-header {
            border-radius: 30px !important;
            overflow: hidden;
            border: 1px solid #fff;
        }

        #update_seat .collapse.show {
            padding-top: 40px;
            padding-bottom: 40px;
        }

        #update_seat button#delete_seat_11 {
            margin-top: 30px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset('assets/js/login.js') }}"></script>

    <body>
        <section class="login">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-5 col-sm-12 form_col d-flex flex-column justify-content-between">
                        <div class="cont">
                            <h2>Welcome back to Networked</h2>
                            <h6>Log In to your account</h6>
                        </div>
                        @if ($errors->has('mismatch_token'))
                            <div class="alert alert-danger">
                                {{ $errors->first('mismatch_token') }}
                                <form action="{{ route('resend_an_email') }}" method="post">
                                    @csrf
                                    <input value="{{ session('email') }}" type="hidden" name="user_email">
                                    <button type="submit" class="theme_btn" style="padding: 7px 20px; margin: 0;">Resend an
                                        email</button>
                                </form>
                            </div>
                        @endif
                        @if ($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                {{ session('success') }}
                            </div>
                        @endif
                        <form action="" class="login_form" method="POST">
                            <div>
                                <label for="email">Email address</label>
                                <input value="{{ session('email') }}" type="email" id="email" name="email"
                                    placeholder="Enter your email" required>
                            </div>
                            <div class="pass">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" placeholder="Enter your password"
                                    required>
                                <span id="passwordError" style="color: red;"></span>
                                <span id="successMessage" style="color: green;"></span>

                                <span class="forg_pass">
                                    <a href="#" class="" data-toggle="modal" data-target="#basicModal">Forgot
                                        password?</a>
                                    <a href="{{ URL('auth/linkedin/redirect') }}">Login Via LinkedIn</a>
                                    <!-- <a href="#">Forgot password?</a> -->
                                </span>
                            </div>
                            <div class="btn_div"></div>
                        </form>
                        <div class="regist">
                            Don't have an account? <a href="{{ route('register') }}">Register</a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-sm-12">
                        <div class="login_img">
                            <img src="{{ asset('assets/img/login-picture.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- basic modal -->
        <div class="modal fade fotget_password_popup" id="basicModal" tabindex="-1" role="dialog"
            aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h3>Forgot password</h3>

                        <p>Enter the email address you sighed up with to receive a secure link.</p>
                        <form action="" class="forget_pass">
                            <input type="email" class="email" placeholder="Enter your email">
                            <button class="theme_btn">Send link</button>
                        </form>
                    </div>
                    <!-- <div class="modal-footer">
                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                                                                                                                                                                                                                                                            <button type="button" class="btn btn-primary">Save changes</button>
                                                                                                                                                                                                                                                                                                                          </div> -->
                </div>
            </div>
        </div>
        <script>
            var checkCredentialsRoute = "{{ route('checkCredentials') }}";
        </script>
    </body>
@endsection
