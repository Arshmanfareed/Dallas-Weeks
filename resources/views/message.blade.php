@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard message_sec">
	<div class="container_fluid">
		<div class="row">
			<div class="col-lg-1">
				@include('partials/dashboard_sidebar_menu')
			</div>
			<div class="col-lg-11 col-sm-12">
				<div class="row">
					<div class="col-lg-12 lead_sec justify-content-between d-flex">
						<h3>Messages</h3>
						<div class="filt_opt d-flex">
							<div class="filt_opt">
								<select name="Channels" id="Channels">
									<option value="01">All Channels</option>
									<option value="02">All Channels</option>
									<option value="03">3All Channels</option>
									<option value="04">All Channels</option>
								</select>
							</div>
							<div class="add_btn">
								<a href="javascript:;" class="" type="button" data-bs-toggle="modal" data-bs-target="#export_modal"><img class="img-fluid" src="/assets/img/sync.svg" alt=""></a>Sync
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="messages_box">
							<div class="border_box">
								<div class="filter d-flex">
									<form action="/search" method="get" class="search-form">
										<input type="text" name="q" placeholder="Search Accounts here...">
										<button type="submit">
										<i class="fa fa-search"></i>
										</button>
									</form>
									<a href="javascript:;" class="message_filter"><i class="fa-solid fa-filter"></i></a>
									<div class="msg_filter_cont">
										<h6>Filter Leads by Label</h6>
										<span>Lorem ipsum dolor, sit amet consectetur adipisicing elit.</span>
										<hr>
										<h6>Filter by chat type</h6>
										<form action="" class="filter_form">
											<div class="" data-toggle="">
												<div class="checkbox">
													<input type="checkbox" id="LinkedIn" name="LinkedIn" value="LinkedIn">
													<label for="LinkedIn" >LinkedIn Messages
													</label>
												</div>
												<div class="checkbox">
													<input type="checkbox" id="Messages" name="Messages" value="Messages">
													<label for="Messages" >Email Messages
													</label>
												</div>
												<div class="fltr_btn_form">
													<button type="button" class="list-group-item">Apply filter<i class="fa-solid fa-arrow-right"></i></button>
												</div>
											</div>
										</form>
									</div>
								</div>
								<ul class="list-unstyled p-0 m-0">
									@for($i=0;$i<8;$i++)
									<li class="d-flex">
										<img src="/assets/img/acc_img{{$i+1}}.png" alt="">
										<div class="d-block">
											<strong>John doe</strong>
											<span>Lorem Ipsum is simply...</span>
										</div>
										<div class="date">Sep 4</div>
										<div class="linkedin">
											<a href="javascript:;"><i class="fa-brands fa-linkedin"></i></a>
										</div>
									</li>
									@endfor
								</ul>
							</div>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="conversation_box row border_box">
							<div class="conversation col-lg-8">
								<ul class=" msg_setting d-flex justify-content-end p-0 list-unstyled">
									<li><a href="javascript:;"><i class="fa-solid fa-user"></i></a></li>
									<li><a href="javascript:;"><i class="fa-solid fa-tag"></i></a></li>
									<li><a href="javascript:;"><img src="/assets/img/settings.svg" alt=""></a></li>
								</ul>
								<div class="mesasges">
									<ul class="list-unstyled">
										@for($i=0;$i<5;$i++)
										<li>
											<img src="/assets/img/acc_img{{$i+1}}.png" alt="">
											<span>Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi aut corporis quod, totam iure numquam ipsam blanditiis natus a nulla delectus modi eos obcaecati, suscipit maiores aliquid nisi. Nobis, nulla.</span>
										</li>
										@endfor
									</ul>
								</div>
								<form action="" class="send_form">
									<input type="text">
									<input type="button" class="send_btn" value="send">
								</form>
							</div>
							<div class="conversation_info col-lg-4">
								<div class="info">
									<img src="/assets/img/account_img.png" alt="">
									<h6>John doe</h6>
									<span class="user_name">Lorem Ipsum</span>
									<span class="user_email">johndoe@gmail.com</span>
									<div class="note">
										<p>Note:</p>
										<span>Sed ut perspiciatis unde omnis iste natus error sit.</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	@endsection