@extends('partials/master')
@section('content')
    @if ($is_owner)
        <script src="{{ asset('assets/js/team.js') }}"></script>
    @endif
    <style>
        .disabled {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
    <section class="blacklist team_management">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="filter_head_row d-flex">
                        <div class="cont">
                            <h3>Team Management</h3>
                            @if ($is_owner)
                                <p>Invite team members and manage team permissions.</p>
                            @else
                                <p class="text-danger">You can not invite team members and manage team permissions.</p>
                            @endif
                        </div>
                        <div class="filt_opt d-flex">
                            @if ($is_owner)
                                <div style="cursor: pointer;" class="add_btn " data-bs-toggle="modal"
                                    data-bs-target="#invite_team_modal">
                                    <a href="javascript:;" class="">
                                        <i class="fa-solid fa-plus"></i></a>
                                    Add team member
                                </div>
                            @endif
                            <select name="num" id="num">
                                <option value="01">10</option>
                                <option value="02">20</option>
                                <option value="03">30</option>
                                <option value="04">40</option>
                            </select>
                        </div>
                    </div>
                    <div class="filtr_desc">
                        <div class="d-flex">
                            <strong>Team members</strong>
                            <div class="filter">
                                <form action="/search" method="get" class="search-form">
                                    <input type="text" name="q" placeholder="Search...">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                                <a href="{{ route('rolespermission') }}" class="roles_btn">Roles & permissions</a>
                            </div>
                        </div>
                    </div>
                    <div class="data_row">
                        <div class="data_head">
                            <table class="data_table w-100">
                                <thead>
                                    <tr>
                                        <th width="75%">Name</th>
                                        <th width="10%">Email</th>
                                        <th width="5%">Role</th>
                                        <th width="5%">Status</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($team_members->isNotEmpty())
                                        @foreach ($team_members as $team_member)
                                            @foreach ($team_member->roles as $role)
                                                <tr title="{{ empty(auth()->user()->email_verified_at) ? 'Verify your email first to view seat' : '' }}"
                                                    style="opacity:{{ empty(auth()->user()->email_verified_at) ? 0.7 : 1 }};">
                                                    <td>
                                                        <div class="d-flex align-items-center"><img
                                                                style="background: #000; border-radius: 50%;"
                                                                src="{{ asset('assets/img/acc.png') }}"
                                                                alt=""><strong>{{ $team_member->name }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>{{ $team_member->email }}</td>
                                                    <td>{{ $role['role_name'] }}</td>
                                                    @if (!empty($team_member->email_verified_at))
                                                        <td><a style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};"
                                                                href="javascript:;"
                                                                class="black_list_activate active">Active</a></td>
                                                    @else
                                                        <td><a style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};"
                                                                href="javascript:;"
                                                                class="black_list_activate non_active">InActive</a></td>
                                                    @endif
                                                    <td>
                                                        <a style="cursor: {{ empty(auth()->user()->email_verified_at) ? 'auto' : 'pointer' }};"
                                                            href="javascript:;" type="button" class="setting setting_btn"
                                                            id=""><i class="fa-solid fa-gear"></i></a>
                                                        <ul class="setting_list">
                                                            <li><a href="javascript:;">Edit</a></li>
                                                            <li><a href="javascript:;">Delete</a></li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-center text-danger"
                                                    style="font-size: 25px; font-weight: bold; font-style: italic;">
                                                    Not Found!
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
    </section>
    <script>
        var seats = @json($seats);
    </script>
    @if ($is_owner)
        <div class="modal fade step_form_popup " id="create_new_role" tabindex="-1" aria-labelledby="create_new_role"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="create_new_role">Create a custom role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body" bis_skin_checked="1">
                        <form class="step_form">
                            <label for="role_name">Role name</label>
                            <input type="text" name="role_name" required>
                            <div>
                                @if ($permissions->isNotEmpty())
                                    @foreach ($permissions as $permission)
                                        <div class="row">
                                            <div class="col-lg-6" style="display: flex; width: 390px;">
                                                <input class="permission"
                                                    style="width: 25px; height: 25px; margin-right: 25px;" type="checkbox"
                                                    id="permission_{{ $permission['permission_slug'] }}"
                                                    name="{{ $permission['permission_slug'] }}">
                                                <label
                                                    for="permission_{{ $permission['permission_slug'] }}">{{ $permission['permission_name'] }}</label>
                                            </div>
                                            <div class="col-lg-6" style="display: none; width: 390px;">
                                                @if ($permission->allow_view_only == 1)
                                                    <input type="radio"
                                                        style="width: 25px; height: 25px; margin-right: 25px;"
                                                        id="view_only_{{ $permission['permission_slug'] }}"
                                                        class="view_only"
                                                        name="view_only_{{ $permission['permission_slug'] }}">
                                                    <label for="view_only_{{ $permission['permission_slug'] }}">View
                                                        Only</label>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="btn btn-next">Create Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($is_owner)
        <div class="modal fade create_sequence_modal invite_team_modal" id="invite_team_modal" tabindex="-1"
            aria-labelledby="invite_team_modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sequance_modal">Invite a team member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="model_alert alert alert-danger alert-dismissible fade show" style="display: none;"
                            role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="invite_form">
                            <input type="hidden" name="team_id" value="{{ $team->id }}">
                            <div class="row invite_modal_row">
                                <div class="col-lg-6">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" placeholder="Enter team member's name" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="email">Email</label>
                                    <input type="email" id="invite_email" name="invite_email"
                                        placeholder="Enter team member's email" required>
                                </div>
                                <span>Select one role for your team member</span>
                                <div class="col-lg-6 edit_able disabled">
                                    @if ($roles->isNotEmpty())
                                        @foreach ($roles as $role)
                                            <div class="checkboxes">
                                                <div class="check">
                                                    <input value="{{ 'role_' . $role['id'] }}" name="role"
                                                        type="checkbox">
                                                    <label class="roles"
                                                        for="{{ 'role_' . $role['id'] }}">{{ $role['role_name'] }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-lg-6 add_col edit_able disabled">
                                    <div class="d-flex justify-content-end">
                                        <div style="cursor: pointer;" class="add_btn" data-bs-toggle="modal"
                                            data-bs-target="#create_new_role">
                                            <a href="javascript:;" class="" type="button"><i
                                                    class="fa-solid fa-plus"></i></a>
                                            Create custom role
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 edit_able disabled">
                                    <div class="border_box">
                                        <h6>Manage payment system</h6>
                                        <p>This is a global option that enables access to invoices and adding seats.</p>
                                        <div class="switch_box"><input type="checkbox" name="manage_payment_system"
                                                class="switch" id="switch0"><label for="switch0">Toggle</label></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 edit_able disabled">
                                    <div class="border_box">
                                        <h6>Manage global blacklist</h6>
                                        <p>This is a global option that enables managing the global blacklist on the team
                                            level.
                                        </p>
                                        <div class="switch_box"><input type="checkbox" name="manage_global_blacklist"
                                                class="switch" id="switch1"><label for="switch1">Toggle</label></div>
                                    </div>
                                </div>
                                <button type="submit" class="crt_btn edit_able disabled theme_btn manage_member mt-5">
                                    Invite member <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($is_owner)
        <script>
            var customRoleRoute = "{{ route('customRole') }}";
            var teamMemeberRoute = "{{ route('add_team_member') }}";
        </script>
    @endif
@endsection
