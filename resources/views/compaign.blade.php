@extends('partials/dashboard_header')
@section('content')
<section class="main_dashboard blacklist  compaign_sec">
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
                                    <a href="/compaign/createcompaign" class="" ><i class="fa-solid fa-plus"></i></a>Add Campaign
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
                                    <a href="#"><i class="fa-solid fa-filter"></i></a>
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
                            <p>Easily track your campaigns in one place.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="border_box ">
                            <div class="compaign_list">
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
                            <tbody>
                                @for ($i = 0; $i < 8; $i++)
                                <tr>
                                    <td><div class="switch_box"><input type="checkbox" class="switch" id="switch{{$i}}"><label for="switch{{$i}}">Toggle</label></div></td>
                                    <td>Sed ut perspiciatis unde</td>
                                    <td>44</td>
                                    <td>105</td>
                                    <td class="stats">
                                        <ul class="status_list d-flex align-items-center list-unstyled p-0 m-0">
                                            <li><span><img src="/assets/img/eye.svg" alt="">10</span></li>
                                            <li><span><img src="/assets/img/request.svg" alt="">42</span></li>
                                            <li><span><img src="/assets/img/mailmsg.svg" alt="">10</span></li>
                                            <li><span><img src="/assets/img/mailopen.svg" alt="">16</span></li>
                                        </ul>
                                    </td>
                                    <td><div class="per up">34%</div></td>
                                    <td><div class="per down">23%</div>
                                </td>
                                <td>
                                    <a href="javascript:;" type="button" class="setting setting_btn" id=""><i class="fa-solid fa-gear"></i></a>
                                    <ul class="setting_list">
                                        <li><a href="#">Check campaign details</a></li>
                                        <li><a href="#">Edit campaign</a></li>
                                        <li><a href="#">Duplicate campaign steps</a></li>
                                        <li><a href="#">Add new leads</a></li>
                                        <li><a href="#">Export data</a></li>
                                        <li><a href="#">Archive campaign</a></li>
                                        <li><a href="#">Delete campaign</a></li>
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
</section>
@endsection