@extends('partials/master')
@section('content')
<section class="dashboard">
	<div class="container-fluid">
		<div class="row">
			@include('partials/dashboard_sidebar')
			<div class="col-lg-8">
				<div class="dashboard_cont">
					<div class="row_filter d-flex align-items-center justify-content-between">
						<div class="account d-flex align-items-center">
							<img src="assets/img/account_img.png" alt=""><span>John dow</span>
						</div>
						<div class="form_add d-flex">
							<form action="/search" method="get" class="search-form">
								<input type="text" name="q" placeholder="Search...">
								<button type="submit">
								<i class="fa fa-search"></i>
								</button>
							</form>
							<div class="add_btn">
								<a href="#"  class="" data-toggle="modal" data-target="#addaccount"><i class="fa-solid fa-plus"></i></a>Add account
							</div>
						</div>
					</div>
					<hr>
					<div class="">
						<div class="data_row dashboard_data">
                    <div class="data_head">
                        <table class="data_table w-100">
                            <thead>
                                <tr>
                                    <th width="85%">Name</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Status</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td><div class="d-flex align-items-center"><img src="/assets/img/acc_img{{$i+1}}.png" alt=""><strong>John doe</strong></div></td>
                                    <td><a href="javascript:;" class="black_list_activate down ">Connected</a></td>
                                     <td><a href="javascript:;" class="black_list_activate active">Active</a></td>
                                    <td>
                                        <a href="javascript:;" type="button" class="setting setting_btn" id="" data-bs-toggle="modal" data-bs-target="#active_subs"><i class="fa-solid fa-gear"></i></a>
                                       <!--  <ul class="setting_list">
                                            <li><a href="javascript:;">Edit</a></li>
                                            <li><a href="javascript:;">Delete</a></li>
                                        </ul> -->
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
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
<!-- Modal Add Email Accounts -->
<div class="modal fade activate_box create_sequence_modal add_email" id="active_subs" tabindex="-1" aria-labelledby="active_subs" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="sequance_modal">Your subscription is active</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
			</div>
			<div class="modal-body">
				<form action="">
					<div class="row">
						<div class="col-12">
							<p>Select your email provider</p>
							<div class="row">
								<div class="col-lg-4">
									<div class="border_box">
										<div class="email_box_img">
											<img src="/assets/img/gmail.png" alt="">
											<span>Gmail</span>
										</div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="border_box">
										<div class="email_box_img">
											<img src="/assets/img/outlook.png" alt="">
											<span>Outlook</span>
										</div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="border_box">
										<div class="email_box_img">
											<img src="/assets/img/web-browser.png" alt="">
											<span>Custom SMTP server</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>