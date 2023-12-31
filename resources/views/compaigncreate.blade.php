@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard blacklist  compaign_sec">
    <div class="container_fluid">
        <div class="row">
            <div class="col-lg-1">
                @include('partials/dashboard_sidebar_menu')
            </div>
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
                                    <li class="active"><span>1</span><a href="javascript:;">Campaign info</a></li>
                                    <li><span>2</span><a href="javascript:;">Campaign settings</a></li>
                                    <li><span>3</span><a href="javascript:;">Campaign steps</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row insrt_cmp_r">
                    <div class="border_box">
                        <div class="insrt_cont">
                            <p>What type of the campaign you will be running?</p>
                            <div class="crt_cmp_opt">
                                <ul class="list-unstyled d-flex justify-content-between align-items-center">
                                    <li class="border_box">
                                        <a href="javascript:;"><img src="assets/img/linkedin.svg" alt=""></a>
                                        <title>LinkedIn search result</title>
                                    </li>
                                    <li class="border_box">
                                        <a href="javascript:;"><img src="assets/img/navigation.svg" alt=""></a>
                                        <title>Sales navigator search result</title>
                                    </li>
                                    <li class="border_box">
                                        <a href="javascript:;"><img src="assets/img/recruiter.svg" alt=""></a>
                                        <title>Recruiter search result</title>
                                    </li>
                                    <li class="border_box">
                                        <a href="javascript:;"><img src="assets/img/import.svg" alt=""></a>
                                        <title>Import</title>
                                    </li>
                                    <li class="border_box">
                                        <a href="javascript:;"><img src="assets/img/engagement.svg" alt=""></a>
                                        <title>Post engagement</title>
                                    </li>
                                    <li class="border_box">
                                        <a href="javascript:;"><img src="assets/img/list.svg" alt=""></a>
                                        <title>Leads list</title>
                                    </li>
                                </ul>
                                <form id="campaign-form" class="campaign-form">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <label for="campaign-name">Campaign Name:</label>
                                            <input type="text" id="campaign-name" name="campaign-name" placeholder="Campaign name ex. Los angeles lead" required>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <label for="linkedin-url">LinkedIn URL:</label>
                                            <input type="url" id="linkedin-url" name="linkedin-url" placeholder="LinkedIn search URL" required>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <label for="connections">Connections:</label>
                                            <select id="connections" name="connections">
                                                <option value="1">1st-degree</option>
                                                <option value="2">2nd-degree</option>
                                                <option value="3">3rd-degree</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="cmp_btns d-flex justify-content-center align-items-center">
                            <a href="javascript:;" class="btn"><i class="fa-solid fa-arrow-left"></i>Back</a>
                            <a href="javascript:;" class="btn nxt_btn">Next<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection