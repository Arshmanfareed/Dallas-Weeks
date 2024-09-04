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
    <style>
        .alert.alert-danger.alert-dismissible {
            background: #870000;
            border: none;
            border-radius: 30px;
            padding: 20px;
            text-align: center;
            color: #fff;
        }

        .alert.alert-danger.alert-dismissible .close {
            height: 50px;
            width: 50px;
            opacity: 1;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            border-radius: 100%;
            position: absolute;
            top: 5px;
            right: 10px;
        }
    </style>

    <body>
        <section class="login">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-5 col-sm-12 form_col d-flex flex-column justify-content-between">
                        <div class="cont">
                            <h2>Welcome to Networked</h2>
                            <h6>Register your account</h6>
                        </div>

                        @if ($errors->first())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errors->first() }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('register-user') }}" class="login_form" method="POST">
                            @csrf
                            <div>
                                <label for="username">Your name</label>
                                <input type="text" id="username" name="name" placeholder="Enter your name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div>
                                <label for="username_email">Email address</label>
                                <input type="email" id="username_email" name="email"
                                    placeholder="Enter your email address" value="{{ old('email') }}" required>
                            </div>
                            <div>
                                <label for="company">Company/Team Name</label>
                                <input type="text" id="company" name="company"
                                    placeholder="Enter your company/team name" value="{{ old('company') }}" required>
                            </div>
                            <div>
                                <label for="username_phone">Phone number (Optional)</label>
                                <input type="tel" id="username_phone" name="username_phone"
                                    placeholder="Enter phone number" value="{{ old('username_phone') }}">
                            </div>
                            <div class="pass">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="password">Password</label>
                                        <input type="password" id="password" name="password"
                                            placeholder="Enter your password" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="confirm_password">Confirm password</label>
                                        <input type="password" id="confirm_password" name="password_confirmation"
                                            placeholder="Confirm password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" id="termsCheckbox" name="termsCheckbox"
                                    {{ old('termsCheckbox') ? 'checked' : '' }} required>
                                <label for="termsCheckbox">I agree with the <a href="terms_and_conditions.html"
                                        target="_blank">Terms and Conditions</a></label>
                            </div>
                            <div>
                                <button type="submit" class="theme_btn">
                                    Register
                                </button>
                            </div>
                        </form>

                        <div class="regist">
                            Already have an account? <a href="{{ route('login') }}">Login</a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-sm-12">
                        <div class="login_img">
                            <img src="{{ asset('assets/img/register-picture.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
@endsection
