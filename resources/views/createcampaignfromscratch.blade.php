@extends('partials/dashboard_header')
@section('content')
    @php
        $linkedin_setting_json = json_encode($linkedin_setting);
    @endphp
    <section class="main_dashboard blacklist  campaign_sec">
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
                            <div class="task-list">
                                <div class="step-1 element_item" id="step-1">
                                    <div class="list-icon">
                                        <i class="fa-solid fa-certificate"></i>
                                    </div>
                                    <div class="item_details">
                                        <p class="item_name">Lead Source (Step 1)</p>
                                        <p class="item_desc">Lorem ipsum dolor sit amet consectetur
                                            adipisicing elit.</p>
                                    </div>
                                    <div class="element_change_output attach-elements-out condition_true"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 add-elements">
                            <div class="element-tab">
                                <button class="element-btn active" id="element-list-btn" data-tab="element-list">Add
                                    Elements</button>
                                <button class="element-btn" id="properties-btn" data-tab="properties">Properties</button>
                            </div>
                            <div class="element-list element-content active" id="element-list">
                                <div class="element_div">
                                    @if (!empty($campaigns))
                                        <div class="action_elements">
                                            <p>Actions</p>
                                            <ul class='drop-list'>
                                                @foreach ($campaigns as $campaign)
                                                    <li>
                                                        <div class="element element_item"
                                                            id="{{ $campaign['element_slug'] }}" data-filter-item
                                                            data-filter-name="{{ $campaign['element_slug'] }}">
                                                            <div
                                                                class="element_change_input attach-elements attach-elements-in">
                                                            </div>
                                                            <div class="cancel-icon">
                                                                <i class="fa-solid fa-x"></i>
                                                            </div>
                                                            <div class="list-icon">
                                                                {!! $campaign['element_icon'] !!}
                                                            </div>
                                                            <div class="item_details">
                                                                <p class="item_name">{{ $campaign['element_name'] }}</p>
                                                                <p class="item_desc">Lorem ipsum dolor sit amet consectetur
                                                                    adipisicing elit.</p>
                                                            </div>
                                                            <div class="menu-icon">
                                                                <i class="fa-solid fa-bars"></i>
                                                            </div>
                                                            <div
                                                                class="element_change_output attach-elements attach-elements-out condition_true">
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!empty($conditional_campaigns))
                                        <div class="conditional_elements">
                                            <p>Conditions</p>
                                            <ul class='drop-list'>
                                                @foreach ($conditional_campaigns as $campaign)
                                                    <li>
                                                        <div class="element element_item"
                                                            id="{{ $campaign['element_slug'] }}" data-filter-item
                                                            data-filter-name="{{ $campaign['element_slug'] }}">
                                                            <div
                                                                class="element_change_input conditional-elements conditional-elements-in">
                                                            </div>
                                                            <div class="cancel-icon">
                                                                <i class="fa-solid fa-x"></i>
                                                            </div>
                                                            <div class="list-icon">
                                                                {!! $campaign['element_icon'] !!}
                                                            </div>
                                                            <div class="item_details">
                                                                <p class="item_name">{{ $campaign['element_name'] }}</p>
                                                                <p class="item_desc">Lorem ipsum dolor sit amet consectetur
                                                                    adipisicing elit.</p>
                                                            </div>
                                                            <div class="menu-icon">
                                                                <i class="fa-solid fa-bars"></i>
                                                            </div>
                                                            <div class="conditional-elements conditional-elements-out">
                                                                <div class="element_change_output condition_true">
                                                                    <i class="fa-solid fa-check"></i>
                                                                </div>
                                                                <div class="element_change_output condition_false">
                                                                    <i class="fa-solid fa-xmark"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="save-btns">
                                    <button id="save-changes">Save Changes</button>
                                </div>
                            </div>
                            <div class="properties element-content" id="properties"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection