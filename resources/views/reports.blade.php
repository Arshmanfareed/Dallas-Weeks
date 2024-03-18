@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard blacklist  report_sec ">
	<div class="container_fluid">
		<div class="row">
			<div class="col-lg-1">
				@include('partials/dashboard_sidebar_menu')
			</div>
			<div class="col-lg-11 col-sm-12">
				<div class="row">
					<div class="col-lg-12">
						<div class="report_head_tabs d-flex justify-content-between align-items-center" >
							<h3>Reports</h3>
							<ul class="d-flex list-unstyled justify-content-end p-0 m-0 ">
								<li><a href="javascript:;" class="active">Main graph</a></li>
								<li><a href="#table">Table view</a></li>
								<li><a href="#sequence">Sequence steps</a></li>
							</ul>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="chart_box">
							<div class="border_box">
								<div class="chart_filter d-flex justify-content-between">
									<h4>Main graph</h4>
									<div class="chart_cont">
										<div class="select d-flex">
											<select name="timezone">
												<option value="GMT+01:00">Central European Time (CET) - GMT+01:00</option>
												<option value="GMT+01:01">Central European Time (CET) - GMT+01:01</option>
												<option value="GMT+01:02">Central European Time (CET) - GMT+01:02</option>
												<!-- Add more timezone options here if needed -->
											</select>
											<select name="post-sales-tips">
												<option value="01.09. Post-Sales Tips">01.09. Post-Sales Tips</option>
												<option value="01.10. Post-Sales Tips">01.10. Post-Sales Tips</option><option value="01.11. Post-Sales Tips">01.11. Post-Sales Tips</option>
												<!-- Add more post-sales tips options here if needed -->
											</select>
										</div>
										<div class="btn_box d-flex">
											<a href="#" class="theme_btn">Export PDF</a>
											<a href="#" class="theme_btn">Export CSV</a>
										</div>
									</div>
								</div>
								<div class="chart_canvas_report">
									<div id="chartContainer" style="height: 388px; width: 100%;"></div>
								</div>
								<!-- <img src="/assets/img/chart.png" alt=""> -->
								<ul class="chart_status d-flex justify-content-between list-unstyled p-0">
									<li><span></span>Views</li>
									<li><span></span>Follows</li>
									<li><span></span>Connections sent</li>
									<li><span></span>Invite via email sent</li>
									<li><span></span>Messages sent</li>
									<li><span></span>InMails sent</li>
									<li><span></span>Emails sent</li>
									<li><span></span>Connections accepted</li>
									<li><span></span>Replies Received</li>
								</ul>
								<ul class="chart_status d-flex list-unstyled p-0">
									<li><span></span>Response rate</li>
									<li><span></span>Acceptance rate</li>
									<li><span></span>Email opened</li>
									<li><span></span>Email clicked</li>
									<li><span></span>Email open rate</li>
									<li><span></span>Emails click rate</li>
									<li><span></span>Email verified</li>
									<li><span></span>Bounce rate</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-lg-12" id="table">
						<div class="chart_data_list">
							<div class="border_box">
								<h4>Table view</h4>
								<div class="scroll_div">
								<table class="data_table w-100" id="chat_table">
									<thead>
										<tr>
											<th width="15%">Date</th>
											<th width="20%">Views</th>
											<th width="30%">Invite via email sent</th>
											<th width="20%" class="">Follows</th>
											<th width="15%">Connections sent</th>
										</tr>
									</thead>
									<tbody>
										@for($i=0;$i<=5;$i++)
										<tr>
											<td>2023-10-01</td>
											<td>25</td>
											<td>14</td>
											<td>22</td>
											<td>19</td>
										</tr>
										@endfor
									</tbody>
									<tfoot>
									<tr>
										<td>Total</td>
										<td>406</td>
										<td>156</td>
										<td>63</td>
										<td>268</td>
									</tr>
									</tfoot>
								</table>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12" id="sequence">
						<div class="sequence_sec">
							<div class="border_box">
								<div class="row">
									<div class="col-lg-8">
										<h4>Sequence steps</h4>
										<img src="/assets/img/sequence.png" alt="">
									</div>
									<div class="col-lg-4">
										<div class="sequence_side">
											<h6>
											Properties
											</h6>
											<div class="add_btn">
												<a href="javascript:;" class=""><i class="fa-solid fa-share-nodes"></i></a>Add team member
											</div>
											<table>
												@for($i=0;$i<5;$i++)
												<tr>
													<td class="cont">Contrary to popular belief,</td>
													<td>21{{$i}}</td>
													<td>9{{$i}}</td>
												</tr>
												@endfor
											</table>
											<div class="connect">
												<h6>Connect message</h6>
												<form action="">
													<input type="email" placeholder="Enter your connect message">
												</form>
											</div>
											<div class="note">
												<div class="d-flex justify-content-between">
													<h6>Note</h6>
													<i class="fa-solid fa-triangle-exclamation"></i>
												</div>
												<div class="note_cont">
													Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.
												</div>
											</div>
											<div class="video_watch">
												<div class="play_btn">
													<a href="#"><i class="fa-solid fa-play"></i>Watch the explainer video</a>
												</div>
											</div>
										</div>
									</div>
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