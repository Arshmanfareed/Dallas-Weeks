@extends('partials/dashboard_header')
@section('content')
    <script>
        sessionStorage.removeItem('campaign_details');
        sessionStorage.removeItem('edit_campaign_details');
    </script>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
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
                                        <a href="/campaign/createcampaign" class=""><i
                                                class="fa-solid fa-plus"></i></a>Add Campaign
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
                                            <input id="search_campaign" type="text" name="q"
                                                placeholder="Search Campaign here...">
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
                                                                    <input type="checkbox" class="switch"
                                                                        id="switch{{ $campaign->id }}" checked>
                                                                @else
                                                                    <input type="checkbox" class="switch"
                                                                        id="switch{{ $campaign->id }}">
                                                                @endif
                                                                <label for="switch{{ $campaign->id }}">Toggle</label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $campaign->campaign_name }}</td>
                                                        <td>44</td>
                                                        <td>105</td>
                                                        <td class="stats">
                                                            <ul
                                                                class="status_list d-flex align-items-center list-unstyled p-0 m-0">
                                                                <li><span><img src="{{ asset('assets/img/eye.svg') }}"
                                                                            alt="">10</span></li>
                                                                <li><span><img src="{{ asset('assets/img/request.svg') }}"
                                                                            alt="">42</span></li>
                                                                <li><span><img src="{{ asset('assets/img/mailmsg.svg') }}"
                                                                            alt="">10</span></li>
                                                                <li><span><img src="{{ asset('assets/img/mailopen.svg') }}"
                                                                            alt="">16</span></li>
                                                            </ul>
                                                        </td>
                                                        <td>
                                                            <div class="per up">34%</div>
                                                        </td>
                                                        <td>
                                                            <div class="per down">23%</div>
                                                        </td>
                                                        <td>
                                                            <a type="button"
                                                                class="setting setting_btn" id=""><i
                                                                    class="fa-solid fa-gear"></i></a>
                                                            <ul class="setting_list" style="display: none">
                                                                <li><a
                                                                        href="{{ route('campaignDetails', ['campaign_id' => $campaign->id]) }}">Check
                                                                        campaign details</a></li>
                                                                <li><a
                                                                        href="{{ route('editCampaign', ['campaign_id' => $campaign->id]) }}">Edit
                                                                        campaign</a></li>
                                                                {{-- <li><a href="#">Duplicate campaign steps</a></li> --}}
                                                                {{-- <li><a href="javascript:;" data-bs-toggle="modal"
                                                                        data-bs-target="#add_new_leads_modal">Add new leads</a>
                                                                </li> --}}
                                                                {{-- <li><a href="#">Export data</a></li> --}}
                                                                <li><a class="archive_campaign"
                                                                        id="{{ 'archive' . $campaign->id }}">Archive
                                                                        campaign</a>
                                                                </li>
                                                                <li><a class="delete_campaign"
                                                                        id="{{ 'delete' . $campaign->id }}">Delete
                                                                        campaign</a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8">
                                                        <div class="text-center text-danger"
                                                            style="font-size: 25px; font-weight: bold; font-style: italic;">
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
    </script>
    {{-- <div class="modal fade create_add_new_leads_modal" id="add_new_leads_modal" tabindex="-1"
        aria-labelledby="add_new_leads_modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add_new_leads_modal">Add New Leads</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="schedule-tab">
                            <button class="schedule-btn active" id="my_schedule_btn" data-tab="from_csv_file">From CSV File</button>
                            <button class="schedule-btn " id="team_schedule_btn" data-tab="from_url">From URL</button>
                        </div>
                        <div class="active schedule-content" id="from_csv_file">
                            
                        </div>
                        <div class=" schedule-content" id="from_url">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
