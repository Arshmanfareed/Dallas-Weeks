@extends('partials/master')
@section('content')
    <script src="{{ asset('assets/js/blacklist.js') }}"></script>
    <style>
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

        .blacklist_div {
            margin-top: 45px;
        }

        .blacklist_div .warning_sign {
            display: inline-flex;
            width: 48px;
            height: 48px;
            background-color: #16adcb;
            border-radius: 50%;
            justify-content: center;
            align-items: center;
            margin-right: 8px;
        }

        .blacklist_div .warning_sign i {
            font-size: 25px;
        }

        .blacklist_div .empty_blacklist img {
            width: 30% !important;
            height: 100% !important;
            margin-bottom: 25px;
        }

        .data_row .data_table tbody tr td:nth-child(2):after {
            content: "";
            background: unset;
            width: unset;
            height: unset;
            position: unset;
            top: unset;
            right: unset;
            transform: unset;
        }

        .data_row .data_table thead th {
            padding-bottom: 0;
        }

        .data_table th {
            padding-left: 0;
        }

        .blacklist .filtr_desc .filter {
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .blacklist .filtr_desc .filter .add_to_blacklist {
            background-color: #16adcb;
            border: 0;
            padding: 17px;
            color: #fff;
            border-radius: 7px;
        }

        .add_items_manually_form .input_group {
            border: 1px solid #fff;
            border-radius: 17px;
            padding: 17px;
        }

        .add_items_manually_form .input_group .tag_input_wrapper_input {
            width: 100%;
            height: 158px;
            background-color: transparent !important;
            border: 0 !important;
        }

        .add_items_manually_form .input_group .tag_input_wrapper_input:focus {
            outline: none;
        }

        .add_items_manually_form .input_group #global_blacklist_div,
        .add_items_manually_form .input_group #email_blacklist_div {
            display: flex;
            width: fit-content;
            justify-content: space-between;
        }

        .add_items_manually_form .input_group #global_blacklist_div .item,
        .add_items_manually_form .input_group #email_blacklist_div .item {
            background-color: #16adcb;
            width: fit-content;
            padding: 7px;
            border-radius: 6px;
            margin-right: 7px;
            margin-bottom: 7px;
            cursor: pointer;
        }

        .add_items_manually_form .input_group #global_blacklist_div .item span,
        .add_items_manually_form .input_group #email_blacklist_div .item span {
            margin-right: 10px;
        }

        .state {
            color: #cccccc8c;
            margin: 17px 0;
        }

        .invite_modal_row .disabled {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-center fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                &times;
            </button>
            {{ session('success') }}
        </div>
    @endif
    <section class="blacklist">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="filter_head_row d-flex">
                        <h3>Blacklist</h3>
                        <div class="filt_opt">
                            <select name="num" id="num">
                                <option value="01">10</option>
                                <option value="02">20</option>
                                <option value="03">30</option>
                                <option value="04">40</option>
                            </select>
                        </div>
                    </div>
                    <div class="blacklist_div">
                        <div class="filtr_desc">
                            <div class="d-flex">
                                <div>
                                    <div class="warning_sign"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                    <strong>Blacklist</strong>
                                </div>
                                <div class="filter">
                                    {{-- TODO: Global blacklist filter --}}
                                    <a href="#"><i class="fa-solid fa-filter"></i></a>
                                    <div>
                                        <button class="add_to_blacklist"
                                            data-bs-toggle="{{ $is_verified ? 'modal' : '' }}"
                                            data-bs-target="{{ $is_verified ? '#addGlobalBlacklist' : '' }}"
                                            style="{{ $is_verified ? '' : 'opacity: 0.7; cursor: default;' }}"
                                            title="{{ $is_verified ? '' : 'To add new global blacklist, you need to verify your email address first.' }}">
                                            Add to Blacklist
                                        </button>
                                    </div>
                                    {{-- TODO: Search global blacklist --}}
                                    <div class="search-form">
                                        <input type="text" name="q" placeholder="Search...">
                                        <button type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p>
                                Enter an exact or partial match of a company name, lead's full name, job title, or profile
                                URL you don't wish to target with your campaigns.
                            </p>
                        </div>
                        <div class="data_row">
                            <div class="data_head">
                                <table class="data_table w-100">
                                    <thead>
                                        <tr>
                                            <th width="60%">Keyword</th>
                                            <th class="text-center" width="15%">Blacklist type</th>
                                            <th class="text-center" width="15%">Comparison type</th>
                                            <th class="text-center" width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($global_blacklist->isNotEmpty())
                                            @foreach ($global_blacklist as $blacklist)
                                                <tr id="{{ 'global_blacklist_' . $blacklist->id }}">
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center">
                                                            <strong>{{ $blacklist->keyword }}</strong>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $blacklist->blacklist_type }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $blacklist->comparison_type }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:;" class="delete-global-blacklist"
                                                            data-id="{{ $blacklist->id }}">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">
                                                    <div style="width: 50%; margin: 0 auto; {{ !$is_verified ? ' opacity: 0.7;' : '' }}"
                                                        class="empty_blacklist text-center"
                                                        title="{{ !$is_verified ? 'To add new global blacklist, you need to verify your email address first.' : '' }}">
                                                        <img src="{{ asset('assets/img/empty.png') }}" alt="">
                                                        <p>
                                                            {{ !$is_verified ? "You can't add global blacklist until you verify your email address." : "You don't have any global blacklist yet. Start by adding your first global blacklist." }}
                                                        </p>
                                                        <div class="add_btn">
                                                            <a href="javascript:;" type="button"
                                                                data-bs-toggle="{{ $is_verified ? 'modal' : '' }}"
                                                                data-bs-target="{{ $is_verified ? '#addGlobalBlacklist' : '' }}"
                                                                style="{{ !$is_verified ? 'cursor: default;' : '' }}">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="blacklist_div">
                        <div class="filtr_desc">
                            <div class="d-flex">
                                <div>
                                    <div class="warning_sign"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                    <strong>Email & domain blacklist</strong>
                                </div>
                                <div class="filter">
                                    {{-- TODO: Email blacklist filter --}}
                                    <a href="#"><i class="fa-solid fa-filter"></i></a>
                                    <div>
                                        <button class="add_to_blacklist"
                                            data-bs-toggle="{{ $is_verified ? 'modal' : '' }}"
                                            data-bs-target="{{ $is_verified ? '#addEmailBlacklist' : '' }}"
                                            style="{{ $is_verified ? '' : 'opacity: 0.7; cursor: default;' }}"
                                            title="{{ $is_verified ? '' : 'To add new email blacklist, you need to verify your email address first.' }}">
                                            Add to Blacklist
                                        </button>
                                    </div>
                                    {{-- TODO: Search email blacklist --}}
                                    <div class="search-form">
                                        <input type="text" name="q" placeholder="Search...">
                                        <button type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p>
                                Enter an exact or partial match of the company domain or a lead's email you don't wish to
                                target with your campaigns.
                            </p>
                        </div>
                        <div class="data_row">
                            <div class="data_head">
                                <table class="data_table w-100">
                                    <thead>
                                        <tr>
                                            <th width="60%">Keyword</th>
                                            <th class="text-center" width="15%">Blacklist type</th>
                                            <th class="text-center" width="15%">Comparison type</th>
                                            <th class="text-center" width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($email_blaklist->isNotEmpty())
                                            @foreach ($email_blaklist as $blacklist)
                                                <tr id="{{ 'email_blaklist_' . $blacklist->id }}">
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center">
                                                            <strong>{{ $blacklist->keyword }}</strong>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $blacklist->blacklist_type }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $blacklist->comparison_type }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:;" class="delete-email-blacklist"
                                                            data-id="{{ $blacklist->id }}">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">
                                                    <div style="width: 50%; margin: 0 auto; {{ !$is_verified ? 'opacity: 0.7;' : '' }}"
                                                        class="empty_blacklist text-center"
                                                        title="{{ !$is_verified ? 'To add new global blacklist, you need to verify your email address first.' : '' }}">
                                                        <img src="{{ asset('assets/img/empty.png') }}" alt="">
                                                        <p>
                                                            {{ !$is_verified ? "You can't add email blacklist until you verify your email address." : "You don't have any email blacklist yet. Start by adding your first email blacklist." }}
                                                        </p>
                                                        <div class="add_btn">
                                                            <a href="javascript:;" type="button"
                                                                style="{{ !$is_verified ? 'cursor: default;' : '' }}"
                                                                data-bs-toggle="{{ $is_verified ? 'modal' : '' }}"
                                                                data-bs-target="{{ $is_verified ? '#addEmailBlacklist' : '' }}">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if ($is_verified)
        <div class="modal fade step_form_popup" id="addGlobalBlacklist" tabindex="-1" role="dialog"
            aria-labelledby="addGlobalBlacklist" aria-hidden="true">
            <div class="modal-dialog" style="border-radius: 45px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="text-center">Add Items Manually</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->first())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <a class="close" data-dismiss="alert" aria-label="Close">&times;</a>
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form action="{{ route('saveGlobalBlacklist', ['slug' => $team->slug]) }}" method="post"
                            class="form step_form add_items_manually_form">
                            @csrf
                            <span style="width: 100%;">
                                <div>
                                    <div>
                                        <div class="input_group">
                                            <div id="global_blacklist_div">
                                                @if (old('global_blacklist_item'))
                                                    @foreach (old('global_blacklist_item') as $item)
                                                        <div class="item">
                                                            <span>{{ $item }}</span>
                                                            <span class="remove_global_blacklist_item">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </span>
                                                            <input type="hidden" name="global_blacklist_item[]"
                                                                value="{{ $item }}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <textarea class="tag_input_wrapper_input" data-div-id="global_blacklist_div" rows="1"
                                                placeholder="Add an item to the blacklist..."></textarea>
                                        </div>
                                    </div>
                                    <div class="state">To separate each item you wish to blacklist, use the ; symbol</div>
                                </div>
                                <div class="row invite_modal_row">
                                    <div class="col-lg-6">
                                        <div>Choose blacklist type:</div>
                                        <div class="mt-3">
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="global_blacklist_type"
                                                        value="company_name"
                                                        {{ old('global_blacklist_type') == 'company_name' ? 'checked' : '' }}>
                                                    <label class="global_blacklist_type" for="company_name">Company
                                                        name</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="global_blacklist_type"
                                                        value="lead_full_name"
                                                        {{ old('global_blacklist_type') == 'lead_full_name' ? 'checked' : '' }}>
                                                    <label class="global_blacklist_type" for="lead_full_name">Lead's full
                                                        name</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="global_blacklist_type"
                                                        value="profile_url"
                                                        {{ old('global_blacklist_type') == 'profile_url' ? 'checked' : '' }}>
                                                    <label class="global_blacklist_type" for="profile_url">Profile
                                                        URL</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="global_blacklist_type" value="job_title"
                                                        {{ old('global_blacklist_type') == 'job_title' ? 'checked' : '' }}>
                                                    <label class="global_blacklist_type" for="job_title">Job Title</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div>Choose comparison type:</div>
                                        <div class="mt-3">
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="global_comparison_type" value="exact"
                                                        {{ old('global_comparison_type') == 'exact' ? 'checked' : '' }}>
                                                    <label class="global_comparison_type" for="exact">Exact</label>
                                                </div>
                                            </div>
                                            <div
                                                class="checkboxes {{ old('global_blacklist_type') == 'profile_url' ? 'disabled' : '' }}">
                                                <div class="check">
                                                    <input type="checkbox" name="global_comparison_type" value="contains"
                                                        {{ old('global_comparison_type') == 'contains' ? 'checked' : '' }}>
                                                    <label class="global_comparison_type" for="contains">Contains</label>
                                                </div>
                                            </div>
                                            <div
                                                class="checkboxes {{ old('global_blacklist_type') == 'profile_url' ? 'disabled' : '' }}">
                                                <div class="check">
                                                    <input type="checkbox" name="global_comparison_type"
                                                        value="starts_with"
                                                        {{ old('global_comparison_type') == 'starts_with' ? 'checked' : '' }}>
                                                    <label class="global_comparison_type" for="starts_with">Starts
                                                        with</label>
                                                </div>
                                            </div>
                                            <div
                                                class="checkboxes {{ old('global_blacklist_type') == 'profile_url' ? 'disabled' : '' }}">
                                                <div class="check">
                                                    <input type="checkbox" name="global_comparison_type"
                                                        value="ends_with"
                                                        {{ old('global_comparison_type') == 'ends_with' ? 'checked' : '' }}>
                                                    <label class="global_comparison_type" for="ends_with">Ends
                                                        with</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="crt_btn edit_able theme_btn manage_member mt-5">
                                        Add to blacklist
                                    </button>
                                </div>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($is_verified)
        <div class="modal fade step_form_popup" id="addEmailBlacklist" tabindex="-1" role="dialog"
            aria-labelledby="addEmailBlacklist" aria-hidden="true">
            <div class="modal-dialog" style="border-radius: 45px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="text-center">Add Items Manually</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->first())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <a class="close" data-dismiss="alert" aria-label="Close">&times;</a>
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form action="{{ route('saveEmailBlacklist', ['slug' => $team->slug]) }}" method="post"
                            class="form step_form add_items_manually_form">
                            @csrf
                            <span style="width: 100%;">
                                <div>
                                    <div>
                                        <div class="input_group">
                                            <div id="email_blacklist_div">
                                                @if (old('email_blacklist_item'))
                                                    @foreach (old('email_blacklist_item') as $item)
                                                        <div class="item">
                                                            <span>{{ $item }}</span>
                                                            <span class="remove_email_blacklist_item">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </span>
                                                            <input type="hidden" name="email_blacklist_item[]"
                                                                value="{{ $item }}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <textarea class="tag_input_wrapper_input" data-div-id="email_blacklist_div" rows="1"
                                                placeholder="Add an item to the blacklist..."></textarea>
                                        </div>
                                    </div>
                                    <div class="state">To separate each item you wish to blacklist, use the ; symbol</div>
                                </div>
                                <div class="row invite_modal_row">
                                    <div class="col-lg-6">
                                        <div>Choose blacklist type:</div>
                                        <div class="mt-3">
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="email_blacklist_type" value="lead_email"
                                                        {{ old('email_blacklist_type') == 'lead_email' ? 'checked' : '' }}>
                                                    <label class="email_blacklist_type" for="lead_email">Lead's
                                                        email</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="email_blacklist_type"
                                                        value="company_domain"
                                                        {{ old('email_blacklist_type') == 'company_domain' ? 'checked' : '' }}>
                                                    <label class="email_blacklist_type" for="company_domain">Company
                                                        domain</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div>Choose comparison type:</div>
                                        <div class="mt-3">
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="email_comparison_type" value="exact"
                                                        {{ old('email_comparison_type') == 'exact' ? 'checked' : '' }}>
                                                    <label class="email_comparison_type" for="exact">Exact</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="email_comparison_type" value="contains"
                                                        {{ old('email_comparison_type') == 'contains' ? 'checked' : '' }}>
                                                    <label class="email_comparison_type" for="contains">Contains</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="email_comparison_type"
                                                        value="starts_with"
                                                        {{ old('email_comparison_type') == 'starts_with' ? 'checked' : '' }}>
                                                    <label class="email_comparison_type" for="starts_with">Starts
                                                        with</label>
                                                </div>
                                            </div>
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input type="checkbox" name="email_comparison_type" value="ends_with"
                                                        {{ old('email_comparison_type') == 'ends_with' ? 'checked' : '' }}>
                                                    <label class="email_comparison_type" for="ends_with">Ends with</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="crt_btn edit_able theme_btn manage_member mt-5">
                                        Add to blacklist
                                    </button>
                                </div>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        var deleteGlobalBlacklistRoute =
            "{{ route('deleteGlobalBlacklist', ['slug' => $team->slug, 'id' => ':blacklist-id']) }}";
        var deleteEmailBlacklistRoute =
            "{{ route('deleteEmailBlacklist', ['slug' => $team->slug, 'id' => ':blacklist-id']) }}";
    </script>
    <script>
        $(document).ready(function() {
            if ("{{ session()->has('global_blacklist_error') }}") {
                $('#addGlobalBlacklist').modal('show');
            }
            if ("{{ session()->has('email_blacklist_error') }}") {
                $('#addEmailBlacklist').modal('show');
            }
            $(".setting_list").hide();
            $(".setting_btn").on("click", function(e) {
                $(".setting_list").not($(this).siblings(".setting_list")).hide();
                $(this).siblings(".setting_list").toggle();
            });
            $(document).on("click", function(e) {
                if (!$(event.target).closest(".setting").length) {
                    $(".setting_list").hide();
                }
            });
        });
    </script>
@endsection
