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
                            <h3>Leads</h3>
                            <div class="filt_opt d-flex">
                                <div class="filt_opt">
                                    <select name="compaign" id="compaign">
                                        <option value="01">All Campaigns</option>
                                        <option value="02">All Campaigns</option>
                                        <option value="03">3All Campaigns0</option>
                                        <option value="04">All Campaigns</option>
                                    </select>
                                </div>
                                <div class="add_btn ">
                                    <a href="javascript:;   " class="" type="button" data-bs-toggle="modal" data-bs-target="#export_modal" ><img class="img-fluid" src="/assets/img/importexport.svg" alt=""></a>Export from campaigns
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
                                <strong>Leads</strong>
                                <div class="filter">
                                    <a href="javascript:;" type="button"  data-bs-toggle="modal" data-bs-target="#filter_modal"><i class="fa-solid fa-filter"></i></a>
                                    <form action="/search" method="get" class="search-form">
                                        <input type="text" name="q" placeholder="Search Campaig here...">
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
                        <div class="comp_tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#Leads" role="tab">Leads</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Steps" role="tab">Steps</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#Stats" role="tab">Stats</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#integration" role="tab">Campaign integration</a>
                                </li>
                                </ul><!-- Tab panes -->
                                <div class="tab-content">
                                    <!-- Leads Content -->
                                    <div class="tab-pane active" id="Leads" role="tabpanel">
                                        <div class="border_box">
                                            <div class="scroll_div">
                                            <table class="data_table w-100">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">Status</th>
                                                        <th width="20%">Contact</th>
                                                        <th width="25%">Title/Company</th>
                                                        <th width="15%" class="">Send Connections</th>
                                                        <th width="10%" >Current step</th>
                                                        <th width="10%">Next step</th>
                                                        <th width="10%">Executed time</th>
                                                        <th width="5%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < 10; $i++)
                                                    <tr>
                                                        <td>
                                                            <!-- <div class="switch_box"><input type="checkbox" class="switch" id="switch{{$i}}"><label for="switch{{$i}}">Toggle</label></div> -->
                                                        </td>
                                                        <td class="title_cont">Denys Anderson</td>
                                                        <td class="title_comp">
                                                            Sales manager @  perspiciatis unde
                                                            <span>Sed ut perspiciatis unde omnis iste natus</span>
                                                        </td>
                                                        <td class="" ><div class="per">Connected</div></td>
                                                        <td>23</td>
                                                        <td >
                                                            Message
                                                        </td>
                                                        <td><div class="">2 days ago</div></td>
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
                                <!-- Step Content -->
                                <div class="tab-pane" id="Steps" role="tabpanel">
                                    <div class="lead_step_cont">
                                        <div class="border_box">
                                            <form id="" class="lead_step_form">
                                                <div class="row">
                                                    <div class="comp_name">
                                                        <label for="campaign-name">Campaign Name:</label>
                                                        <input type="text" id="campaign-name" name="campaign-name" placeholder="Campaign name ex. Los angeles lead" required="">
                                                        <!-- <span>Characters 24 / 250</span> -->
                                                    </div>
                                                    <div class="comp_url">
                                                        <label for="linkedin-url">LinkedIn URL:</label>
                                                        <input type="url" id="linkedin-url" name="linkedin-url" placeholder="LinkedIn search URL" required="">
                                                    </div>
                                                    <button>Save changes</button>
                                                </div>
                                            </form>
                                            <div class="date">
                                                <i class="fa-solid fa-calendar-days"></i>Created at: 2023-10-05 16:48
                                            </div>
                                        </div>
                                        <div class="email_setting">
                                            <div class="border_box">
                                                <h3>Email settings</h3>
                                                <div id="accordion">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <a class="card-link" data-toggle="collapse" href="#collapseOne">
                                                                Email accounts to use for this campaign
                                                            </a>
                                                        </div>
                                                        <div id="collapseOne" class="collapse " data-parent="#accordion">
                                                            <div class="card-body">
                                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <a class="collapsed card-link" data-toggle="collapse" href="#collapseTwo">
                                                                Schedule email
                                                            </a>
                                                        </div>
                                                        <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <a class="collapsed card-link" data-toggle="collapse" href="#collapseThree">
                                                                Email tracking preference
                                                            </a>
                                                        </div>
                                                        <div id="collapseThree" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h3>Email settings</h3>
                                                <div class="email_settings">
                                                    <ul class="list-unstyled p-0">
                                                        <li class="d-flex justify-content-between align-items-center ">
                                                            <span>Discover premium LinkedIn account only</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch" id="switch0"><label for="switch0">Toggle</label></div>
                                                        </li>
                                                        <li class="d-flex justify-content-between align-items-center">
                                                            <span>Discover leads with open profile status only</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch" id="switch1"><label for="switch1">Toggle</label></div>
                                                        </li>
                                                        <li class="d-flex justify-content-between align-items-center">
                                                            <span> Collect contact information</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch" id="switch2"><label for="switch2">Toggle</label></div>
                                                        </li>
                                                        <li class="d-flex justify-content-between align-items-center">
                                                            <span>Remove leads with pending connection requests</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch" id="switch3"><label for="switch3">Toggle</label></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <hr>
                                                <h3>Global settings</h3>
                                                <div id="accordion">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <a class="card-link" data-toggle="collapse" href="#target_opt">
                                                                Targeting options
                                                            </a>
                                                        </div>
                                                        <div id="target_opt" class="collapse " data-parent="#accordion">
                                                            <div class="card-body">
                                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <a class="collapsed card-link" data-toggle="collapse" href="#schedule_cmp">
                                                                Schedule campaign
                                                            </a>
                                                        </div>
                                                        <div id="schedule_cmp" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Stats Content -->
                                <div class="tab-pane" id="Stats" role="tabpanel">
                                    <div class="chart_box">
                                        <div class="border_box">
                                            <div class="chart_filter d-flex justify-content-between">
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
                                    <div class="chart_data_list">
                                        <div class="border_box">
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
                                                    @for($i=0;$i<=7;$i++)
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
                                    <div class="chart_last_box">
                                        <div class="border_box">
                                            <img src="/assets/img/temp.png" alt="">
                                            <p class="text-center">you need to choose some campaign to check out statistics by step.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Campaign Integration Content -->
                                <div class="tab-pane" id="integration" role="tabpanel">
                                    <div class="leads_int">
                                        <div class="border_box">
                                            <div class="int_box">
                                                <h4>Use a Webhook to add leads to your campaign</h4>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                <ul class="integration_list list-unstyled p-0">
                                                    <li><strong>Method:</strong><span>Post</span></li>
                                                    <li><strong>URL to use:</strong><span class="url">https://lorem.ipsum/dolor/sit-amet</span></li>
                                                    <li><strong>Headers:</strong><span>Lorem ipsum</span></li>
                                                    <li><strong>Payload to use:</strong>
                                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="border_box">
                                            <div class="int_box">
                                                <h4>Use a Webhook to add leads to your campaign</h4>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                <ul class="integration_list list-unstyled p-0">
                                                    <li><strong>Method:</strong><span>Post</span></li>
                                                    <li><strong>URL to use:</strong><span class="url">https://lorem.ipsum/dolor/sit-amet</span></li>
                                                    <li><strong>Headers:</strong><span>Lorem ipsum</span></li>
                                                    <li><strong>Payload to use:</strong>
                                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <hr>
                                            <div class="int_box">
                                                <h4>Use a Webhook to add leads to your campaign</h4>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                <ul class="integration_list list-unstyled p-0">
                                                    <li><strong>Method:</strong><span>Post</span></li>
                                                    <li><strong>URL to use:</strong><span class="url">https://lorem.ipsum/dolor/sit-amet</span></li>
                                                    <li><strong>Headers:</strong><span>Lorem ipsum</span></li>
                                                    <li><strong>Payload to use:</strong>
                                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
                                                    </li>
                                                </ul>
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
<!-- Modal Filter leads -->
<div class="modal fade create_sequence_modal filter_modal" id="filter_modal" tabindex="-1" aria-labelledby="filter_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sequance_modal">Filter leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-12">
                            <div class="border_box">
                                <p>Filter leads by status</p>
                                <ul class="lead_status d-flex flex-wrap list-unstyled">
                                    <li><span>
                                        <input type="checkbox" id="status_Discovered" name="lead_status" value="Discovered">
                                        <label for="status_Discovered">
                                        Discovered</label>
                                    </span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Connection_pending" name="lead_status" value="Connection_pending">
                                        <label for="status_Connection_pending">Connection pending
                                        </label>
                                    </span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Connected_not_replied" name="lead_status" value="Connected, not replied">
                                        <label for="status_Connected_not_replied">Connected, not replied
                                        </label>
                                    </span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Replied" name="lead_status" value="Replied">
                                        <label for="status_Replied"> Replied
                                        </label>
                                    </span></li>
                                    <li><span>
                                        <input type="checkbox" id="status_Replied_not_connected" name="lead_status" value="Replied, not connected">
                                        <label for="status_Replied_not_connected">
                                            Replied, not connected
                                        </label>
                                    </span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="border_box">
                            <p>Filter leads by their email status</p>
                            <div class="checkboxes d-flex align-items-center">
                                <div class="check">
                                    <input type="checkbox" id="verified" name="verified" checked />
                                    <label for="verified">Show leads with verified emails</label>
                                </div>
                                <div class="check">
                                    <input type="checkbox" id="unverified" name="unverified" checked />
                                    <label for="unverified">Show leads with unverified emails</label>
                                </div>
                            </div>
                        </div>
                        
                        <a href="javascript:;" class="crt_btn ">Apply filters<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Export leads -->
<div class="modal fade create_sequence_modal export_modal" id="export_modal" tabindex="-1" aria-labelledby="export_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sequance_modal">Export data from all campaigns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <p class="w-75">Once the export is complete, we will send you the exported data is a CSV file. Please insert the email you would like us to use.</p>
                                <input type="email" placeholder="admin@gmail.com">
                            </div>
                        </div>
                        
                        <a href="javascript:;" class="crt_btn ">Submit<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection