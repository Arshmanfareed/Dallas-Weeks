@extends('partials/master')
@section('content')
<body>
	<section class="login">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-5 col-sm-12 form_col d-flex flex-column justify-content-between">
					<div class="cont">
						<h2>Welcome to Networked</h2>
						<h6>Register your account</h6>
					</div>

					@if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					
					@if(session('success'))
						<div class="alert alert-success">
							{{ session('success') }}
						</div>
					@endif

					<form action="{{ route('register-user') }}" class="login_form" method="POST">
					@csrf
						<div>
							<label for="username">Your name</label>
							<input type="text" id="username" name="name" placeholder="Enter your name" required>
						</div>
						<div>
							<label for="username_email">Email address</label>
							<input type="email" id="username_email" name="email" placeholder="Enter your email" required>
						</div>
						<div>
							<label for="username_phone">Phone number (Optional)</label>
							<input type="tel" id="username_phone" name="username_phone" required>
						</div>
						<div class="pass">
							<div class="row">
								<div class="col-6">
									<label for="password">Password</label>
									<input type="password" id="password" name="password" value="" placeholder="Enter your password" required>
								</div>
								<div class="col-6">
									<label for="confirm_password">Confirm password</label>
									<input type="password" id="confirm_password" name="password_confirmation" value="" placeholder="Enter your password" required>
								</div>
							</div>
							<!-- <span class="forg_pass"><a href="#">Forgot password?</a></span> -->
						</div>
						<div class="checkbox">
							<input type="checkbox" id="termsCheckbox" name="termsCheckbox">
							<label for="termsCheckbox">I agree with the <a href="terms_and_conditions.html" target="_blank">Terms and Conditions</a></label>
						</div>
						<div>
							<button type="submit" class="theme_btn">
							Register
							</button>
						</div>
					</form>
					<div class="regist">
						Already have an account?  <a href="{{ URL('/login') }}">Login</a>
					</div>
				</div>
				<div class="col-lg-7 col-sm-12">
					<div class="login_img">
						<a href="{{ URL('/') }}"><img src="assets/img/blank_img.png" alt=""></a>
					</div>
				</div>
			</div>
		</div>
	</section>
</body>
@endsection