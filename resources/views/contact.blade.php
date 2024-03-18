@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard message_sec contact_sec">
	<div class="container_fluid">
		<div class="row">
			<div class="col-lg-1">
				@include('partials/dashboard_sidebar_menu')
			</div>
			<div class="col-lg-11 col-sm-12">
				<div class="row">
					<div class="col-lg-12 lead_sec justify-content-between d-flex">
						<h3>Contacts</h3>
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
                                    <a href="javascript:;" class="" type="button" data-bs-toggle="modal" data-bs-target="#contact_modal"><i class="fa-solid fa-plus"></i></a>New contact
                                </div>
                            </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="messages_box">
							<div class="border_box">
								<div class="filter d-flex">
									<form action="/search" method="get" class="search-form w-100">
										<input type="text" name="q" placeholder="Search Accounts here...">
										<button type="submit">
										<i class="fa fa-search"></i>
										</button>
									</form>
									
								</div>
								<ul class="list-unstyled p-0 m-0">
									@for($i=0;$i<8;$i++)
									<li class="d-flex justify-content-start">
										<img src="/assets/img/acc_img{{$i+1}}.png" alt="">
										<div class="d-block">
											<strong>John doe</strong>
										</div>
										
									</li>
									@endfor
								</ul>
							</div>
						</div>						
					</div>
					<div class="col-lg-8 border_box">
							<div class="conversation_box row ">
								<div class="call_box col-lg-8">
									<div class="caller_info">
										<img src="/assets/img/caller_img.png" alt="">
									<span class="call_timer_counter d-block">01:22</span>
									</div>
									<div class="call_opt">
										<ul class="d-flex align-items-center justify-content-center">
											<li><a href="javascript:;"><i class="fa-solid fa-volume-high"></i></a></li>
											<li class="call_end"><a href="javascript:;"><img src="/assets/img/call_end.png" alt=""></a></li>
											<li><a href="javascript:;"><i class="fa-solid fa-microphone"></i></a></li>
										</ul>
									</div>
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
		<!-- Modal Add contact -->
		<div class="modal fade create_sequence_modal add_contact_modal export_modal " id="contact_modal" tabindex="-1" aria-labelledby="contact_modal" aria-hidden="true">
<!-- <div class="modal fade add_contact_modal export_modal" id="contact_modal" tabindex="-1" aria-labelledby="contact_modal" aria-modal="true" role="dialog" style="display: block;"> -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sequance_modal">Add new contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <p class="w-75">Enter email to connect with the new chat</p>
                                <div class="cont_div"><i class="fa-solid fa-id-card"></i><input type="email" placeholder="Enter email"></div>
                            </div>
                        </div>
                        
                        <a href="javascript:;" class="crt_btn">Add new contact</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>