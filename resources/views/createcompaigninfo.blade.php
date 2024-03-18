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
                                    <li class="active prev full"><span>1</span><a href="javascript:;">Campaign info</a></li>
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
                                    <button class="nav-link active" id="nav-email-tab" data-bs-toggle="tab" data-bs-target="#nav-email" type="button" role="tab" aria-controls="nav-email" aria-selected="true">Email settings</button>
                                    <button class="nav-link" id="nav-linkedin-tab" data-bs-toggle="tab" data-bs-target="#nav-linkedin" type="button" role="tab" aria-controls="nav-linkedin" aria-selected="false">LinkedIn settings</button>
                                    <button class="nav-link" id="nav-global-tab" data-bs-toggle="tab" data-bs-target="#nav-global" type="button" role="tab" aria-controls="nav-global" aria-selected="false">Global settings</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-email" role="tabpanel" aria-labelledby="nav-email-tab">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Email accounts to use for this campaign
                                            </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Schedule email
                                            </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Email tracking preference
                                            </button>
                                            </h2>
                                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="cmp_btns d-flex justify-content-center align-items-center">
                            <a href="javascript:;" class="btn"><i class="fa-solid fa-arrow-left"></i>Back</a>
                            <a href="javascript:;" class="btn nxt_btn">Next<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                                </div>
                                <div class="tab-pane fade" id="nav-linkedin" role="tabpanel" aria-labelledby="nav-linkedin-tab">
                                   <div class="linked_set d-flex justify-content-between">
                                       <p> Collect contact information <span>!</span></p>
                                       <div class="switch_box"><input type="checkbox" class="switch" id="switch0"><label for="switch0">Toggle</label></div>
                                   </div>
                                    <div class="cmp_btns d-flex justify-content-center align-items-center">
                            <a href="javascript:;" class="btn"><i class="fa-solid fa-arrow-left"></i>Back</a>
                            <a href="javascript:;" class="btn nxt_btn">Next<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                                </div>
                                <div class="tab-pane fade" id="nav-global" role="tabpanel" aria-labelledby="nav-global-tab">
                                        
                                        <div class="accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Targeting options
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        Schedule campaign
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
      </div>
    </div>
  </div>
</div>

                                     <div class="cmp_btns d-flex justify-content-center align-items-center">
                            <a href="javascript:;" class="btn"><i class="fa-solid fa-arrow-left"></i>Back</a>
                            <a href="javascript:;" type="button" class="btn nxt_btn" data-bs-toggle="modal" data-bs-target="#sequance_modal">Create sequence<i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal Create sequence -->
<div class="modal fade create_sequence_modal" id="sequance_modal" tabindex="-1" aria-labelledby="sequance_modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sequance_modal">Create a sequence</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
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
                    <a href="javascript:;" class="btn">From scratch</a>
                    <p>Create a sequence from scratch specify steps and everything.</p>
                </div>
            </div>
            <a href="javascript:;" class="crt_btn ">Create sequence<i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection