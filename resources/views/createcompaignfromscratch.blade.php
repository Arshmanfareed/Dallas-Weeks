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
                                <h3>Smart sequence</h3>
                                <div class="filt_opt d-flex">
                                    <div class="add_btn">
                                        <span><a href=""><i
                                                    class="fa-solid fa-up-right-and-down-left-from-center"></i></a>Sequence
                                            template</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row crt_cmp_r sequence-steps">
                        <div class="col-lg-9 drop-pad">
                            <h5>Sequence Steps</h5>
                            <div class="task-list"></div>
                        </div>
                        <div class="col-lg-3 element-list">
                            <h5>Add Elements</h5>
                            <ul class='drop-list'>
                                <li>
                                    <div class="element" id="view_profile" data-filter-item data-filter-name="view_profile">
                                        <div class="attach-elements attach-elements-in"></div>
                                        <div class="cancel-icon">
                                            <i class="fa-solid fa-x"></i>
                                        </div>
                                        <div class="list-icon">
                                            <i class="fa-solid fa-eye"></i>
                                        </div>
                                        <div class="item_details">
                                            <p class="item_name">View Profile</p>
                                            <p class="item_desc">Lorem ipsum dolor sit amet consectetur
                                                adipisicing elit.</p>
                                        </div>
                                        <div class="menu-icon">
                                            <i class="fa-solid fa-bars"></i>
                                        </div>
                                        <div class="attach-elements attach-elements-out"></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="element" id="invite_to_connect" data-filter-item
                                        data-filter-name="invite_to_connect">
                                        <div class="attach-elements attach-elements-in"></div>
                                        <div class="cancel-icon">
                                            <i class="fa-solid fa-x"></i>
                                        </div>
                                        <div class="list-icon">
                                            <i class="fa-solid fa-share"></i>
                                        </div>
                                        <div class="item_details">
                                            <p class="item_name">Invite to Connect</p>
                                            <p class="item_desc">Lorem ipsum, dolor sit amet consectetur
                                                adipisicing elit.</p>
                                        </div>
                                        <div class="menu-icon">
                                            <i class="fa-solid fa-bars"></i>
                                        </div>
                                        <div class="attach-elements attach-elements-out"></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
