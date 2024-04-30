@extends('partials/dashboard_header')
@section('content')
    <section class="main_dashboard">
        <div class="container_fluid">

            <div class="row">
                <div class="col-lg-1">
                    @include('partials/dashboard_sidebar_menu')
                </div>
                <div class="col-lg-4 col-sm-12">
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
                        <div class="darkgrey_div">
                            <div class="d-flex justify-content-between">
                                <img src="{{ asset('assets/img/people.png') }}" alt="">
                                <div class="cont">Manage Connections<i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>

                    </div>
                    <div class="border_box">
                        <div class="chart_box">
                            <div class="d-flex justify-content-between">
                                <span>Campaign stats</span><a href="javascript:;"><img
                                        src="{{ asset('assets/img/settings.svg') }}" alt=""></a>
                            </div>
                            <div class="chart_canvas">
                                <div id="chartContainer" style="height: 350px; width: 100%;"></div>
                            </div>
                            <!-- <img src="{{ asset('assets/img/chart_1.png') }}" alt=""> -->
                        </div>
                        <div class="invite_date_box">
                            @for ($i = 0; $i <= 3; $i++)
                                <ul class="date d-flex list-unstyle">
                                    <li>
                                        <span>Date</span>
                                        2023-01-16
                                    </li>
                                    <li>
                                        <span>Views</span>
                                        25
                                    </li>
                                    <li class="invites">
                                        <span>Invites</span>
                                        2
                                    </li>
                                    <li>
                                        <span>Follows</span>
                                        15
                                    </li>
                                </ul>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-sm-12">
                    <div class="border_box">
                        <div class="campaign_box">
                            <div class="d-flex justify-content-between">
                                <span>Campaign stats</span><a href="javascript:;"><img
                                        src="{{ asset('assets/img/settings.svg') }}" alt=""></a>
                            </div>
                            <div class="campaign_data">
                                @foreach ($campaigns as $campaign)
                                    <ul class="campaign_list">
                                        <li>{{ $campaign->campaign_name }}</li>
                                        <li>44</li>
                                        <li><a href="javascript:;" class="campaign_stat">48%</a></li>
                                        <li><a href="javascript:;" class="campaign_stat down">23%</a></li>
                                        <li>
                                            <div class="switch_box">
                                                @if ($campaign->is_active == 1)
                                                    <input type="checkbox" class="switch" id="switch{{ $campaign->id }}"
                                                        checked />
                                                @else
                                                    <input type="checkbox" class="switch" id="switch{{ $campaign->id }}" />
                                                @endif
                                                <label for="switch{{ $campaign->id }}">Toggle</label>
                                            </div>
                                        </li>
                                    </ul>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div class="border_box">
                        <div class="campaign_box">
                            <div class="d-flex justify-content-between">
                                <span>Messages</span><a href="javascript:;"><img
                                        src="{{ asset('assets/img/settings.svg') }}" alt=""></a>
                            </div>
                            <div class="campaign_data">
                                @for ($i = 0; $i < 7; $i++)
                                    @php
                                        $asset_id = $i + 1;
                                        $asset = 'assets/img/acc_img' . $asset_id . '.png';
                                    @endphp
                                    <ul class="message_list">
                                        <li> <a href="/message"><img src="{{ asset($asset) }}" alt="">John doe </a>
                                        </li>
                                        <li>
                                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem</p>
                                        </li>
                                        <li><a href="javascript:;"><i class="fa-brands fa-linkedin"></i></a></li>
                                    </ul>
                                @endfor
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
