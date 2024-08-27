@extends('partials/master')
@section('content')

    <body>
        <section class="login">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-5 col-sm-12 form_col d-flex flex-column justify-content-between">
                        <div class="cont">
                            <h2>Welcome back to Networked</h2>
                            <h6>Log In to your account</h6>
                        </div>
                        @if ($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif
                        <form action="" class="login_form" method="POST">
                            <div>
                                <label for="email">Email address</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
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
                            <div>
                                <a href="{{ route('dashobardz') }}" style="display: none;"
                                    class="theme_btn login_btn">Login</a>
                                <!-- <button style="display: none;" class="theme_btn login_btn">Login</button> -->
                            </div>
                        </form>
                        <div class="regist">
                            Don't have an account? <a href="{{ URL('/register') }}">Register</a>
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
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            var credentialAjax = null;
            $(document).ready(function() {
                $('#password').on('input', credential_check);
                $('#email').on('input', credential_check);

                function credential_check() {
                    if (credentialAjax) {
                        credentialAjax.abort();
                        credentialAjax = null;
                    }
                    var email = $('#email').val();
                    var password = $('#password').val();
                    if (password.trim() === '') {
                        $('#passwordError').html('Password is required.');
                        return;
                    } else {
                        $('#passwordError').text('');
                    }
                    credentialAjax = $.ajax({
                        type: 'POST',
                        url: '{{ route('checkCredentials') }}',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'email': email,
                            'password': password
                        },
                        success: function(response, textStatus, xhr) {
                            if (response.success) {
                                // Success response
                                $('#passwordError').text('');
                                $('#successMessage').html(response.message);
                                $('.login_btn').show();
                            } else {
                                // Error response with status code 200 (this might be an unexpected case)
                                $('#passwordError').html(response.error);
                                $('#successMessage').text('');
                                $('.login_btn').hide();
                            }
                        },
                        error: function(xhr, textStatus, error) {
                            console.error(error);
                        },
                    });
                }
            });
        </script>

    </body>
@endsection
