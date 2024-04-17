@extends('partials/dashboard_header')
@section('content')
    <section class="main_dashboard blacklist  campaign_sec">
        <div class="container_fluid">
            <div class="row">
                <div class="col-lg-1">
                    @include('partials/dashboard_sidebar_menu')
                </div>
                @php
                    $campaign_details_json = json_encode($campaign_details);
                @endphp
                <div class="col-lg-11 col-sm-12">
                    <div class="row crt_cmp_r">
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="cont">
                                    <h3>Campaigns</h3>
                                    <p>Choose between options and get your campaign running</p>
                                </div>
                                <div class="cmp_opt_link d-flex">
                                    <ul class="d-flex list-unstyled justify-content-end align-items-center">
                                        <li class="active prev full"><span>1</span><a href="javascript:;">Campaign info</a>
                                        </li>
                                        <li class="active "><span>2</span><a href="javascript:;">Campaign settings</a></li>
                                        <li><span>3</span><a href="javascript:;">Campaign steps</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row insrt_cmp_r">
                        <div class="border_box">
                            <div class="comp_tabs">
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-email-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-email" type="button" role="tab"
                                            aria-controls="nav-email" aria-selected="true">Email settings</button>
                                        <button class="nav-link" id="nav-linkedin-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-linkedin" type="button" role="tab"
                                            aria-controls="nav-linkedin" aria-selected="false">LinkedIn settings</button>
                                        <button class="nav-link" id="nav-global-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-global" type="button" role="tab"
                                            aria-controls="nav-global" aria-selected="false">Global settings</button>
                                    </div>
                                </nav>
                                <form id="linkedin_settings" method="POST"
                                    action="{{ route('createcampaignfromscratch') }}">
                                    @csrf
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="nav-email" role="tabpanel"
                                            aria-labelledby="nav-email-tab">
                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingOne">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                            aria-expanded="true" aria-controls="collapseOne">
                                                            Email accounts to use for this campaign
                                                        </button>
                                                    </h2>
                                                    <div id="collapseOne" class="accordion-collapse collapse"
                                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
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
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                            aria-expanded="false" aria-controls="collapseTwo">
                                                            Schedule email
                                                        </button>
                                                    </h2>
                                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="schedule-tab">
                                                                <button class="schedule-btn active" id="my_schedule_btn"
                                                                    data-tab="my_schedule">My Schedules</button>
                                                                <button class="schedule-btn " id="team_schedule_btn"
                                                                    data-tab="team_schedule">Team schedules</button>
                                                            </div>
                                                            <div class="active schedule-content" id="my_schedule">
                                                                <div class="schedule_content_row1">
                                                                    <p>Manage your schedules.</p>
                                                                    <button>Create Schedule</button>
                                                                </div>
                                                                <div class="schedule_content_row2">
                                                                    <input type="text"
                                                                        placeholder="Search schedules here...">
                                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                                </div>
                                                                @if (!empty($campaign_schedule))
                                                                    <ul class="schedule_list">
                                                                        @foreach ($campaign_schedule as $schedule)
                                                                            <li>
                                                                                <div class="row schedule_list_item">
                                                                                    <div class="col-lg-1 schedule_item">
                                                                                        <input type="radio"
                                                                                            name="schedule"
                                                                                            class="schedule" checked>
                                                                                    </div>
                                                                                    <div class="col-lg-1 schedule_avatar">S
                                                                                    </div>
                                                                                    <div class="col-lg-3 schedule_name">
                                                                                        <i class="fa-solid fa-circle-check"
                                                                                            style="color: #4bcea6;"></i>
                                                                                        <span>{{ $schedule['schedule_name'] }}</span>
                                                                                    </div>
                                                                                    <div class="col-lg-6 schedule_days">
                                                                                        @php
                                                                                            $schedule_days = App\Models\ScheduleDays::where(
                                                                                                'schedule_id',
                                                                                                $schedule['id'],
                                                                                            )->get();
                                                                                        @endphp
                                                                                        <ul class="schedule_day_list">
                                                                                            @foreach ($schedule_days as $day)
                                                                                                <li
                                                                                                    class="schedule_day {{ $day['is_active'] == '1' ? 'selected_day' : '' }}">
                                                                                                    {{ ucfirst($day['schedule_day']) }}
                                                                                                </li>
                                                                                            @endforeach
                                                                                            <li class="schedule_time">
                                                                                                <a href=""><i
                                                                                                        class="fa-solid fa-globe"
                                                                                                        style="color: #16adcb;"></i></a>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div
                                                                                        class="col-lg-1 schedule_menu_btn">
                                                                                        <i class="fa-solid fa-ellipsis-vertical"
                                                                                            style="color: #ffffff;"></i>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                            <div class=" schedule-content" id="team_schedule">Hwllo</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingThree">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                            aria-expanded="false" aria-controls="collapseThree">
                                                            Email tracking preference
                                                        </button>
                                                    </h2>
                                                    <div id="collapseThree" class="accordion-collapse collapse"
                                                        aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="linked_set d-flex justify-content-between">
                                                                <p> Track the number of email link clicks </p>
                                                                <div class="switch_box"><input type="checkbox"
                                                                        name="track_the_number_of_email_link_clicks"
                                                                        class="linkedin_setting_switch"
                                                                        id="track_the_number_of_email_link_clicks"><label
                                                                        for="track_the_number_of_email_link_clicks">Toggle</label>
                                                                </div>
                                                            </div>
                                                            <div class="linked_set d-flex justify-content-between">
                                                                <p> Track the number of opened emails </p>
                                                                <div class="switch_box"><input type="checkbox"
                                                                        name="track_the_number_of_opened_emails"
                                                                        class="linkedin_setting_switch"
                                                                        id="track_the_number_of_opened_emails"><label
                                                                        for="track_the_number_of_opened_emails">Toggle</label>
                                                                </div>
                                                            </div>
                                                            <div class="linked_set d-flex justify-content-between">
                                                                <p> Text only email (no HTML) <span>!</span></p>
                                                                <div class="switch_box"><input type="checkbox"
                                                                        name="text_only_email_no_html"
                                                                        class="linkedin_setting_switch"
                                                                        id="text_only_email_no_html"><label
                                                                        for="text_only_email_no_html">Toggle</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cmp_btns d-flex justify-content-center align-items-center">
                                                <a href="{{ url('/campaign/createcampaign') }}" class="btn"><i
                                                        class="fa-solid fa-arrow-left"></i>Back</a>
                                                <a href="javascript:;" class="btn next_tab nxt_btn">Next<i
                                                        class="fa-solid fa-arrow-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-linkedin" role="tabpanel"
                                            aria-labelledby="nav-linkedin-tab">
                                            <div class="linked_set d-flex justify-content-between">
                                                <p> Discover Premium Linked accounts only </p>
                                                <div class="switch_box"><input type="checkbox"
                                                        name="discover_premium_linked_accounts_only"
                                                        class="linkedin_setting_switch"
                                                        id="discover_premium_linked_accounts_only"><label
                                                        for="discover_premium_linked_accounts_only">Toggle</label></div>
                                            </div>
                                            <div class="linked_set d-flex justify-content-between">
                                                <p> Discover Leads with Open Profile status only </p>
                                                <div class="switch_box"><input type="checkbox"
                                                        name="discover_leads_with_open_profile_status_only"
                                                        class="linkedin_setting_switch"
                                                        id="discover_leads_with_open_profile_status_only"><label
                                                        for="discover_leads_with_open_profile_status_only">Toggle</label>
                                                </div>
                                            </div>
                                            <div class="linked_set d-flex justify-content-between">
                                                <p> Collect contact information <span>!</span></p>
                                                <div class="switch_box"><input type="checkbox"
                                                        name="collect_contact_information" class="linkedin_setting_switch"
                                                        id="collect_contact_information"><label
                                                        for="collect_contact_information">Toggle</label></div>
                                            </div>
                                            <div class="linked_set d-flex justify-content-between">
                                                <p> Remove leads with pending connections <span>!</span></p>
                                                <div class="switch_box"><input type="checkbox"
                                                        name="remove_leads_with_pending_connections"
                                                        class="linkedin_setting_switch"
                                                        id="remove_leads_with_pending_connections"><label
                                                        for="remove_leads_with_pending_connections">Toggle</label></div>
                                            </div>
                                            <div class="cmp_btns d-flex justify-content-center align-items-center">
                                                <a href="javascript:;" class="btn prev_tab"><i
                                                        class="fa-solid fa-arrow-left"></i>Back</a>
                                                <a href="javascript:;" class="btn next_tab nxt_btn">Next<i
                                                        class="fa-solid fa-arrow-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-global" role="tabpanel"
                                            aria-labelledby="nav-global-tab">

                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingOne">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse1"
                                                            aria-expanded="true" aria-controls="collapse1">
                                                            Targeting options
                                                        </button>
                                                    </h2>
                                                    <div id="collapse1" class="accordion-collapse collapse"
                                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="linked_set d-flex justify-content-between">
                                                                <p> Include leads that replied to your messages
                                                                    <span>!</span>
                                                                </p>
                                                                <div class="switch_box"><input type="checkbox"
                                                                        name="include_leads_that_replied_to_your_messages"
                                                                        class="linkedin_setting_switch"
                                                                        id="include_leads_that_replied_to_your_messages"><label
                                                                        for="include_leads_that_replied_to_your_messages">Toggle</label>
                                                                </div>
                                                            </div>
                                                            <div class="linked_set d-flex justify-content-between">
                                                                <p> Include leads also found in campaigns across your team
                                                                    seats
                                                                    <span>!</span>
                                                                </p>
                                                                <div class="switch_box"><input type="checkbox"
                                                                        name="include_leads_also_found_in_campaigns_across_your_team_seats"
                                                                        class="linkedin_setting_switch"
                                                                        id="include_leads_also_found_in_campaigns_across_your_team_seats"><label
                                                                        for="include_leads_also_found_in_campaigns_across_your_team_seats">Toggle</label>
                                                                </div>
                                                            </div>
                                                            <div class="linked_set d-flex justify-content-between">
                                                                <p> Discover new leads only <span>!</span>
                                                                </p>
                                                                <div class="switch_box"><input type="checkbox"
                                                                        name="discover_new_leads_only"
                                                                        class="linkedin_setting_switch"
                                                                        id="discover_new_leads_only"><label
                                                                        for="discover_new_leads_only">Toggle</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingTwo">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse2"
                                                            aria-expanded="false" aria-controls="collapse2">
                                                            Schedule campaign
                                                        </button>
                                                    </h2>
                                                    <div id="collapse2" class="accordion-collapse collapse"
                                                        aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
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
                                            </div>
                                            <div class="cmp_btns d-flex justify-content-center align-items-center">
                                                <a href="javascript:;" class="btn prev_tab"><i
                                                        class="fa-solid fa-arrow-left"></i>Back</a>
                                                <a href="javascript:;" type="button" class="btn nxt_btn"
                                                    data-bs-toggle="modal" data-bs-target="#sequance_modal">Create
                                                    sequence<i class="fa-solid fa-arrow-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal Create sequence -->
    <div class="modal fade create_sequence_modal" id="sequance_modal" tabindex="-1" aria-labelledby="sequance_modal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sequance_modal">Create a sequence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="border_box">
                                <img src="/assets/img/temp.png" alt="">
                                <a href="javascript:;" class="btn">From template</a>
                                <p>Create a sequence from our suggested templates.</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border_box">
                                <img src="/assets/img/creat_temp.png" alt="">
                                <a id="create_sequence" class="btn">From scratch</a>
                                <p>Create a sequence from scratch specify steps and everything.</p>
                            </div>
                        </div>
                        <a href="javascript:;" class="crt_btn ">Create sequence<i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
