@extends('partials/dashboard_header')
@section('content')
    <section class="main_dashboard blacklist campaign_detail_sec">
        <div class="container_fluid">
            <div class="row">
                <div class="col-lg-1">
                    @include('partials/dashboard_sidebar_menu')
                </div>
                <div class="col-lg-11">
                    <div class="border_box">
                        <div class="camp_details">
                            <h4>Campaign Details:</h4>
                            <table class="details_table">
                                <tr>
                                    <td class="item_name">Name:</td>
                                    <td class="item_value">{{ $campaign->campaign_name }}</td>
                                </tr>
                                <tr>
                                    <td class="item_name">Type:</td>
                                    <td class="item_value">{{ $campaign->campaign_type }}</td>
                                </tr>
                                <tr>
                                    <td class="item_name">Url:</td>
                                    <td class="campaign_url"><a href="{{ $campaign->campaign_url }}"
                                            target="blank">{{ $campaign->campaign_url }}</a></td>
                                </tr>
                                @if (!empty($campaign->campaign_connection))
                                    <tr>
                                        <td class="item_name">Connections:</td>
                                        @if ($campaign->campaign_connection == '1')
                                            <td class="item_value">1st degree</td>
                                        @elseif ($campaign->campaign_connection == '2')
                                            <td class="item_value">2nd degree</td>
                                        @elseif ($campaign->campaign_connection == '3')
                                            <td class="item_value">3rd degree</td>
                                        @elseif ($campaign->campaign_connection == 'o')
                                            <td class="item_value">Others</td>
                                        @endif
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="comp_tabs">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-email-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-email" type="button" role="tab" aria-controls="nav-email"
                                        aria-selected="true">Email settings</button>
                                    <button class="nav-link" id="nav-linkedin-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-linkedin" type="button" role="tab"
                                        aria-controls="nav-linkedin" aria-selected="false">LinkedIn settings</button>
                                    <button class="nav-link" id="nav-global-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-global" type="button" role="tab"
                                        aria-controls="nav-global" aria-selected="false">Global settings</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-email" role="tabpanel"
                                    aria-labelledby="nav-email-tab">
                                    @php
                                        $email_settings = App\Models\EmailSetting::where(
                                            'campaign_id',
                                            $campaign->id,
                                        )->get();
                                    @endphp
                                    @foreach ($email_settings as $item)
                                        @if ($item->setting_slug != 'email_settings_schedule_id')
                                            <div class="linked_set d-flex justify-content-between">
                                                <p> {{ str_replace('Email Settings ', '', $item->setting_name) }}</p>
                                                <div class="switch_box"><input type="checkbox"
                                                        name="{{ $item->setting_slug }}" class="linkedin_setting_switch"
                                                        id="{{ $item->setting_slug }}"
                                                        {{ $item->value == 'yes' ? 'checked' : '' }}
                                                        data-id="{{ $item->id }}"><label
                                                        for="{{ $item->setting_slug }}">Toggle</label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @foreach ($email_settings as $item)
                                        @if ($item->setting_slug == 'email_settings_schedule_id')
                                            <div class="schedule_div">
                                                <table class="schedule_table border_box">
                                                    @php
                                                        $schedule = App\Models\CampaignSchedule::where(
                                                            'id',
                                                            $item->value,
                                                        )->first();
                                                    @endphp
                                                    <thead>
                                                        <th class="text-center text-danger" colspan="4">Schedule: <span
                                                                class="ml-3">{{ $schedule->schedule_name }}</span></th>
                                                    </thead>
                                                    <thead>
                                                        <th>Day</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Status</th>
                                                    </thead>
                                                    @php
                                                        $schedules = App\Models\ScheduleDays::where(
                                                            'schedule_id',
                                                            $item->value,
                                                        )
                                                            ->orderBy('id')
                                                            ->get();
                                                    @endphp
                                                    @if (!empty($schedules))
                                                        <tbody>
                                                            @foreach ($schedules as $day)
                                                                <tr>
                                                                    <td>{{ ucfirst($day->schedule_day) }}</td>
                                                                    <td>{{ date('h:i A', strtotime($day->start_time)) }}
                                                                    </td>
                                                                    <td>{{ date('h:i A', strtotime($day->end_time)) }}</td>
                                                                    <td>{{ $day->is_active == 1 ? 'Open' : 'Closed' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @else
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="4">
                                                                    <div class="text-center text-danger"
                                                                        style="font-size: 20px; font-weight: bold; font-style: italic; background-color: #1c1e22; border-radius: 25px;">
                                                                        No Scedule Found
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endif
                                                </table>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="nav-linkedin" role="tabpanel"
                                    aria-labelledby="nav-linkedin-tab">
                                    @php
                                        $linkedin_settings = App\Models\LinkedinSetting::where(
                                            'campaign_id',
                                            $campaign->id,
                                        )->get();
                                    @endphp
                                    @foreach ($linkedin_settings as $item)
                                        <div class="linked_set d-flex justify-content-between">
                                            <p>{{ str_replace('Linkedin Settings ', '', $item->setting_name) }}</p>
                                            <div class="switch_box"><input type="checkbox" name="{{ $item->setting_slug }}"
                                                    class="linkedin_setting_switch" id="{{ $item->setting_slug }}"
                                                    {{ $item->value == 'yes' ? 'checked' : '' }}
                                                    data-id="{{ $item->id }}"><label
                                                    for="{{ $item->setting_slug }}">Toggle</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="nav-global" role="tabpanel" aria-labelledby="nav-global-tab">
                                    @php
                                        $global_settings = App\Models\GlobalSetting::where(
                                            'campaign_id',
                                            $campaign->id,
                                        )->get();
                                    @endphp
                                    @foreach ($global_settings as $item)
                                        @if ($item->setting_slug != 'global_settings_schedule_id')
                                            <div class="linked_set d-flex justify-content-between">
                                                <p>{{ str_replace('Global Settings ', '', $item->setting_name) }}</p>
                                                <div class="switch_box"><input type="checkbox"
                                                        name="{{ $item->setting_slug }}" class="linkedin_setting_switch"
                                                        id="{{ $item->setting_slug }}"
                                                        {{ $item->value == 'yes' ? 'checked' : '' }}
                                                        data-id="{{ $item->id }}"><label
                                                        for="{{ $item->setting_slug }}">Toggle</label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @foreach ($global_settings as $item)
                                        @if ($item->setting_slug == 'global_settings_schedule_id')
                                            <div class="schedule_div">
                                                <table class="schedule_table border_box">
                                                    @php
                                                        $schedule = App\Models\CampaignSchedule::where(
                                                            'id',
                                                            $item->value,
                                                        )->first();
                                                    @endphp
                                                    <thead>
                                                        <th class="text-center text-danger" colspan="4">Schedule: <span
                                                                class="ml-3">{{ $schedule->schedule_name }}</span></th>
                                                    </thead>
                                                    <thead>
                                                        <th>Day</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Status</th>
                                                    </thead>
                                                    @php
                                                        $schedules = App\Models\ScheduleDays::where(
                                                            'schedule_id',
                                                            $item->value,
                                                        )
                                                            ->orderBy('id')
                                                            ->get();
                                                    @endphp
                                                    @if (!empty($schedules))
                                                        <tbody>
                                                            @foreach ($schedules as $day)
                                                                <tr>
                                                                    <td>{{ ucfirst($day->schedule_day) }}</td>
                                                                    <td>{{ date('h:i A', strtotime($day->start_time)) }}
                                                                    </td>
                                                                    <td>{{ date('h:i A', strtotime($day->end_time)) }}</td>
                                                                    <td>{{ $day->is_active == 1 ? 'Open' : 'Closed' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @else
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="4">
                                                                    <div class="text-center text-danger"
                                                                        style="font-size: 20px; font-weight: bold; font-style: italic; background-color: #1c1e22; border-radius: 25px;">
                                                                        No Scedule Found
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endif
                                                </table>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row camp_sequence">
                            <div class="col-lg-9">
                                <img src="{{ $campaign->img_path }}" alt="">
                            </div>
                            {{-- <div class="col-lg-9 drop-pad">
                                <h5>Sequence Steps</h5>
                                <div class="task-list"></div>
                            </div> --}}
                            <div class="col-lg-3 add-elements">
                                <div class="element-tab">
                                    <button class="element-btn active" id="properties-btn"
                                        data-tab="properties">Properties</button>
                                </div>
                                <div class="properties active" id="properties"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        var campaign_id = {{ $campaign->id }};
        var getElementPath = "{{ route('getElements', ':campaign_id') }}";
        var getCampaignElementPath = "{{ route('getcampaignelementbyid', ':element_id') }}";
    </script>
@endsection
