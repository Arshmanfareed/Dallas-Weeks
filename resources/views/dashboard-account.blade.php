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
    @if (!empty(auth()->user()->email_verified_at))
        <script src="{{ asset('assets/js/dashboard-account.js') }}"></script>
    @else
        <script src="{{ asset('assets/js/dashboard-account-filter-search.js') }}"></script>
    @endif
    <section class="dashboard">
        <div class="container-fluid">
            @if ($errors->has('error'))
                <div class="alert alert-danger">
                    {{ $errors->first('error') }}
                </div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success text-center">
                    <a href="javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">×</a>
                    <p>{{ Session::get('success') }}</p>
                </div>
            @endif
            <div class="row">
                @include('partials/dashboard_sidebar')
                <div class="col-lg-8">
                    <div class="dashboard_cont">
                        <div class="row_filter d-flex align-items-center justify-content-between">
                            <div class="account d-flex align-items-center">
                                @if (!empty($team))
                                    <img src="{{ asset('assets/img/acc.png') }}" alt="Team Image" class="team-image">
                                    <span>{{ $team->name }}</span>
                                @endif
                            </div>
                            <div class="form_add d-flex">
                                <form action="/search" method="get" class="search-form">
                                    <input type="text" name="q" placeholder="Search..." id="search_seat">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                                @if ($is_executive)
                                    <div class="add_btn"
                                        style="opacity:{{ empty(auth()->user()->email_verified_at) ? 0.7 : 1 }}">
                                        @if (empty(auth()->user()->email_verified_at))
                                            <span style="cursor: pointer;"
                                                title="To add new seats, you need to verify your email address first."><a
                                                    href="javascript:;" type="button"><i
                                                        class="fa-solid fa-plus"></i></a>Add
                                                account</span>
                                        @else
                                            <span style="cursor: pointer;" data-bs-toggle="modal"
                                                data-bs-target="#addaccount"><a href="javascript:;" type="button"><i
                                                        class="fa-solid fa-plus"></i></a>Add account</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row_table">
                            @if ($seats->isNotEmpty())
                                <div class="add_account_div" style="width: 100%">
                                    <div class="campaign_list">
                                        <table class="data_table w-100">
                                            <tbody id="campaign_table_body">
                                                @foreach ($seats as $seat)
                                                    <tr title="{{ empty(auth()->user()->email_verified_at) ? 'Verify your email first to view seat' : '' }}"
                                                        style="opacity:{{ empty(auth()->user()->email_verified_at) ? 0.7 : 1 }};"
                                                        id="{{ 'table_row_' . $seat['id'] }}" class="seat_table_row">
                                                        @if (isset($seat['account_profile']) && $seat['account_profile']['profile_picture_url'] != '')
                                                            <td width="10%" class="seat_table_data"
                                                                style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};">
                                                                <img class="seat_img"
                                                                    src="{{ $seat['account_profile']['profile_picture_url'] }}"
                                                                    alt="">
                                                            </td>
                                                        @else
                                                            <td width="10%" class="seat_table_data"
                                                                style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};">
                                                                <img class="seat_img"
                                                                    src="{{ asset('assets/img/acc.png') }}" alt="">
                                                            </td>
                                                        @endif
                                                        <td width="50%" class="text-left seat_table_data"
                                                            style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};">
                                                            {{ $seat['seat_info']['username'] }}
                                                        </td>
                                                        <td width="15%" class="connection_status">
                                                            @if ($seat['connected'])
                                                                <div class="connected"><span></span>Connected</div>
                                                            @else
                                                                <div class="disconnected"><span></span>Disconnected</div>
                                                            @endif
                                                        </td>
                                                        <td width="15%" class="activeness_status">
                                                            @if ($seat['active'])
                                                                <div class="active"><span></span>Active</div>
                                                            @else
                                                                <div class="not_active"><span></span>In Active</div>
                                                            @endif
                                                        </td>
                                                        <td width="10%">
                                                            <a href="javascript:;" type="button"
                                                                class="setting setting_btn"
                                                                style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};"><i
                                                                    class="fa-solid fa-gear"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @elseif ($is_executive)
                                <div class="add_account_div"
                                    style="opacity:{{ empty(auth()->user()->email_verified_at) ? 0.7 : 1 }}">
                                    <img src="{{ asset('assets/img/empty.png') }}" alt="">
                                    <p class="text-center">You don't have any account yet. Start by adding your first
                                        account.</p>
                                    <div class="add_btn">
                                        @if (empty(auth()->user()->email_verified_at))
                                            <a href="javascript:;" type="button"
                                                title="To add new seats, you need to verify your email address first."><i
                                                    class="fa-solid fa-plus"></i></a>
                                        @else
                                            <a href="javascript:;" type="button" data-bs-toggle="modal"
                                                data-bs-target="#addaccount"><i class="fa-solid fa-plus"></i></a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="add_account_div" style="width: 100%">
                                    <div class="campaign_list">
                                        <table class="data_table w-100">
                                            <tbody id="campaign_table_body">
                                                <tr>
                                                    <td colspan="8">
                                                        <div class="text-center text-danger"
                                                            style="font-size: 25px; font-weight: bold; font-style: italic;">
                                                            Not Found!
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if ($is_executive)
        <div class="modal fade step_form_popup" id="addaccount" tabindex="-1" role="dialog"
            aria-labelledby="addaccount" aria-hidden="true">
            <div class="modal-dialog" style="border-radius: 45px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="text-center">Add Account</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <form role="form" action="{{ route('stripe.post') }}" method="post" data-cc-on-file="false"
                            data-stripe-publishable-key="pk_test_51PbR9cGIhEK4X1bD1csgji86ypCOKzUAWbqzIpVj8TYK1h8yakYAZQeKHkE6fS3qySFp9noqGNRpyps5B1BhAznS00TObcS9Ze"
                            method="post" class="form step_form require-validation" id="payment-form">
                            @csrf
                            <div class="progress-bar" id="progress-bar">
                                <div class="progress" id="progress"></div>
                                <div class="progress-step active" data-title="Add account"></div>
                                <div class="progress-step" data-title="Company "></div>
                                <div class="progress-step" data-title="Payment"></div>
                            </div>
                            <div class="form-step active">
                                <h3>Personal Informations</h3>
                                <div class="form_row row">
                                    <div class="input-group col-12">
                                        <label for="username">User Name</label>
                                        <input type="text" name="username" id="username" placeholder="User Name">
                                    </div>
                                    <div class="input-group col-6">
                                        <label for="City">City</label>
                                        <input type="text" name="city" id="City"
                                            placeholder="Enter your city">
                                    </div>
                                    <div class="input-group col-6">
                                        <label for="State">State</label>
                                        <input type="text" name="state" id="State"
                                            placeholder="Enter your state">
                                    </div>
                                    <div class="input-group col-12">
                                        <label for="Company">Company name</label>
                                        <input type="text" name="company" id="Company"
                                            placeholder="Enter your company name">
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
                                            <input autocomplete='off' class='form-control card-number' size='20'
                                                type='text'>
                                        </div>
                                    </div>
                                    <div class='form-row row'>
                                        <div class='col-xs-12 col-md-4 form-group cvc required'>
                                            <label class='control-label'>CVC</label>
                                            <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311'
                                                size='4' type='text'>
                                        </div>
                                        <div class='col-xs-12 col-md-4 form-group expiration required'>
                                            <label class='control-label'>Expiration Month</label> <input
                                                class='form-control card-expiry-month' placeholder='MM' size='2'
                                                type='text'>
                                        </div>
                                        <div class='col-xs-12 col-md-4 form-group expiration required'>
                                            <label class='control-label'>Expiration Year</label>
                                            <input class='form-control card-expiry-year' placeholder='YYYY'
                                                size='4' type='text'>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-prev">Previous</a>
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Pay Now</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (!empty(auth()->user()->email_verified_at))
        <div class="modal fade step_form_popup" id="update_seat" tabindex="-1" role="dialog"
            aria-labelledby="update_seat" aria-hidden="true">
            <div class="modal-dialog" style="border-radius: 45px;width: 35%;">
                <div class="modal-content"></div>
            </div>
        </div>
    @endif
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    @if (!empty(auth()->user()->email_verified_at))
        <script>
            var getSeatRoute = "{{ route('getSeatById', [':seat_id']) }}";
            var deleteSeatRoute = "{{ route('deleteSeat', [':seat_id']) }}";
            var updateNameRoute = "{{ route('updateName', [':seat_id', ':seat_name']) }}";
            var dashboardRoute = "{{ route('acc_dash') }}";
        </script>
    @endif
    <script>
        var filterSeatRoute = "{{ route('filterSeat', [':search']) }}";
    </script>
@endsection
