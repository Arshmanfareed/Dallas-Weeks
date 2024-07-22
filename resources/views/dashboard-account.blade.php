@extends('partials/master')
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
<script src="{{ asset('assets/js/dashboard-account.js') }}"></script>
@if (!empty($user->token))
{!! '<p>Connected.</p>' !!}
@endif
<section class="dashboard">
    <div class="container-fluid">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (Session::has('success'))
        <div class="alert alert-success text-center">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <p>{{ Session::get('success') }}</p>
        </div>
        @endif
        <div class="row">
            @include('partials/dashboard_sidebar')
            <div class="col-lg-8">
                <div class="dashboard_cont">
                    <div class="row_filter d-flex align-items-center justify-content-between">
                        <div class="account d-flex align-items-center">
                            @php
                            $user = auth()->user();
                            @endphp
                            <img src="{{ asset('assets/img/account_img.png') }}" alt=""><span>{{ $user->name }}</span>
                        </div>
                        <div class="form_add d-flex">
                            <form action="/search" method="get" class="search-form">
                                <input type="text" name="q" placeholder="Search..." id="search_seat">
                                <button type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </form>
                            <div class="add_btn">
                                <a href="javascript:;" type="button" data-bs-toggle="modal" data-bs-target="#addaccount"><i class="fa-solid fa-plus"></i></a>Add account
                            </div>

                        </div>
                    </div>
                    <hr>
                    <div class="row_table">
                        @if (!empty($seats->first()))
                        <div class="add_account_div" style="width: 100%">
                            <div class="campaign_list">
                                <table class="data_table w-100">
                                    <thead>
                                        <tr>
                                            <th width="10%">Profile Pic</th>
                                            <th width="55%" class="text-left">Seat Name</th>
                                            <th width="25%">Status</th>
                                            <th width="10%" class="text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="campaign_table_body">
                                        @foreach ($seats as $seat)
                                        <tr id="{{ 'table_row_' . $seat->id }}" class="seat_table_row">
                                            <td class="seat_table_data"><img src="{{ asset('assets/img/acc.png') }}" alt="">
                                            </td>
                                            <td class="text-left seat_table_data">
                                                {{ $seat->username }}
                                            </td>
                                            <td>
                                                @if ($seat->connected)
                                                <div class="per discovered">Connected</div>
                                                @else
                                                <div class="per connected_not_replied">Disconnected</div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="javascript:;" type="button" class="setting setting_btn"><i class="fa-solid fa-gear"></i></a>
                                                <!-- <ul class="setting_list text-left" style="display: none">
                                                    <li><a href="{{ route('campaignDetails', ['campaign_id' => $seat->id]) }}">Check
                                                            campaign details</a></li>
                                                    <li><a href="{{ route('editCampaign', ['campaign_id' => $seat->id]) }}">Edit
                                                            campaign</a></li>
                                                </ul> -->
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="add_account_div">
                            <img src="{{ asset('assets/img/empty.png') }}" alt="">
                            <p class="text-center">You don't hanve any account yet. Start by adding your first account.</p>
                            <div class="add_btn">
                                <a href="javascript:;" type="button" data-bs-toggle="modal" data-bs-target="#addaccount"><i class="fa-solid fa-plus"></i></a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- basic modal -->
<div class="modal fade step_form_popup" id="addaccount" tabindex="-1" role="dialog" aria-labelledby="addaccount" aria-hidden="true">
    <div class="modal-dialog" style="border-radius: 45px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-center">Add Account</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Add Account Popup -->
                <form role="form" action="{{ route('stripe.post') }}" method="post" data-cc-on-file="false" data-stripe-publishable-key="pk_test_51KQb3pC6mJiJ0AUpeAjoS786h11qy1jW92S6gWsGD4NpK4JGOuKplhC2I0vHFgEWwRy7T9NwHDZPiILuzQPynCdK007sgX6ox6" method="post" class="form step_form require-validation" id="payment-form">
                    @csrf
                    <!-- Progress Bar -->
                    <div class="progress-bar">
                        <div class="progress" id="progress"></div>
                        <div class="progress-step active" data-title="Add account"></div>
                        <div class="progress-step" data-title="Company "></div>
                        <div class="progress-step" data-title="Payment"></div>
                    </div>
                    <!-- Steps -->
                    <div class="form-step active">
                        <h3>Personal Informations</h3>
                        <div class="form_row row">
                            <div class="input-group col-12">
                                <label for="username">User Name</label>
                                <input type="text" name="username" id="username" placeholder="User Name">
                            </div>
                            <div class="input-group col-6">
                                <label for="City">City</label>
                                <input type="text" name="city" id="City" placeholder="Enter your city">
                            </div>
                            <div class="input-group col-6">
                                <label for="State">State</label>
                                <input type="text" name="state" id="State" placeholder="Enter your state">
                            </div>
                            <div class="input-group col-12">
                                <label for="Company">Company name</label>
                                <input type="text" name="company" id="Company" placeholder="Enter your company name">
                            </div>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-prev">Previous</a>
                            <a class="btn btn-next">Next</a>
                        </div>
                    </div>
                    <div class="form-step ">
                        <h3>Contact Informations</h3>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email">
                        </div>
                        <div class="input-group">
                            <label for="phone">Phone Number</label>
                            <input type="phone" name="phone" id="phone">
                        </div>
                        <div class="input-group">
                            <label for="summary">Profile Summary</label>
                            <textarea name="summary" id="summary" cols="42" rows="6"></textarea>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-prev">Previous</a>
                            <a class="btn btn-next">Next</a>
                        </div>
                    </div>
                    <div class="form-step ">
                        <h3>Payment</h3>
                        <div class="experiences-group">
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group required'>
                                    <label class='control-label'>Name on Card</label>
                                    <input class='form-control' size='4' type='text'>
                                </div>
                            </div>
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group  required'>
                                    <label class='control-label'>Card Number</label>
                                    <input autocomplete='off' class='form-control card-number' size='20' type='text'>
                                </div>
                            </div>
                            <div class='form-row row'>
                                <div class='col-xs-12 col-md-4 form-group cvc required'>
                                    <label class='control-label'>CVC</label>
                                    <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text'>
                                </div>
                                <div class='col-xs-12 col-md-4 form-group expiration required'>
                                    <label class='control-label'>Expiration Month</label> <input class='form-control card-expiry-month' placeholder='MM' size='2' type='text'>
                                </div>
                                <div class='col-xs-12 col-md-4 form-group expiration required'>
                                    <label class='control-label'>Expiration Year</label>
                                    <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text'>
                                </div>
                            </div>
                            <!-- <div class='form-row'>
                                <div class='col-md-12 error form-group hide'>
                                    <div class='alert-danger alert'>Please correct the errors and try again.</div>
                                </div>
                            </div>  -->
                        </div>
                        <!-- <div class="add-experience">
                                <a class="add-exp-btn"> + Add Experience</a>
                            </div> -->
                        <div class="btn-group">
                            <a class="btn btn-prev">Previous</a>
                            <button class="btn btn-primary btn-lg btn-block" type="submit">Pay Now</button>
                            <!-- <input type="submit" value="Complete" name="complete" class="btn btn-complete"> -->
                        </div>
                    </div>
                </form>
            </div>
            <!-- <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" class="btn btn-primary">Save changes</button>                                                                                                                                                                                                                                           </div> -->
        </div>
    </div>
</div>
<div class="modal fade step_form_popup" id="update_seat" tabindex="-1" role="dialog" aria-labelledby="update_seat" aria-hidden="true">
    <div class="modal-dialog" style="border-radius: 45px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-center">Your subscription is <span id="active_subs">Active</span></h4>
                <button type="button" class="close mt-1" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i> Change
                                seat name
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="form-group">
                                <label for="seat_name">Seat Name: </label>
                                <input type="text" id="seat_input_name" name="seat_name">
                            </div>
                            <button id="update_seat_name" type="button" class="update_seat_name theme_btn mb-3" style="background-color: #16adcb" ;>Save Changes</button>
                        </div>
                    </div>
                    <div class="accordion-item d-none">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i> Change
                                seat time zone
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        </div>
                    </div>
                    <div class="accordion-item d-none">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i> Cancel
                                subscription
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="headingFour">
                                <i class="fa-solid fa-address-card fa-sm mr-2" style="color: #b0b0b0;"></i> Delete
                                seat
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            Are you sure you want to delete <span id="seat_name" style="color: #16adcb; font-weight: 600;"></span> seat?
                            <button id="delete_seat" type="button" class="theme_btn mb-3 delete_seat">Delete seat</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <a href="javascript:;" id="linkedin-auth-btn">Authenticate with LinkedIn</a> -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    var dashboardRoute = "{{ route('acc_dash') }}";
    var getSeatRoute = "{{ route('getSeatById', [':seat_id']) }}";
    var deleteSeatRoute = "{{ route('deleteSeat', [':seat_id']) }}";
    var updateNameRoute = "{{ route('updateName', [':seat_id', ':seat_name']) }}";
    var filterSeatRoute = "{{ route('filterSeat', [':search'])}}";
    jQuery(document).ready(function() {
        // Attach a click event to the LinkedIn authentication button
        jQuery('#linkedin-auth-btn').click(function(e) {
            e.preventDefault();
            // Perform the AJAX request to the LinkedIn authentication route
            jQuery.ajax({
                type: 'GET',
                url: '/auth/linkedin/redirect', // Change this URL to your actual route
                success: function(response) {
                    // Handle the success response if needed
                    console.log(response);
                    // After successful authentication, you can redirect to the callback route
                    window.location.href = '/auth/linkedin/callback';
                },
                error: function(error) {
                    // Handle the error response if needed
                    console.error(error);
                }
            });
        });
    });
</script>

<script>
    $('.btn-next').on('click', function(e) {
        var progress_step = $('.progress-step.active');
        var form_step = $('.form-step.active');
        $(form_step).removeClass('active');
        $(progress_step).removeClass('active');
        $(form_step).next('.form-step').addClass('active');
        $(progress_step).next('.progress-step').addClass('active');
        $('#progress').css({
            'width': $('.progress-step.active').position().left + 170,
        });
    });
    $('.btn-prev').on('click', function(e) {
        var progress_step = $('.progress-step.active');
        var form_step = $('.form-step.active');
        $(form_step).removeClass('active');
        $(progress_step).removeClass('active');
        $(form_step).prev('.form-step').addClass('active');
        $(progress_step).prev('.progress-step').addClass('active');
        console.log($('.progress-step.active').position().left);
        console.log($('.progress-step.active').next('.progress-step').position().left);
        $('#progress').css({
            'width': $('.progress-step.active').position().left + 170,
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#payment-form').on('submit', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: "{{ route('stripe.post') }}",
                data: formData,
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {
                    console.log(error);
                },
            });
        });
    });
</script>

@endsection