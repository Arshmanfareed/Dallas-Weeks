@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard blacklist  compaign_sec lead_sec">
    <div class="container_fluid">
        <div class="row">
            <div class="col-lg-1">
                @include('partials/dashboard_sidebar_menu')
            </div>
            <div class="col-lg-11 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <h3>Integrations</h3>
                            <div class="filt_opt d-flex">
                                <div class="filt_opt">
                                </div>
                                <div class="add_btn ">
                                    <a href="javascript:;" class="" type="button" data-bs-toggle="modal" data-bs-target="#webhook_modal" >
                                    	<i class="fa-solid fa-plus"></i>
                                    </a>Create new webhook
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="filter_head_row d-flex">
                            
                        </div>
                        <div class="filtr_desc">
                            <div class="d-flex">
                                <strong>Integrations</strong>
                                <div class="filter">
                                    <form action="/search" method="get" class="search-form">
                                        <input type="text" name="q" placeholder="Search account here...">
                                        <button type="submit">
                                        <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                    <div class="filt_opt">
                                        <select name="num" id="num">
                                            <option value="01">10</option>
                                            <option value="02">20</option>
                                            <option value="03">30</option>
                                            <option value="04">40</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                    	<div class="integration_sec">
                    		<div class="border_box">
                            <div class="scroll_div">
                            <table class="data_table w-100">
                                                <thead>
                                                    <tr>
                                                        <th width="12%">All campaigns</th>
                                                        <th width="25%">Invited to connect</th>
                                                        <th width="5%">Connections</th>
                                                        <th width="10%" class="">Contact replies</th>
                                                        <th width="13%" >Message received</th>
                                                        <th width="10%">Chat label</th>
                                                        <th width="20%">Webhook url</th>
                                                        <th width="5%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < 10; $i++)
                                                    <tr>
                                                        <td>
                                                           Sed ut perspiciatis
                                                        </td>
                                                        <td class="title_cont">Sales manager @  perspiciatis unde</td>
                                                        <td class="">
                                                        </td>
                                                        <td class="" >
                                                        	<i class="fa-solid fa-check"></i>
                                                        </td>
                                                        <td></td>
                                                        <td >
                                                            
                                                        </td>
                                                        <td><div class="web_hook">https//edut-perspiciatis/unde/</div></td>
                                                        <!-- <td><div class="per">23%</div> -->
                                                    </td>
                                                    <td class="setting">
                                                        <a href="javascript:;" type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>
                                                        <ul class="setting_list">
                                                            <li><a href="#">Edit</a></li>
                                                            <li><a href="#">Delete</a></li>
                                                        </ul>
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
<!-- Modal Filter leads -->
<div class="modal fade create_sequence_modal filter_modal webhook_modal" id="webhook_modal" tabindex="-1" aria-labelledby="filter_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sequance_modal">Setup Webhook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <p>Callback URL</p>
                                <input type="text" placeholder="Enter callback URL here">
                                <h6 class="text-center">What type of updates to send</h6>
                                <ul class="webhook_check d-flex flex-wrap list-unstyled">
                                    <li><span>
                                        <input type="checkbox" id="status_Discovered" name="lead_status" value="Discovered">
                                        <label for="status_Discovered">
                                        When a contact is invited to connect</label>
                                    </span><span class="test">test</span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Connection_pending" name="lead_status" value="Connection_pending">
                                        <label for="status_Connection_pending">When a contact accepts connection
                                        </label>
                                    </span><span class="test">test</span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Connected_not_replied" name="lead_status" value="Connected, not replied">
                                        <label for="status_Connected_not_replied">When a contact replies
                                        </label>
                                    </span><span class="test">test</span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Replied" name="lead_status" value="Replied">
                                        <label for="status_Replied"> When a message is received from contact
                                        </label>
                                    </span><span class="test">test</span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Replied_not_connected" name="lead_status" value="Replied, not connected">
                                        <label for="status_Replied_not_connected">
                                            Send all connection requests
                                        </label>
                                    </span><span class="test">test</span></li>
                                     <li><span>
                                        <input type="checkbox" id="status_Replied_not_connected" name="lead_status" value="Replied, not connected">
                                        <label for="status_Replied_not_connected">
                                            When a lead completed the campaign
                                        </label>
                                    </span><span class="test">test</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="radio_btns_div">
                            <p>Choose which active campaigns to connect</p>
                            <div class="radio_btns d-flex align-items-center">
                                <div class="radio">                                	
                                		<input type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                                		<label for="exampleRadios">
                                		Global Webhook
                                		</label>
                                </div>
                                <div class="radio">                              
                                		<input type="radio" name="exampleRadios" id="exampleRadios1" value="option1">
                                		  	<label for="exampleRadios1">
                                		Let me select individual campaigns
                                	</label>
                                </div>
                            </div>
                        </div>
                        <div class="btn_row d-flex justify-content-center">
                        	 <a href="javascript:;" class="crt_btn back"><i class="fa-solid fa-arrow-left"></i>Back</a>
                        	  <a href="javascript:;" class="crt_btn submit">Submit<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                       
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection