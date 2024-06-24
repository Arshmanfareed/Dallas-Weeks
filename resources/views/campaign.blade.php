@extends('partials/dashboard_header')
@section('content')
<style>
    #payment-form input.form-control {
        color: white !important;
    }

    .alert.alert-success.text-center {
        background: #e3c935;
        color: #000;
        border: none;
        border-radius: 30px;
        padding: 20px;
        width: 50%;
        margin: 20px auto;
        margin-bottom: 50px;
    }

    .alert.alert-success.text-center p {
        margin: 0;
        color: #000;
        font-weight: 600;
        text-transform: uppercase;
    }

    .alert.alert-success.text-center a.close {
        width: 50px;
        height: 50px;
        position: absolute;
        top: 7px;
        right: 1%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100%;
        background: #0b3b6a;
        opacity: 1;
        color: #fff;
        font-weight: 400;
    }

    #update_seat .accordion .accordion-item .accordion-header button {
        background: #1C1E22 !important;
        width: 100%;
        border-radius: 30px !important;
        color: #fff;
        /* border: 1px solid #fff; */
        padding: 20px 15px;
        font-size: 18px;
    }


    #update_seat div#accordionExample {
        padding: 20px;
        padding-bottom: 50px;
    }

    #update_seat .accordion .accordion-item .accordion-header .accordion-button::after {
        color: #e3c935 !important;
        filter: invert(1);
    }

    #update_seat .accordion .accordion-item .accordion-header .accordion-button i {
        color: #e3c935 !important;
        font-size: 20px;
    }

    #update_seat .accordion .accordion-item .accordion-header {
        border-radius: 30px !important;
        overflow: hidden;
        border: 1px solid #fff;
    }

    #update_seat .collapse.show {
        padding-top: 40px;
        padding-bottom: 40px;
    }

    #update_seat button#delete_seat_11 {
        margin-top: 30px;
    }
</style>
@if (Session::has('success'))
<div class="alert alert-success text-center">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    <p>{{ Session::get('success') }}</p>
</div>
@php
session()->forget('success');
@endphp
@endif
@php
session()->forget('campaign_details');
session()->forget('edit_campaign_details');
@endphp
<section class="main_dashboard blacklist campaign_sec">
    <div class="container_fluid">
        <div class="row">
            <div class="col-lg-1">
                @include('partials/dashboard_sidebar_menu')
            </div>
            <div class="col-lg-11 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <h3>Campaigns</h3>
                            <div class="filt_opt d-flex">
                                <div class="add_btn ">
                                    <a href="/campaign/createcampaign" class=""><i class="fa-solid fa-plus"></i></a>Add Campaign
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="border_box dashboard_box">
                            <div class="count_div">
                                <strong>1092</strong>
                                <div class="cont">
                                    <span>Total connections</span>
                                    <div class="gray_back d-flex">
                                        <i class="fa-solid fa-arrow-up"></i>2%<span>Today</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="border_box dashboard_box">
                            <div class="count_div">
                                <strong>5915</strong>
                                <div class="cont">
                                    <span>Total profile views</span>
                                    <div class="gray_back d-flex">
                                        <i class="fa-solid fa-arrow-up"></i>2%<span>Today</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="border_box dashboard_box">
                            <div class="count_div">
                                <strong>984</strong>
                                <div class="cont ">
                                    <span>Total replies</span>
                                    <div class="gray_back d-flex down">
                                        <i class="fa-solid fa-arrow-down"></i>2%<span>Today</span>
                                    </div>
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
                                <strong>Campaigns</strong>
                                <div class="filter">
                                    <a id="filterToggle"><i class="fa-solid fa-filter"></i></a>
                                    <select id="filterSelect" style="display: none">
                                        <option value="active">Active Campaigns</option>
                                        <option value="inactive">InActive Campaigns</option>
                                        <option value="archive">Archive Campaigns</option>
                                    </select>
                                    <form method="get" class="search-form">
                                        <input id="search_campaign" type="text" name="q" placeholder="Search Campaign here...">
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
                            <p>Easily track your campaigns in one place.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="border_box ">
                            <div class="campaign_list">
                                <table class="data_table w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">Status</th>
                                            <th width="20%">Campaign name</th>
                                            <th width="10%">Total leads</th>
                                            <th width="10%">Sent messages</th>
                                            <th width="30%" class="stat">States</th>
                                            <th width="10%">Acceptance</th>
                                            <th width="10%">Response</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="campaign_table_body">
                                        @if (!empty($campaigns->first()))
                                        @foreach ($campaigns as $campaign)
                                        <tr id="{{ 'table_row_' . $campaign->id }}" class="campaign_table_row">
                                            <td>
                                                <div class="switch_box">
                                                    @if ($campaign->is_active == 1)
                                                    <input type="checkbox" class="switch" id="switch{{ $campaign->id }}" checked>
                                                    @else
                                                    <input type="checkbox" class="switch" id="switch{{ $campaign->id }}">
                                                    @endif
                                                    <label for="switch{{ $campaign->id }}">Toggle</label>
                                                </div>
                                            </td>
                                            <td>{{ $campaign->campaign_name }}</td>
                                            @php
                                            $leads = App\Models\Leads::where('campaign_id', $campaign->id)->get();
                                            $lc = new App\Http\Controllers\LeadsController();
                                            $lead = $lc->getLeadsCountByCampaign($campaign->id);
                                            $lead = json_decode($lead->getContent(), true);
                                            $viewProfile = $lc->getViewProfileByCampaign($campaign->id);
                                            $viewProfile = json_decode($viewProfile->getContent(), true);
                                            $inviteToConnect = $lc->getInviteToConnectByCampaign($campaign->id);
                                            $inviteToConnect = json_decode($inviteToConnect->getContent(), true);
                                            $message = $lc->getSentMessageByCampaign($campaign->id);
                                            $message = json_decode($message->getContent(), true);
                                            $emailMessage = $lc->getSentEmailByCampaign($campaign->id);
                                            $emailMessage = json_decode($emailMessage->getContent(), true);
                                            $lead_count = $lead['count'];
                                            $viewProfile_count = $viewProfile['count'];
                                            $inviteToConnect_count = $inviteToConnect['count'];
                                            $message_count = $message['count'];
                                            $emailMessage_count = $emailMessage['count'];
                                            @endphp
                                            <td id="{{ 'lead_count_' .  $campaign['id'] }}">
                                                {{ $lead_count }}
                                            </td>
                                            <td id="{{ 'sent_message_count_' .  $campaign['id'] }}">
                                                {{ $message_count }}
                                            </td>
                                            <td class="stats">
                                                <ul class="status_list d-flex align-items-center list-unstyled p-0 m-0">
                                                    <li><span><img src="{{ asset('assets/img/eye.svg') }}" alt=""><span id="{{ 'view_profile_count_' .  $campaign['id'] }}">{{ $viewProfile_count }}</span></span></li>
                                                    <li><span><img src="{{ asset('assets/img/request.svg') }}" alt=""><span id="{{ 'invite_to_connect_count_' .  $campaign['id'] }}">{{ $inviteToConnect_count }}</span></span></li>
                                                    <li><span><img src="{{ asset('assets/img/mailmsg.svg') }}" alt=""><span id="{{ 'email_message_count_' .  $campaign['id'] }}">{{ $emailMessage_count }}</span></span></li>
                                                    <!-- <li><span><img src="{{ asset('assets/img/mailopen.svg') }}" alt="">16</span></li> -->
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="per up">34%</div>
                                            </td>
                                            <td>
                                                <div class="per down">23%</div>
                                            </td>
                                            <td>
                                                <a type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>
                                                <ul class="setting_list" style="display: none">
                                                    <li><a href="{{ route('campaignDetails', ['campaign_id' => $campaign->id]) }}">Check
                                                            campaign details</a></li>
                                                    <li><a href="{{ route('editCampaign', ['campaign_id' => $campaign->id]) }}">Edit
                                                            campaign</a></li>
                                                    {{-- <li><a href="#">Duplicate campaign steps</a></li> --}}
                                                    {{-- <li><a href="javascript:;" data-bs-toggle="modal"
                                                                        data-bs-target="#add_new_leads_modal">Add new
                                                                        leads</a> --}}
                                                    </li>
                                                    {{-- <li><a href="#">Export data</a></li> --}}
                                                    <li><a class="archive_campaign" id="{{ 'archive' . $campaign->id }}">Archive
                                                            campaign</a>
                                                    </li>
                                                    <li><a class="delete_campaign" id="{{ 'delete' . $campaign->id }}">Delete
                                                            campaign</a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-center text-danger" style="font-size: 25px; font-weight: bold; font-style: italic;">
                                                    Not Found!
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    var filterCampaignRoute = "{{ route('filterCampaign', [':filter', ':search']) }}";
    var deleteCampaignRoute = "{{ route('deleteCampaign', ':id') }}";
    var activateCampaignRoute = "{{ route('changeCampaignStatus', ':campaign_id') }}";
    var archiveCampaignRoute = "{{ route('archiveCampaign', ':id') }}";
    var leadsCountRoute = "{{ route('getLeadsCountByCampaign', ':id') }}";
    var viewProfileCountRoute = "{{ route('getViewProfileByCampaign', ':id') }}";
    var inviteToConnectCountRoute = "{{ route('getInviteToConnectByCampaign', ':id') }}";
    var sentEmailRoute = "{{ route('getSentEmailByCampaign', ':id') }}";
    var sentMessageRoute = "{{ route('getSentMessageByCampaign', ':id') }}";
</script>
{{-- <div class="modal fade create_sequence_modal" id="sequance_modal" tabindex="-1" aria-labelledby="sequance_modal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sequance_modal">Duplicate & Blacklisted items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: left">
                            <p>This file contains:</p>
                            <ul>
                                <li>Total: <span class="bold_lead" id="total_leads">0 leads</span></li>
                                <li>Blacklisted items (found on your and global blacklist): <span class="bold_lead"
                                        id="blacklist_leads">0 leads</span></li>
                                <li>Duplicates found across your and your team's campaigns: <span class="bold_lead"
                                        id="duplicate_among_teams">0 leads</span></li>
                                <li>Duplicates found across CSV file: <span class="bold_lead" id="duplicate_csv_file">0
                                        leads</span></li>
                                <li>Total without duplicates and blacklisted leades: <span class="bold_lead"
                                        id="total_without_leads">0 leads</span></li>
                            </ul>
                        </div>
                        <div class="col-lg-12 modal_info">
                            <div class="info_icon"
                                title="Duplicates are found based on either profileUrl or email columns. We won't be able to identify duplicates with Sales Navigator URLs. This action will automatically remove the items that match all blacklists and existing duplicates within this line.">
                                !</div>
                            <p>Duplicates are found based on either profileUrl or email columns. We won't be able to
                                identify duplicates with Sales Navigator URLs.<br>This action will automatically remove the
                                items that match all blacklists and existing duplicates within this line.</p>
                        </div>
                        <a href="javascript:;" class="blacklist_btn cancel_btn" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</a>
                        <a href="javascript:;" class="blacklist_btn import_btn">Import <i
                                class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
<div class="modal fade create_add_new_leads_modal" id="add_new_leads_modal" tabindex="-1" aria-labelledby="add_new_leads_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_new_leads_modal">Add New Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="schedule-tab">
                        <button class="schedule-btn active" id="my_schedule_btn" data-tab="from_csv_file">From CSV
                            File</button>
                        <button class="schedule-btn " id="team_schedule_btn" data-tab="from_url">From URL</button>
                    </div>
                    <div class="active schedule-content" id="from_csv_file">
                        <label for="">Upload CSV File</label>
                        <input type="file" name="csv_file" id="csv_file">
                    </div>
                    <div class=" schedule-content" id="from_url">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection