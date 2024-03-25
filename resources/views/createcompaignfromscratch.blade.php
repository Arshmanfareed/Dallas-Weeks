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
                            <div class="task-list">
                                <div class="step-1" id="step-1">
                                    <div class="list-icon">
                                        <i class="fa-solid fa-certificate"></i>
                                    </div>
                                    <div class="item_details">
                                        <p class="item_name">Lead Source (Step 1)</p>
                                        <p class="item_desc">Lorem ipsum dolor sit amet consectetur
                                            adipisicing elit.</p>
                                    </div>
                                    <div class="attach-elements-out"></div>
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
                                <ul class='drop-list'>
                                    @if (!empty($compaigns))
                                        @foreach ($compaigns as $compaign)
                                            <li>
                                                <div class="element" id="{{$compaign['element_slug']}}" data-filter-item
                                                    data-filter-name="{{$compaign['element_slug']}}">
                                                    <div class="attach-elements attach-elements-in"></div>
                                                    <div class="cancel-icon">
                                                        <i class="fa-solid fa-x"></i>
                                                    </div>
                                                    <div class="list-icon">
                                                        {!!$compaign['element_icon']!!}
                                                    </div>
                                                    <div class="item_details">
                                                        <p class="item_name">{{$compaign['element_name']}}</p>
                                                        <p class="item_desc">Lorem ipsum dolor sit amet consectetur
                                                            adipisicing elit.</p>
                                                    </div>
                                                    <div class="menu-icon">
                                                        <i class="fa-solid fa-bars"></i>
                                                    </div>
                                                    <div class="attach-elements attach-elements-out"></div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <div class="save-btns">
                                    <button id="save-changes">Save Changes</button>
                                </div>
                            </div>
                            <div class="properties element-content" id="properties">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
