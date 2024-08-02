@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard blacklist  campaign_sec lead_sec">
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
                                    @if (!empty($campaigns))
                                    <select name="campaign" id="campaign">
                                        <option value="all">All Campaigns</option>
                                        @foreach ($campaigns as $campaign)
                                        <option value="{{ $campaign->id }}">{{ $campaign->campaign_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                                <div class="add_btn ">
                                    <a href="javascript:;" class="" type="button" data-bs-toggle="modal" data-bs-target="#export_modal"><img class="img-fluid" src="{{ asset('assets/img/importexport.svg') }}" alt=""></a>Export
                                    from campaigns
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
                                    <a href="javascript:;" type="button" data-bs-toggle="modal" data-bs-target="#filter_modal"><i class="fa-solid fa-filter"></i></a>
                                    <form action="/search" method="get" class="search-form">
                                        @csrf
                                        <input type="text" name="q" placeholder="Search Lead here..." id="search_lead">
                                        <button type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                    <div class="filt_opt">
                                        <select name="num" id="num">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="40">40</option>
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
                                    <a class="nav-link lead_tab active" data-toggle="tab" href="javascript:;" role="tab" data-bs-target="Leads">Leads</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lead_tab" data-toggle="tab" href="javascript:;" role="tab" data-bs-target="Steps">Steps</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lead_tab" data-toggle="tab" href="javascript:;" role="tab" data-bs-target="Stats">Stats</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lead_tab" data-toggle="tab" href="javascript:;" role="tab" data-bs-target="integration">Campaign
                                        integration</a>
                                </li>
                            </ul><!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Leads Content -->
                                <div class="tab-pane lead_pane active" id="Leads" role="tabpanel">
                                    <div class="border_box">
                                        <div class="scroll_div leads_list">
                                            <table class="data_table w-100">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">Status</th>
                                                        <th width="20%">Contact</th>
                                                        <th width="25%">Title/Company</th>
                                                        <th width="15%">Send Connections</th>
                                                        <th width="10%">Current step</th>
                                                        <th width="10%">Next step</th>
                                                        <th width="10%">Executed time</th>
                                                        <th width="5%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (!empty($leads))
                                                    @foreach ($leads as $lead)
                                                    <tr>
                                                        <td>
                                                            <div class="switch_box"><input type="checkbox" class="switch" id="{{ 'swicth' . $lead['id'] }}" {{ $lead['is_active'] == 1 ? 'checked' : '' }}>
                                                                <label for="{{ 'swicth' . $lead['id'] }}">Toggle</label>
                                                            </div>
                                                        </td>
                                                        <td class="title_cont">{{ $lead['contact'] }}</td>
                                                        <td class="title_comp">
                                                            {{ $lead['title_company'] }}
                                                        </td>
                                                        <td class="">
                                                            @if ($lead['send_connections'] == 'discovered')
                                                            <div class="per discovered">Discovered</div>
                                                            @elseif ($lead['send_connections'] == 'connected_not_replied')
                                                            <div class="per connected_not_replied">
                                                                Connected, not replied</div>
                                                            @elseif ($lead['send_connections'] == 'replied_not_connected')
                                                            <div class="per replied_not_connected">Replied,
                                                                not connected</div>
                                                            @elseif ($lead['send_connections'] == 'connection_pending')
                                                            <div class="per connection_pending">Connection
                                                                pending</div>
                                                            @elseif ($lead['send_connections'] == 'replied')
                                                            <div class="per replied">Replied</div>
                                                            @else
                                                            <div class="per replied">Disconnected</div>
                                                            @endif
                                                        </td>
                                                        <td style="color: {{ $lead['current_step'] ? '' : 'red' }}; font-weight: {{ $lead['current_step'] ? '' : 'bold' }};">
                                                            {{ $lead['current_step'] ?? 'Step 1' }}
                                                        </td>
                                                        <td style="color: {{ $lead['next_step'] ? '' : 'green' }}; font-weight: {{ $lead['next_step'] ? '' : 'bold' }};">
                                                            {{ $lead['next_step'] ?? 'Completed' }}
                                                        </td>
                                                        <td>
                                                            <div class="">
                                                                {{ $lead['created_at']->diffInDays(now()) }}
                                                                days ago
                                                            </div>
                                                        </td>
                                                        <!-- <td><div class="per">23%</div> -->
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>
                                                            <!--<ul class="setting_list" style="display: block;">-->
                                                            <!--    <li><a href="#">Edit</a></li>-->
                                                            <!--    <li><a href="#">Delete</a></li>-->
                                                            <!--</ul>-->
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="8" style="z-index: 99">
                                                            <div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">
                                                                Not Found!</div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Step Content -->
                                <div class="tab-pane lead_pane" id="Steps" role="tabpanel">
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
                                                </div>
                                            </form>
                                            <div class="date" id="created_at">
                                                <i class="fa-solid fa-calendar-days"></i>Created at: 2023-10-05 16:48
                                            </div>
                                        </div>
                                        <div class="email_setting">
                                            <div class="border_box">
                                                <h3>Email settings</h3>
                                                <div class="accordion" id="accordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                Email accounts to use for this campaign
                                                            </button>
                                                        </h2>
                                                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordion">
                                                            <div class="accordion-body">
                                                                <strong>This is the second item's accordion body.</strong> It is
                                                                hidden by default, until the collapse plugin adds the
                                                                appropriate
                                                                classes that we use to style each element. These classes control
                                                                the
                                                                overall appearance, as well as the showing and hiding via CSS
                                                                transitions. You can modify any of this with custom CSS or
                                                                overriding our default variables. It's also worth noting that
                                                                just
                                                                about any HTML can go within the <code>.accordion-body</code>,
                                                                though the transition does limit overflow.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwo">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                                Schedule email
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordion">
                                                            <div class="accordion-body">
                                                                <div class="schedule-tab">
                                                                    <button class="schedule-btn active" id="my_email_schedule_btn" data-tab="my_email_schedule">My Schedules</button>
                                                                    <button class="schedule-btn " id="team_email_schedule_btn" data-tab="team_email_schedule">Team schedules</button>
                                                                </div>
                                                                <div class="active schedule-content" id="my_email_schedule">
                                                                    @if (!empty($campaign_schedule->first()))
                                                                    <ul class="schedule_list" id="schedule_list_1">
                                                                        @foreach ($campaign_schedule as $schedule)
                                                                        <li>
                                                                            <div class="row schedule_list_item">
                                                                                <div class="col-lg-2 schedule_item">
                                                                                    <input type="radio" class="schedule_id email_settings_schedule_id" value="{{ $schedule['id'] }}" {{ $schedule['user_id'] == '0' ? 'checked' : '' }}>
                                                                                </div>
                                                                                <div class="col-lg-2 schedule_avatar">S
                                                                                </div>
                                                                                <div class="col-lg-2 schedule_name">
                                                                                    <i class="fa-solid fa-circle-check" style="color: #4bcea6;"></i>
                                                                                    <span>{{ $schedule['schedule_name'] }}</span>
                                                                                </div>
                                                                                <div class="col-lg-6 schedule_days">
                                                                                    @php
                                                                                    $schedule_days = App\Models\ScheduleDays::where(
                                                                                    'schedule_id',
                                                                                    $schedule['id'],
                                                                                    )
                                                                                    ->orderBy('id')
                                                                                    ->get();
                                                                                    @endphp
                                                                                    <ul class="schedule_day_list">
                                                                                        @foreach ($schedule_days as $day)
                                                                                        <li class="schedule_day {{ $day['is_active'] == '1' ? 'selected_day' : '' }}">
                                                                                            {{ ucfirst($day['schedule_day']) }}
                                                                                        </li>
                                                                                        @endforeach
                                                                                        <li class="schedule_time">
                                                                                            <button href="javascript:;" type="button" class="btn" data-bs-toggle="modal" data-bs-target="#time_modal"><i class="fa-solid fa-globe" style="color: #16adcb;"></i></button>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        @endforeach
                                                                    </ul>
                                                                    @endif
                                                                </div>
                                                                <div class=" schedule-content" id="team_email_schedule">Hello
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingThree">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                                Email tracking preference
                                                            </button>
                                                        </h2>
                                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordion">
                                                            <div class="accordion-body">
                                                                <div class="linked_set d-flex justify-content-between">
                                                                    <p> Track the number of email link clicks </p>
                                                                    <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="email_settings_track_the_number_of_email_link_clicks" disabled><label for="email_settings_track_the_number_of_email_link_clicks">Toggle</label>
                                                                    </div>
                                                                </div>
                                                                <div class="linked_set d-flex justify-content-between">
                                                                    <p> Track the number of opened emails </p>
                                                                    <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="email_settings_track_the_number_of_opened_emails" disabled><label for="email_settings_track_the_number_of_opened_emails">Toggle</label>
                                                                    </div>
                                                                </div>
                                                                <div class="linked_set d-flex justify-content-between">
                                                                    <p> Text only email (no HTML) <span title="Send email messages that only include text without images, graphics or formatting. If you enable this option, you won't be able to track open and link click rates.">!</span>
                                                                    </p>
                                                                    <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="email_settings_text_only_email_no_html" disabled><label for="email_settings_text_only_email_no_html">Toggle</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h3>LinkedIn settings</h3>
                                                <div class="email_settings">
                                                    <ul class="list-unstyled p-0">
                                                        <li class="d-flex justify-content-between align-items-center ">
                                                            <span>Discover premium LinkedIn account only</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="linkedin_settings_discover_premium_linked_accounts_only" disabled><label for="linkedin_settings_discover_premium_linked_accounts_only">Toggle</label></div>
                                                        </li>
                                                        <li class="d-flex justify-content-between align-items-center">
                                                            <span>Discover leads with open profile status only</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="linkedin_settings_discover_leads_with_open_profile_status_only" disabled><label for="linkedin_settings_discover_leads_with_open_profile_status_only">Toggle</label></div>
                                                        </li>
                                                        <li class="d-flex justify-content-between align-items-center">
                                                            <span> Collect contact information</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="linkedin_settings_collect_contact_information" disabled><label for="linkedin_settings_collect_contact_information">Toggle</label></div>
                                                        </li>
                                                        <li class="d-flex justify-content-between align-items-center">
                                                            <span>Remove leads with pending connection requests</span>
                                                            <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="linkedin_settings_remove_leads_with_pending_connections" disabled><label for="linkedin_settings_remove_leads_with_pending_connections">Toggle</label></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <hr>
                                                <h3>Global settings</h3>
                                                <div class="accordion" id="accordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingOne">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                                                Targeting options
                                                            </button>
                                                        </h2>
                                                        <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordion">
                                                            <div class="accordion-body">
                                                                <div class="linked_set d-flex justify-content-between">
                                                                    <p> Include leads that replied to your messages
                                                                        <span title="Include all leads you previously had a conversation with via Linkedin messages, inMails, or email">!</span>
                                                                    </p>
                                                                    <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="global_settings_include_leads_that_replied_to_your_messages" disabled><label for="global_settings_include_leads_that_replied_to_your_messages">Toggle</label>
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="linked_set d-flex justify-content-between">
                                                            <p> Include leads also found in campaigns across your team
                                                                seats
                                                                <span>!</span>
                                                            </p>
                                                            <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="global_settings_include_leads_also_found_in_campaigns_across_your_team_seats" disabled><label for="global_settings_include_leads_also_found_in_campaigns_across_your_team_seats">Toggle</label>
                                                            </div>
                                                        </div> -->
                                                                <div class="linked_set d-flex justify-content-between">
                                                                    <p> Discover new leads only <span title="Leads that exist in other campaigns in your seat will not be discovered">!</span>
                                                                    </p>
                                                                    <div class="switch_box"><input type="checkbox" class="switch setting_switch" id="global_settings_discover_new_leads_only" disabled><label for="global_settings_discover_new_leads_only">Toggle</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTwo">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                                Schedule campaign
                                                            </button>
                                                        </h2>
                                                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordion">
                                                            <div class="accordion-body">
                                                                <div class="schedule-tab">
                                                                    <button class="schedule-btn active" id="my_campaign_schedule_btn" data-tab="my_campaign_schedule">My Schedules</button>
                                                                    <button class="schedule-btn " id="team_campaign_schedule_btn" data-tab="team_campaign_schedule">Team
                                                                        schedules</button>
                                                                </div>
                                                                <div class="active schedule-content" id="my_campaign_schedule">
                                                                    @if (!empty($campaign_schedule->first()))
                                                                    <ul class="schedule_list" id="schedule_list_2">
                                                                        @foreach ($campaign_schedule as $schedule)
                                                                        <li>
                                                                            <div class="row schedule_list_item">
                                                                                <div class="col-lg-2 schedule_item">
                                                                                    <input type="radio" class="schedule_id global_settings_schedule_id" value="{{ $schedule['id'] }}" {{ $schedule['user_id'] == '0' ? 'checked' : '' }}>
                                                                                </div>
                                                                                <div class="col-lg-2 schedule_avatar">S
                                                                                </div>
                                                                                <div class="col-lg-2 schedule_name">
                                                                                    <i class="fa-solid fa-circle-check" style="color: #4bcea6;"></i>
                                                                                    <span>{{ $schedule['schedule_name'] }}</span>
                                                                                </div>
                                                                                <div class="col-lg-6 schedule_days">
                                                                                    @php
                                                                                    $schedule_days = App\Models\ScheduleDays::where(
                                                                                    'schedule_id',
                                                                                    $schedule['id'],)
                                                                                    ->orderBy('id')
                                                                                    ->get();
                                                                                    @endphp
                                                                                    <ul class="schedule_day_list">
                                                                                        @foreach ($schedule_days as $day)
                                                                                        <li class="schedule_day {{ $day['is_active'] == '1' ? 'selected_day' : '' }}">
                                                                                            {{ ucfirst($day['schedule_day']) }}
                                                                                        </li>
                                                                                        @endforeach
                                                                                        <li class="schedule_time">
                                                                                            <button href="javascript:;" type="button" class="btn" data-bs-toggle="modal" data-bs-target="#time_modal"><i class="fa-solid fa-globe" style="color: #16adcb;"></i></button>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        @endforeach
                                                                    </ul>
                                                                    @endif
                                                                </div>
                                                                <div class=" schedule-content" id="team_campaign_schedule">
                                                                    Hello</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Stats Content -->
                                <div class="tab-pane lead_pane" id="Stats" role="tabpanel">
                                    <div class="chart_box">
                                        <div class="border_box">
                                            <div class="chart_filter d-flex justify-content-between">
                                                <div class="select d-flex">
                                                    <select name="timezone">
                                                        <option value="GMT+01:00">Central European Time (CET) -
                                                            GMT+01:00</option>
                                                        <option value="GMT+01:01">Central European Time (CET) -
                                                            GMT+01:01</option>
                                                        <option value="GMT+01:02">Central European Time (CET) -
                                                            GMT+01:02</option>
                                                        <!-- Add more timezone options here if needed -->
                                                    </select>
                                                    <select name="post-sales-tips">
                                                        <option value="01.09. Post-Sales Tips">01.09. Post-Sales Tips
                                                        </option>
                                                        <option value="01.10. Post-Sales Tips">01.10. Post-Sales Tips
                                                        </option>
                                                        <option value="01.11. Post-Sales Tips">01.11. Post-Sales Tips
                                                        </option>
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
                                            <!-- <img src="{{ asset('assets/img/chart.png') }}" alt=""> -->
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
                                                        @for ($i = 0; $i <= 7; $i++) <tr>
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
                                            <img src="{{ asset('assets/img/temp.png') }}" alt="">
                                            <p class="text-center">you need to choose some campaign to check out
                                                statistics by step.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Campaign Integration Content -->
                                <div class="tab-pane lead_pane" id="integration" role="tabpanel">
                                    <div class="leads_int">
                                        <div class="border_box">
                                            <div class="int_box">
                                                <h4>Use a Webhook to add leads to your campaign</h4>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                    eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem
                                                    ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                    tempor incididunt ut labore et dolore magna aliqua.</p>
                                                <ul class="integration_list list-unstyled p-0">
                                                    <li><strong>Method:</strong><span>Post</span></li>
                                                    <li><strong>URL to use:</strong><span class="url">https://lorem.ipsum/dolor/sit-amet</span>
                                                    </li>
                                                    <li><strong>Headers:</strong><span>Lorem ipsum</span></li>
                                                    <li><strong>Payload to use:</strong>
                                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                                            sed do eiusmod tempor incididunt ut labore et dolore magna
                                                            aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing
                                                            elit, sed do eiusmod tempor incididunt ut labore et dolore
                                                            magna aliqua.</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="border_box">
                                            <div class="int_box">
                                                <h4>Use a Webhook to add leads to your campaign</h4>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                    eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem
                                                    ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                    tempor incididunt ut labore et dolore magna aliqua.</p>
                                                <ul class="integration_list list-unstyled p-0">
                                                    <li><strong>Method:</strong><span>Post</span></li>
                                                    <li><strong>URL to use:</strong><span class="url">https://lorem.ipsum/dolor/sit-amet</span>
                                                    </li>
                                                    <li><strong>Headers:</strong><span>Lorem ipsum</span></li>
                                                    <li><strong>Payload to use:</strong>
                                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                                            sed do eiusmod tempor incididunt ut labore et dolore magna
                                                            aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing
                                                            elit, sed do eiusmod tempor incididunt ut labore et dolore
                                                            magna aliqua.</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <hr>
                                            <div class="int_box">
                                                <h4>Use a Webhook to add leads to your campaign</h4>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                    eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem
                                                    ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                                    tempor incididunt ut labore et dolore magna aliqua.</p>
                                                <ul class="integration_list list-unstyled p-0">
                                                    <li><strong>Method:</strong><span>Post</span></li>
                                                    <li><strong>URL to use:</strong><span class="url">https://lorem.ipsum/dolor/sit-amet</span>
                                                    </li>
                                                    <li><strong>Headers:</strong><span>Lorem ipsum</span></li>
                                                    <li><strong>Payload to use:</strong>
                                                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                                            sed do eiusmod tempor incididunt ut labore et dolore magna
                                                            aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing
                                                            elit, sed do eiusmod tempor incididunt ut labore et dolore
                                                            magna aliqua.</span>
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
                <form id="export_form">
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <p class="w-75">Once the export is complete, we will send you the exported data is a
                                    CSV file. Please insert the email you would like us to use.</p>
                                <input name="export_email" id="export_email" type="email" placeholder="example@gmail.com">
                                <span style="color: red; display: none;" id="email_error"></span>
                            </div>
                        </div>
                        <a href="javascript:;" id="export_leads" class="crt_btn ">Submit<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var leadsCampaignFilterPath = "{{ route('getLeadsByCampaign', [':id', ':search']) }}";
    var sendLeadsToEmail = "{{ route('sendLeadsToEmail') }}";
    $(document).ready(function() {
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}`;
        $("#created_at").html(
            '<i class="fa-solid fa-calendar-days"></i>Created at: ' +
            formattedDate
        );
        $(".setting_list").hide();
        $(".setting_btn").on("click", function(e) {
            $(".setting_list").not($(this).siblings(".setting_list")).hide();
            $(this).siblings(".setting_list").toggle();
        });
        $(document).on("click", function(e) {
            if (!$(event.target).closest(".setting").length) {
                $(".setting_list").hide();
            }
        });
    });
</script>
@endsection